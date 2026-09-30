<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminPaymentIndexRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Loan;
use App\Models\Payment;
use App\Services\PaymentLedgerService;
use App\Services\PayMongoService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentLedgerService $ledger) {}

    /*
    |----------------------------------------------------------------------
    | USER PAYMENT PAGE
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $user = auth()->user();

        $loans = Loan::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with(['loanType', 'paymentSchedules'])
            ->latest()
            ->get();

        $payments = Payment::where('user_id', $user->id)
            ->with('loan.loanType')
            ->orderByRaw('COALESCE(paid_at, created_at) DESC')
            ->orderByDesc('id')
            ->get();

        return view('payments.index', compact('loans', 'payments'));
    }

    /*
    |----------------------------------------------------------------------
    | STORE PAYMENT (FIXED)
    |----------------------------------------------------------------------
    */
    public function store(StorePaymentRequest $request, PayMongoService $payMongo): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $loan = Loan::findOrFail($validated['loan_id']);

        if ($loan->user_id !== auth()->id() || $loan->status !== Loan::STATUS_APPROVED) {
            abort(403);
        }

        $remainingBalance = max(0, (float) $loan->getRemainingBalance());

        $amount = round((float) $validated['amount'], 2);

        if ($amount > $remainingBalance) {
            throw ValidationException::withMessages([
                'amount' => 'The payment cannot be greater than the remaining loan balance.',
            ]);
        }

        $proofPath = null;

        if ($validated['method'] === 'cash' && $request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payments', 'public');
        }

        $payment = Payment::create([
            'loan_id' => $loan->id,
            'user_id' => auth()->id(),
            'amount' => $amount,
            'reference' => 'LOANPAY-'.str()->upper(Str::random(12)),
            'currency' => 'PHP',
            'method' => $validated['method'],
            'proof' => $proofPath,
            'status' => Payment::STATUS_PENDING,
        ]);

        if ($validated['method'] !== 'cash') {
            try {
                $checkoutUrl = $payMongo->createCheckoutSession($payment);

                if ($request->expectsJson()) {
                    return response()->json([
                        'payment_id' => $payment->id,
                        'checkout_url' => $checkoutUrl,
                        'status_url' => route('payments.status', $payment),
                        'cancel_url' => route('payments.cancel', $payment),
                    ]);
                }

                return redirect()->away($checkoutUrl);
            } catch (\Throwable $exception) {
                $payment->delete();
                Log::error('Unable to create PayMongo checkout session.', [
                    'payment_id' => $payment->id,
                    'error' => $exception->getMessage(),
                ]);

                throw ValidationException::withMessages([
                    'method' => 'Online payment is temporarily unavailable. Please try again later.',
                ]);
            }
        }

        return redirect()->route('payments.index')
            ->with('success', 'Cash payment submitted successfully. Awaiting approval.');
    }

    public function success(int $payment, PayMongoService $payMongo): Response
    {
        $paymentModel = $this->findUserPayment($payment);

        if ($paymentModel->status === Payment::STATUS_PENDING
            && $paymentModel->paymongo_session_id) {
            try {
                $session = $payMongo->retrieveCheckoutSession($paymentModel->paymongo_session_id);
                $this->reconcileCheckoutSession($paymentModel, $session);
                $paymentModel->refresh();
            } catch (\Throwable $exception) {
                // The signed webhook remains the source of truth if this check
                // is unavailable or PayMongo has not settled the session yet.
                Log::notice('PayMongo return-time reconciliation is pending.', [
                    'payment_id' => $paymentModel->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return response()->view('payments.success', ['payment' => $paymentModel]);
    }

    public function cancel(int $payment): Response
    {
        $paymentModel = $this->findUserPayment($payment);

        if ($paymentModel->method !== 'cash' && $paymentModel->status === Payment::STATUS_PENDING) {
            $paymentModel->update(['status' => Payment::STATUS_REJECTED]);
        }

        return response()->view('payments.cancel', ['payment' => $paymentModel]);
    }

    public function status(int $payment, PayMongoService $payMongo): JsonResponse
    {
        $paymentModel = $this->findUserPayment($payment);

        if ($paymentModel->status === Payment::STATUS_PENDING
            && $paymentModel->paymongo_session_id) {
            try {
                $session = $payMongo->retrieveCheckoutSession($paymentModel->paymongo_session_id);
                $this->reconcileCheckoutSession($paymentModel, $session);
                $paymentModel->refresh();
            } catch (\Throwable $exception) {
                Log::notice('PayMongo completion polling is pending.', [
                    'payment_id' => $paymentModel->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $redirectUrl = match ($paymentModel->status) {
            Payment::STATUS_APPROVED => route('payments.success', $paymentModel),
            Payment::STATUS_REJECTED => route('payments.cancel', $paymentModel),
            default => null,
        };

        return response()->json([
            'status' => $paymentModel->status,
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function webhook(Request $request, PayMongoService $payMongo): JsonResponse
    {
        $payload = $request->getContent();

        $signature = $request->header('Paymongo-Signature')
            ?: $request->header('X-Paymongo-Signature');

        if (! $payMongo->isValidWebhook($payload, $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $event = json_decode($payload, true);

        if (! is_array($event)) {
            return response()->json(['message' => 'Invalid payload.'], 400);
        }

        $eventId = data_get($event, 'data.id') ?: data_get($event, 'id') ?: hash('sha256', $payload);
        $envelopeType = data_get($event, 'data.type');
        $eventType = $envelopeType && $envelopeType !== 'event'
            ? $envelopeType
            : data_get($event, 'data.attributes.type', '');
        $resource = data_get($event, 'data.data') ?: data_get($event, 'data.attributes.data', []);

        DB::transaction(function () use ($eventId, $eventType, $event, $resource) {
            $created = DB::table('paymongo_webhook_events')->insertOrIgnore([
                'event_id' => $eventId,
                'event_type' => $eventType,
                'livemode' => (int) (data_get($event, 'data.attributes.livemode') ?? data_get($event, 'data.livemode', false)),
                'processed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (! $created) {
                return;
            }

            $attributes = data_get($resource, 'attributes', []);
            $resourceType = data_get($resource, 'type');
            $resourceId = data_get($resource, 'id');
            $checkoutSessionId = $resourceType === 'checkout_session' ? $resourceId : null;
            $checkoutReference = data_get($attributes, 'reference_number');
            $paymongoPayment = data_get($attributes, 'payments.0');
            $paymongoPaymentId = data_get($paymongoPayment, 'id')
                ?: ($resourceType === 'payment' ? $resourceId : null);
            $paymentAttributes = data_get($paymongoPayment, 'attributes', []) ?: ($resourceType === 'payment' ? $attributes : []);
            $reference = $checkoutReference ?: data_get($paymentAttributes, 'external_reference_number');

            $payment = null;

            if ($reference || $checkoutSessionId || $paymongoPaymentId) {
                $payment = Payment::where(function ($query) use ($reference, $checkoutSessionId, $paymongoPaymentId) {
                    $query->when($reference, fn ($query) => $query->where('reference', $reference));
                    $query->when($checkoutSessionId, fn ($query) => $query->orWhere('paymongo_session_id', $checkoutSessionId));
                    $query->when($paymongoPaymentId, fn ($query) => $query->orWhere('paymongo_payment_id', $paymongoPaymentId));
                })->lockForUpdate()->first();
            }

            if (! $payment) {
                Log::warning('PayMongo webhook did not match a local payment.', ['event_id' => $eventId, 'event_type' => $eventType]);

                return;
            }

            $amount = data_get($paymentAttributes, 'amount');
            $currency = data_get($paymentAttributes, 'currency');

            if (in_array($eventType, ['checkout_session.payment.paid', 'payment.paid'], true)
                && (! $paymongoPaymentId || $amount === null || ! $currency
                    || data_get($paymentAttributes, 'status') !== 'paid')) {
                Log::warning('PayMongo paid webhook was missing verified payment details.', [
                    'event_id' => $eventId,
                    'payment_id' => $payment->id,
                ]);

                return;
            }

            if ($amount !== null && ((int) $amount !== (int) round((float) $payment->amount * 100)
                || strtoupper((string) $currency) !== strtoupper($payment->currency))) {
                Log::warning('PayMongo webhook amount mismatch.', ['event_id' => $eventId, 'payment_id' => $payment->id]);

                return;
            }

            if ($eventType === 'checkout_session.payment.paid' || $eventType === 'payment.paid') {
                $this->ledger->approve($payment, $checkoutSessionId, $paymongoPaymentId, data_get($paymentAttributes, 'source.type'));
            } elseif (in_array($eventType, ['payment.failed', 'checkout_session.payment.failed'], true)
                && $payment->status === Payment::STATUS_PENDING) {
                $payment->update(['status' => Payment::STATUS_REJECTED, 'paymongo_payment_id' => $paymongoPaymentId]);
            } elseif (in_array($eventType, ['refund.succeeded', 'payment.refunded'], true)
                && $payment->status === Payment::STATUS_APPROVED) {
                $payment->update(['status' => Payment::STATUS_REJECTED]);
                $this->ledger->recalculate($payment->loan()->lockForUpdate()->firstOrFail());
            }
        });

        return response()->json(['received' => true]);
    }

    /*
    |----------------------------------------------------------------------
    | ADMIN PAYMENTS LIST
    |----------------------------------------------------------------------
    */
    public function adminIndex(AdminPaymentIndexRequest $request)
    {
        $validated = $request->validated();

        $search = trim($validated['q'] ?? '');
        $status = $validated['status'] ?? null;
        $method = $validated['method'] ?? null;
        $dateFrom = isset($validated['date_from'])
            ? Carbon::createFromFormat('Y-m-d', $validated['date_from'], 'Asia/Manila')->startOfDay()->utc()
            : null;
        $dateTo = isset($validated['date_to'])
            ? Carbon::createFromFormat('Y-m-d', $validated['date_to'], 'Asia/Manila')->endOfDay()->utc()
            : null;
        $methodValues = match ($method) {
            'gcash' => ['gcash', 'qrph'],
            'bank_transfer' => ['bank_transfer', 'dob', 'brankas'],
            null => [],
            default => [$method],
        };

        $payments = Payment::query()
            ->with(['loan.user', 'loan.loanType'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhere('provider_reference', 'like', "%{$search}%")
                        ->orWhere('paymongo_payment_id', 'like', "%{$search}%")
                        ->orWhereHas('loan', function ($loan) use ($search) {
                            $loan->where('loan_code', 'like', "%{$search}%")
                                ->orWhereHas('user', function ($user) use ($search) {
                                    $user->where('name', 'like', "%{$search}%")
                                        ->orWhere('first_name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%")
                                        ->orWhere('contact_number', 'like', "%{$search}%");
                                });
                        });

                    if (ctype_digit($search)) {
                        $query->orWhere('payments.id', (int) $search);
                    }
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($methodValues, fn ($query) => $query->whereIn('method', $methodValues))
            ->when($dateFrom, fn ($query) => $query->whereRaw('COALESCE(paid_at, created_at) >= ?', [$dateFrom]))
            ->when($dateTo, fn ($query) => $query->whereRaw('COALESCE(paid_at, created_at) <= ?', [$dateTo]))
            ->orderByRaw("CASE WHEN status = 'pending' AND method = 'cash' THEN 0 WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderByRaw('COALESCE(paid_at, created_at) DESC')
            ->orderByDesc('id')
            ->paginate($validated['per_page'] ?? 25)
            ->withQueryString();

        $stats = [
            'transactions' => Payment::count(),
            'collected' => (float) Payment::where('status', Payment::STATUS_APPROVED)->sum('amount'),
            'pending_cash' => Payment::where('status', Payment::STATUS_PENDING)->where('method', 'cash')->count(),
            'clients' => Payment::query()
                ->join('loans', 'payments.loan_id', '=', 'loans.id')
                ->distinct('loans.user_id')
                ->count('loans.user_id'),
        ];

        $clientSummaries = Payment::query()
            ->join('loans', 'payments.loan_id', '=', 'loans.id')
            ->selectRaw('loans.user_id, COUNT(payments.id) as payment_count')
            ->selectRaw("SUM(CASE WHEN payments.status = 'approved' THEN payments.amount ELSE 0 END) as collected")
            ->groupBy('loans.user_id')
            ->get()
            ->keyBy('user_id');

        $filters = [
            'q' => $search,
            'status' => $status,
            'method' => $method,
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'per_page' => $validated['per_page'] ?? 25,
        ];

        return view('admin.payments.index', compact('payments', 'stats', 'clientSummaries', 'filters'));
    }

    /*
    |----------------------------------------------------------------------
    | SHOW PAYMENT
    |----------------------------------------------------------------------
    */
    public function adminShow($id)
    {
        $payment = Payment::with(['loan.user', 'loan.loanType'])
            ->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    /*
    |----------------------------------------------------------------------
    | APPROVE PAYMENT (FIXED)
    |----------------------------------------------------------------------
    */
    public function approve($id): RedirectResponse
    {
        $payment = Payment::with('loan')->findOrFail($id);

        if ($payment->method !== 'cash') {
            return back()->with('error', 'Online payments are confirmed automatically by PayMongo and cannot be approved manually.');
        }

        if ($payment->status === Payment::STATUS_APPROVED) {
            return back()->with('success', 'Payment already approved.');
        }

        if ($payment->status !== Payment::STATUS_PENDING) {
            return back()->with('error', 'Only pending cash payments can be approved.');
        }

        $this->ledger->approve($payment);

        return back()->with('success', 'Payment approved successfully!');
    }

    /*
    |----------------------------------------------------------------------
    | REJECT PAYMENT
    |----------------------------------------------------------------------
    */
    public function reject($id): RedirectResponse
    {
        $payment = Payment::findOrFail($id);

        if ($payment->method !== 'cash' || $payment->status !== Payment::STATUS_PENDING) {
            return back()->with('error', 'Only pending cash payments can be rejected manually.');
        }

        $payment->update(['status' => Payment::STATUS_REJECTED]);

        return back()->with('success', 'Payment rejected!');
    }

    private function reconcileCheckoutSession(Payment $payment, array $session): void
    {
        $sessionId = data_get($session, 'id');
        $reference = data_get($session, 'attributes.reference_number');

        if ($sessionId !== $payment->paymongo_session_id || $reference !== $payment->reference) {
            return;
        }

        $providerPayment = collect(data_get($session, 'attributes.payments', []))
            ->first(fn (array $item) => data_get($item, 'attributes.status') === 'paid');

        if (! $providerPayment) {
            $intentStatus = data_get($session, 'attributes.payment_intent.attributes.status');
            $lastPaymentError = data_get($session, 'attributes.payment_intent.attributes.last_payment_error');

            if ($intentStatus === 'awaiting_payment_method'
                && $lastPaymentError
                && $payment->status === Payment::STATUS_PENDING) {
                $payment->update(['status' => Payment::STATUS_REJECTED]);
            }

            return;
        }

        $amount = (int) data_get($providerPayment, 'attributes.amount', -1);
        $currency = strtoupper((string) data_get($providerPayment, 'attributes.currency'));

        if ($amount !== (int) round((float) $payment->amount * 100)
            || $currency !== strtoupper($payment->currency)) {
            Log::warning('PayMongo checkout reconciliation amount mismatch.', ['payment_id' => $payment->id]);

            return;
        }

        $this->ledger->approve(
            $payment,
            $sessionId,
            data_get($providerPayment, 'id'),
            data_get($providerPayment, 'attributes.source.type')
        );
    }

    private function findUserPayment(int $payment): Payment
    {
        return Payment::whereKey($payment)
            ->whereHas('loan', fn ($query) => $query->where('user_id', auth()->id()))
            ->firstOrFail();
    }
}
