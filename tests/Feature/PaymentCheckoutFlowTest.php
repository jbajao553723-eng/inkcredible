<?php

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Payment;
use App\Models\User;
use App\Services\PayMongoService;

function checkoutFlowLoan(User $client): Loan
{
    $type = LoanType::create([
        'name' => 'checkout-flow',
        'display_name' => 'Checkout Flow Loan',
        'min_amount' => 100,
        'max_amount' => 50000,
        'interest_rate' => 10,
        'due_days' => 30,
        'is_active' => true,
    ]);

    return Loan::create([
        'user_id' => $client->id,
        'loan_type_id' => $type->id,
        'amount' => 5000,
        'total_payable' => 5500,
        'paid_amount' => 0,
        'status' => Loan::STATUS_APPROVED,
        'loan_code' => 'CHECKOUT-001',
    ]);
}

it('starts online checkout asynchronously so the local completion window can track it', function () {
    $client = User::factory()->create(['role' => 'client']);
    $loan = checkoutFlowLoan($client);

    $payMongo = Mockery::mock(PayMongoService::class);
    $payMongo->shouldReceive('createCheckoutSession')
        ->once()
        ->andReturnUsing(function (Payment $payment): string {
            $payment->update([
                'provider' => 'paymongo',
                'provider_reference' => 'cs_test_async',
                'paymongo_session_id' => 'cs_test_async',
                'checkout_url' => 'https://checkout.paymongo.com/cs_test_async',
            ]);

            return 'https://checkout.paymongo.com/cs_test_async';
        });
    $this->app->instance(PayMongoService::class, $payMongo);

    $response = $this->actingAs($client)->postJson(route('payments.store'), [
        'loan_id' => $loan->id,
        'amount' => 500,
        'method' => 'gcash',
    ]);

    $payment = Payment::firstOrFail();

    $response->assertOk()
        ->assertJsonPath('payment_id', $payment->id)
        ->assertJsonPath('checkout_url', 'https://checkout.paymongo.com/cs_test_async')
        ->assertJsonPath('status_url', route('payments.status', $payment))
        ->assertJsonPath('cancel_url', route('payments.cancel', $payment));
});

it('reconciles a paid checkout while the completion window polls and redirects to the receipt', function () {
    $client = User::factory()->create(['role' => 'client']);
    $loan = checkoutFlowLoan($client);
    $payment = Payment::create([
        'loan_id' => $loan->id,
        'user_id' => $client->id,
        'amount' => 500,
        'reference' => 'LOANPAY-POLLING',
        'currency' => 'PHP',
        'method' => 'qrph',
        'provider' => 'paymongo',
        'provider_reference' => 'cs_test_polling',
        'paymongo_session_id' => 'cs_test_polling',
        'checkout_url' => 'https://checkout.paymongo.com/cs_test_polling',
        'status' => Payment::STATUS_PENDING,
    ]);

    $payMongo = Mockery::mock(PayMongoService::class);
    $payMongo->shouldReceive('retrieveCheckoutSession')
        ->once()
        ->with('cs_test_polling')
        ->andReturn([
            'id' => 'cs_test_polling',
            'attributes' => [
                'reference_number' => 'LOANPAY-POLLING',
                'payments' => [[
                    'id' => 'pay_test_polling',
                    'attributes' => [
                        'status' => 'paid',
                        'amount' => 50000,
                        'currency' => 'PHP',
                        'source' => ['type' => 'qrph'],
                    ],
                ]],
            ],
        ]);
    $this->app->instance(PayMongoService::class, $payMongo);

    $this->actingAs($client)->getJson(route('payments.status', $payment))
        ->assertOk()
        ->assertJsonPath('status', Payment::STATUS_APPROVED)
        ->assertJsonPath('redirect_url', route('payments.success', $payment));

    expect($payment->fresh()->status)->toBe(Payment::STATUS_APPROVED)
        ->and((float) $loan->fresh()->paid_amount)->toBe(500.0);
});

it('closes a cancelled online attempt instead of leaving it pending', function () {
    $client = User::factory()->create(['role' => 'client']);
    $loan = checkoutFlowLoan($client);
    $payment = Payment::create([
        'loan_id' => $loan->id,
        'user_id' => $client->id,
        'amount' => 500,
        'reference' => 'LOANPAY-CANCELLED',
        'currency' => 'PHP',
        'method' => 'gcash',
        'status' => Payment::STATUS_PENDING,
    ]);

    $this->actingAs($client)->get(route('payments.cancel', $payment))
        ->assertOk()
        ->assertSee('Your payment was not completed.')
        ->assertSee('No payment was applied')
        ->assertSee('Payment summary')
        ->assertSee('Try payment again');

    expect($payment->fresh()->status)->toBe(Payment::STATUS_REJECTED);
});

it('gives administrators a simplified cash payment review workspace', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $loan = checkoutFlowLoan($client);
    $payment = Payment::create([
        'loan_id' => $loan->id,
        'user_id' => $client->id,
        'amount' => 500,
        'reference' => 'LOANPAY-CASH-REVIEW',
        'currency' => 'PHP',
        'method' => 'cash',
        'status' => Payment::STATUS_PENDING,
    ]);

    $this->actingAs($admin)->get(route('admin.payment.show', $payment))
        ->assertOk()
        ->assertSee('Payment overview')
        ->assertSee('Technical provider details')
        ->assertSee('Make a decision')
        ->assertSee('Approve')
        ->assertSee('Reject');
});

it('redirects a rejected PayMongo authorization to the local payment result', function () {
    $client = User::factory()->create(['role' => 'client']);
    $loan = checkoutFlowLoan($client);
    $payment = Payment::create([
        'loan_id' => $loan->id,
        'user_id' => $client->id,
        'amount' => 500,
        'reference' => 'LOANPAY-REJECTED',
        'currency' => 'PHP',
        'method' => 'qrph',
        'provider' => 'paymongo',
        'provider_reference' => 'cs_test_rejected',
        'paymongo_session_id' => 'cs_test_rejected',
        'checkout_url' => 'https://checkout.paymongo.com/cs_test_rejected',
        'status' => Payment::STATUS_PENDING,
    ]);

    $payMongo = Mockery::mock(PayMongoService::class);
    $payMongo->shouldReceive('retrieveCheckoutSession')->once()->andReturn([
        'id' => 'cs_test_rejected',
        'attributes' => [
            'reference_number' => 'LOANPAY-REJECTED',
            'payments' => [],
            'payment_intent' => [
                'attributes' => [
                    'status' => 'awaiting_payment_method',
                    'last_payment_error' => ['message' => 'The test payment was rejected.'],
                ],
            ],
        ],
    ]);
    $this->app->instance(PayMongoService::class, $payMongo);

    $this->actingAs($client)->getJson(route('payments.status', $payment))
        ->assertOk()
        ->assertJsonPath('status', Payment::STATUS_REJECTED)
        ->assertJsonPath('redirect_url', route('payments.cancel', $payment));
});
