<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use App\Services\PayMongoService;
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
            ->with('loanType')
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
    public function store(Request $request, PayMongoService $payMongo): RedirectResponse
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric|min:0.01|max:100000000',
            'method' => 'required|in:gcash,paymaya,cash',
            'proof' => 'required_if:method,cash|nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validated['method'] !== 'cash' && (float) $validated['amount'] > 100000) {
            throw ValidationException::withMessages([
                'amount' => 'GCash and Maya payments cannot exceed PHP 100,000 per transaction.',
            ]);
        }

        if ($validated['method'] !== 'cash' && (float) $validated['amount'] < 1) {
            throw ValidationException::withMessages([
                'amount' => 'Online payments must be at least PHP 1.00.',
            ]);
        }

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
            'status' => 'pending',
        ]);

        if ($validated['method'] !== 'cash') {
            try {
                return redirect()->away($payMongo->createCheckoutSession($payment));
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

        if (! in_array($paymentModel->status, ['paid', 'approved', 'refunded'], true)
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
        $this->findUserPayment($payment);

        return response()->view('payments.cancel');
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
                $this->markAsPaid($payment, $checkoutSessionId, $paymongoPaymentId, data_get($paymentAttributes, 'source.type'));
            } elseif (in_array($eventType, ['payment.failed', 'checkout_session.payment.failed'], true)
                && ! in_array($payment->status, ['paid', 'approved', 'refunded'], true)) {
                $payment->update(['status' => 'failed', 'paymongo_payment_id' => $paymongoPaymentId]);
            } elseif (in_array($eventType, ['refund.succeeded', 'payment.refunded'], true)
                && in_array($payment->status, ['paid', 'approved'], true)) {
                $payment->update(['status' => 'refunded']);
                $this->recalculateLoan($payment->loan()->lockForUpdate()->first());
            }
        });

        return response()->json(['received' => true]);
    }

    /*
    |----------------------------------------------------------------------
    | ADMIN PAYMENTS LIST
    |----------------------------------------------------------------------
    */
    public function adminIndex()
    {
        $payments = Payment::with(['loan.user', 'loan.loanType'])
            ->orderByRaw('COALESCE(paid_at, created_at) DESC')
            ->orderByDesc('id')
            ->get();

        return view('admin.payments.index', compact('payments'));
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

        if (in_array($payment->status, ['paid', 'approved'], true)) {
            return back()->with('success', 'Payment already approved.');
        }

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending cash payments can be approved.');
        }

        $this->markAsPaid($payment);

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

        if ($payment->method !== 'cash' || $payment->status !== 'pending') {
            return back()->with('error', 'Only pending cash payments can be rejected manually.');
        }

        $payment->update(['status' => 'rejected']);

        return back()->with('success', 'Payment rejected!');
    }

    private function markAsPaid(Payment $payment, ?string $sessionId = null, ?string $paymentId = null, ?string $method = null): void
    {
        DB::transaction(function () use ($payment, $sessionId, $paymentId, $method) {
            $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if (in_array($payment->status, ['paid', 'approved', 'refunded'], true)) {
                return;
            }

            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'paymongo_session_id' => $sessionId ?: $payment->paymongo_session_id,
                'paymongo_payment_id' => $paymentId ?: $payment->paymongo_payment_id,
                'method' => $method ?: $payment->method,
            ]);

            $this->recalculateLoan($payment->loan()->lockForUpdate()->first());
        });
    }

    private function recalculateLoan(Loan $loan): void
    {
        $successfulPayments = Payment::where('loan_id', $loan->id)
            ->whereIn('status', ['paid', 'approved'])
            ->get(['amount']);
        $totalPaid = $successfulPayments->sum('amount');
        $totalDue = (float) $loan->getTotalWithPenalty();

        $loan->update([
            'paid_amount' => $totalPaid,
            'payment_count' => $successfulPayments->count(),
            'status' => $totalPaid >= $totalDue
                ? Loan::STATUS_PAID
                : ($loan->status === Loan::STATUS_PAID ? Loan::STATUS_APPROVED : $loan->status),
        ]);
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
            return;
        }

        $amount = (int) data_get($providerPayment, 'attributes.amount', -1);
        $currency = strtoupper((string) data_get($providerPayment, 'attributes.currency'));

        if ($amount !== (int) round((float) $payment->amount * 100)
            || $currency !== strtoupper($payment->currency)) {
            Log::warning('PayMongo checkout reconciliation amount mismatch.', ['payment_id' => $payment->id]);

            return;
        }

        $this->markAsPaid(
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
