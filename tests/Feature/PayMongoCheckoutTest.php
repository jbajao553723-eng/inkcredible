<?php

use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

function approvedLoanForPayMongoTest(User $user, float $total = 1500): Loan
{
    return Loan::create([
        'user_id' => $user->id,
        'amount' => $total,
        'total_payable' => $total,
        'paid_amount' => 0,
        'status' => Loan::STATUS_APPROVED,
    ]);
}

it('shows all payment history newest first with Philippine date and time', function () {
    $user = User::factory()->create();
    $activeLoan = approvedLoanForPayMongoTest($user);
    $completedLoan = approvedLoanForPayMongoTest($user);
    $completedLoan->update(['status' => Loan::STATUS_PAID]);

    Payment::query()->forceCreate([
        'loan_id' => $activeLoan->id,
        'user_id' => $user->id,
        'amount' => 100,
        'reference' => 'LOANPAY-OLDER',
        'currency' => 'PHP',
        'method' => 'cash',
        'status' => 'pending',
        'created_at' => Carbon::parse('2026-09-14 09:00:00', 'UTC'),
    ]);

    Payment::query()->forceCreate([
        'loan_id' => $completedLoan->id,
        'user_id' => $user->id,
        'amount' => 300,
        'reference' => 'LOANPAY-NEWEST',
        'currency' => 'PHP',
        'method' => 'gcash',
        'status' => 'paid',
        'paid_at' => Carbon::parse('2026-09-14 12:30:00', 'UTC'),
        'created_at' => Carbon::parse('2026-09-14 08:00:00', 'UTC'),
    ]);

    $this->actingAs($user)
        ->get(route('payments.index'))
        ->assertOk()
        ->assertSeeInOrder(['LOANPAY-NEWEST', 'Sep 14, 2026', '08:30 PM', 'LOANPAY-OLDER'])
        ->assertSee('Payments &amp; billing', false)
        ->assertSee('GCash / QR Ph');
});

it('redirects GCash to a method-specific PayMongo checkout', function () {
    config()->set('paymongo.secret_key', 'sk_test_example');
    config()->set('paymongo.gcash_checkout_method', 'qrph');
    Http::fake([
        'api.paymongo.com/v2/checkout_sessions' => Http::response([
            'data' => [
                'id' => 'cs_test_123',
                'attributes' => ['checkout_url' => 'https://checkout.paymongo.com/cs_test_123'],
            ],
        ]),
    ]);

    $user = User::factory()->create();
    $loan = approvedLoanForPayMongoTest($user);

    $this->actingAs($user)->post(route('payments.store'), [
        'loan_id' => $loan->id,
        'amount' => '500.25',
        'method' => 'gcash',
    ])->assertRedirect('https://checkout.paymongo.com/cs_test_123');

    $payment = Payment::sole();
    expect($payment->status)->toBe('pending')
        ->and($payment->paymongo_session_id)->toBe('cs_test_123');

    Http::assertSent(fn ($request) => $request->url() === 'https://api.paymongo.com/v2/checkout_sessions'
        && $request['data']['attributes']['payment_method_types'] === ['qrph']
        && $request['data']['attributes']['line_items'][0]['amount'] === 50025);
});

it('credits a signed PayMongo payment and updates the dashboard once', function () {
    config()->set('paymongo.webhook_secret', 'whsk_test_example');
    config()->set('paymongo.webhook_tolerance', 300);

    $user = User::factory()->create();
    $loan = approvedLoanForPayMongoTest($user);
    $payment = Payment::create([
        'loan_id' => $loan->id,
        'user_id' => $user->id,
        'amount' => 400,
        'reference' => 'LOANPAY-WEBHOOK-1',
        'currency' => 'PHP',
        'method' => 'gcash',
        'provider' => 'paymongo',
        'provider_reference' => 'cs_test_webhook',
        'paymongo_session_id' => 'cs_test_webhook',
        'status' => 'pending',
    ]);

    $payload = json_encode([
        'event_type' => 'send.webhook',
        'data' => [
            'type' => 'checkout_session.payment.paid',
            'livemode' => false,
            'data' => [
                'id' => 'cs_test_webhook',
                'type' => 'checkout_session',
                'attributes' => [
                    'reference_number' => $payment->reference,
                    'payments' => [[
                        'id' => 'pay_test_webhook',
                        'attributes' => [
                            'amount' => 40000,
                            'currency' => 'PHP',
                            'status' => 'paid',
                            'source' => ['type' => 'gcash'],
                        ],
                    ]],
                ],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES);
    $timestamp = (string) now()->timestamp;
    $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsk_test_example');
    $server = [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_PAYMONGO_SIGNATURE' => "t={$timestamp},te={$signature},li=",
    ];

    $this->call('POST', route('payments.webhook'), [], [], [], $server, $payload)->assertOk();
    $this->call('POST', route('payments.webhook'), [], [], [], $server, $payload)->assertOk();

    expect($payment->fresh()->status)->toBe('paid')
        ->and($loan->fresh()->paid_amount)->toEqual('400.00')
        ->and($loan->fresh()->payment_count)->toBe(1)
        ->and($loan->fresh()->getRemainingBalance())->toEqual(1100.0);

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('₱1,100.00')
        ->assertSee('Paid');
});

it('reconciles a paid checkout when PayMongo returns the borrower', function () {
    config()->set('paymongo.secret_key', 'sk_test_example');

    $user = User::factory()->create();
    $loan = approvedLoanForPayMongoTest($user, 1000);
    $payment = Payment::create([
        'loan_id' => $loan->id,
        'user_id' => $user->id,
        'amount' => 250,
        'reference' => 'LOANPAY-RETURN-1',
        'currency' => 'PHP',
        'method' => 'gcash',
        'provider' => 'paymongo',
        'provider_reference' => 'cs_test_return',
        'paymongo_session_id' => 'cs_test_return',
        'status' => 'pending',
    ]);

    Http::fake([
        'api.paymongo.com/v1/checkout_sessions/cs_test_return' => Http::response([
            'data' => [
                'id' => 'cs_test_return',
                'attributes' => [
                    'reference_number' => $payment->reference,
                    'payments' => [[
                        'id' => 'pay_test_return',
                        'attributes' => [
                            'amount' => 25000,
                            'currency' => 'PHP',
                            'status' => 'paid',
                            'source' => ['type' => 'gcash'],
                        ],
                    ]],
                ],
            ],
        ]),
    ]);

    $this->actingAs($user)->get(route('payments.success', $payment))
        ->assertOk()
        ->assertSee('Payment confirmed');

    expect($payment->fresh()->status)->toBe('paid')
        ->and($loan->fresh()->paid_amount)->toEqual('250.00')
        ->and($loan->fresh()->getRemainingBalance())->toEqual(750.0);
});
