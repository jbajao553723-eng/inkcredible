<?php

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\User;
use App\Services\BusinessReportService;

it('places the business report after the client report directory', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['name' => 'Report Directory Client', 'role' => 'client']);

    $this->actingAs($admin)->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSeeInOrder(['Client reports directory', 'Business performance report'])
        ->assertSee(route('admin.reports.business.download'), false);
});

it('builds reliable business metrics and downloads the report as a PDF', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $loanType = LoanType::create([
        'name' => 'business-report-test', 'display_name' => 'Business Report Test Loan',
        'min_amount' => 1000, 'max_amount' => 5000, 'interest_rate' => 10,
        'due_days' => 7, 'is_active' => true,
    ]);
    $loan = Loan::create([
        'user_id' => $client->id, 'loan_type_id' => $loanType->id, 'amount' => 1000,
        'total_payable' => 1100, 'paid_amount' => 300, 'status' => Loan::STATUS_APPROVED,
        'loan_code' => 'LN-BUSINESS-001', 'approved_at' => now()->subMonth(),
    ]);

    PaymentSchedule::create([
        'loan_id' => $loan->id, 'installment_number' => 1, 'scheduled_amount' => 550,
        'due_date' => now()->subDay(), 'paid_amount' => 300,
        'status' => PaymentSchedule::STATUS_OVERDUE, 'penalty_amount' => 10,
    ]);
    PaymentSchedule::create([
        'loan_id' => $loan->id, 'installment_number' => 2, 'scheduled_amount' => 550,
        'due_date' => now()->addWeek(), 'paid_amount' => 0,
        'status' => PaymentSchedule::STATUS_PENDING, 'penalty_amount' => 0,
    ]);
    Payment::create([
        'loan_id' => $loan->id, 'user_id' => $client->id, 'amount' => 300,
        'reference' => 'BUSINESS-APPROVED-001', 'currency' => 'PHP', 'method' => 'cash',
        'status' => Payment::STATUS_APPROVED, 'paid_at' => now(),
    ]);

    $summary = app(BusinessReportService::class)->generate()['summary'];
    expect($summary['contract_interest'])->toBe(100.0)
        ->and($summary['collections'])->toBe(300.0)
        ->and($summary['outstanding'])->toBe(810.0)
        ->and($summary['overdue_amount'])->toBe(260.0)
        ->and($summary['overdue_share'])->toBe(32.1);

    $response = $this->actingAs($admin)->get(route('admin.reports.business.download'));
    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertDownload('business-report-'.now('Asia/Manila')->format('Ymd').'.pdf');
    expect($response->getContent())->toStartWith('%PDF');
});
