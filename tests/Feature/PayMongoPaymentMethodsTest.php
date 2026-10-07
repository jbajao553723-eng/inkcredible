<?php

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Payment;
use App\Models\User;
use App\Services\PayMongoService;
use Illuminate\Support\Facades\Http;

function paymentMethodsLoan(User $client): Loan
{
    $type = LoanType::create([
        'name' => 'payment-methods',
        'display_name' => 'Payment Methods Loan',
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
        'loan_code' => 'PAYMENT-METHODS-001',
    ]);
}

it('offers only GCash or QR Ph and cash on the client payment page', function () {
    $client = User::factory()->create(['role' => 'client']);
    paymentMethodsLoan($client);

    $this->actingAs($client)->get(route('payments.index'))
        ->assertOk()
        ->assertSee('GCash / QR Ph')
        ->assertSee('Cash payment')
        ->assertDontSee('Maya')
        ->assertDontSee('Bank transfer');
});

it('requests both GCash and QR Ph from PayMongo checkout', function () {
    config(['paymongo.secret_key' => 'sk_test_example']);

    $client = User::factory()->create(['role' => 'client']);
    $loan = paymentMethodsLoan($client);
    $payment = Payment::create([
        'loan_id' => $loan->id,
        'user_id' => $client->id,
        'amount' => 500,
        'reference' => 'LOANPAY-GCASH-QRPH',
        'currency' => 'PHP',
        'method' => 'gcash',
        'status' => Payment::STATUS_PENDING,
    ]);

    Http::fake([
        'api.paymongo.com/v2/checkout_sessions' => Http::response([
            'data' => [
                'id' => 'cs_test_gcash_qrph',
                'attributes' => [
                    'checkout_url' => 'https://checkout.paymongo.com/cs_test_gcash_qrph',
                ],
            ],
        ]),
    ]);

    expect(app(PayMongoService::class)->createCheckoutSession($payment))
        ->toBe('https://checkout.paymongo.com/cs_test_gcash_qrph');

    Http::assertSent(fn ($request) => $request->url() === 'https://api.paymongo.com/v2/checkout_sessions'
        && $request['data']['attributes']['payment_method_types'] === ['gcash', 'qrph']);
});
