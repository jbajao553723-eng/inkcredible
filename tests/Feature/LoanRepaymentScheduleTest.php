<?php

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-09-26 09:00:00', 'Asia/Manila'));
});

afterEach(function () {
    Carbon::setTestNow();
});

it('creates the correct repayment schedule when an admin approves a loan', function (
    string $type,
    int $expectedInstallments,
    int $intervalDays,
    int $periodDays,
) {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $loanType = LoanType::create([
        'name' => $type,
        'display_name' => ucfirst($type).' Loan',
        'description' => 'Test product',
        'min_amount' => 1000,
        'max_amount' => 200000,
        'interest_rate' => 10,
        'due_days' => $intervalDays,
        'is_active' => true,
    ]);

    $loan = Loan::create([
        'user_id' => $client->id,
        'loan_type_id' => $loanType->id,
        'purpose' => 'Testing the repayment schedule',
        'amount' => 1000,
        'total_payable' => 1100.01,
        // Deliberately incorrect values prove approval enforces product rules.
        'installment_count' => 1,
        'repayment_period_days' => 7,
        'paid_amount' => 0,
        'status' => Loan::STATUS_PENDING,
        'loan_code' => 'TEST-'.strtoupper($type),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.loan.approve', $loan))
        ->assertSessionHas('success');

    $loan->refresh();
    $schedules = $loan->paymentSchedules()->get();

    expect($loan->installment_count)->toBe($expectedInstallments)
        ->and($loan->repayment_period_days)->toBe($periodDays)
        ->and($schedules)->toHaveCount($expectedInstallments)
        ->and(round((float) $schedules->sum('scheduled_amount'), 2))->toBe(1100.01)
        ->and($schedules->first()->due_date->toDateString())->toBe(now()->addDays($intervalDays)->toDateString())
        ->and($schedules->last()->due_date->toDateString())->toBe(now()->addDays($periodDays)->toDateString());
})->with([
    'arawan: every day for one month' => ['arawan', 30, 1, 30],
    'weekly: once a week for four weeks' => ['weekly', 4, 7, 28],
    'emergency: once a week for four weeks' => ['emergency', 4, 7, 28],
]);

it('allocates an approved payment to scheduled installments in due order', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $loanType = LoanType::create([
        'name' => 'arawan',
        'display_name' => 'Arawan Loan',
        'min_amount' => 1000,
        'max_amount' => 10000,
        'interest_rate' => 10,
        'due_days' => 1,
        'is_active' => true,
    ]);
    $loan = Loan::create([
        'user_id' => $client->id,
        'loan_type_id' => $loanType->id,
        'amount' => 1000,
        'total_payable' => 1100.01,
        'paid_amount' => 0,
        'status' => Loan::STATUS_PENDING,
        'loan_code' => 'TEST-ARAWAN-ALLOCATION',
    ]);

    $this->actingAs($admin)->post(route('admin.loan.approve', $loan));

    $payment = Payment::create([
        'loan_id' => $loan->id,
        'user_id' => $client->id,
        'amount' => 80,
        'reference' => 'TEST-ARAWAN-PAYMENT',
        'currency' => 'PHP',
        'method' => 'cash',
        'status' => Payment::STATUS_PENDING,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.payment.approve', $payment))
        ->assertSessionHas('success');

    $schedules = $loan->paymentSchedules()->get();

    expect($schedules[0]->status)->toBe(PaymentSchedule::STATUS_PAID)
        ->and($schedules[0]->paid_amount)->toEqual('36.66')
        ->and($schedules[1]->status)->toBe(PaymentSchedule::STATUS_PAID)
        ->and($schedules[1]->paid_amount)->toEqual('36.66')
        ->and($schedules[2]->status)->toBe(PaymentSchedule::STATUS_PENDING)
        ->and($schedules[2]->paid_amount)->toEqual('6.68')
        ->and($loan->fresh()->paid_amount)->toEqual('80.00');
});
