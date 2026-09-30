<?php

use App\Models\ClientVerification;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\PaymentSchedule;
use App\Models\User;
use App\Notifications\ContractReadyNotification;
use App\Notifications\LoanApprovedNotification;
use App\Notifications\PenaltyAppliedNotification;
use App\Services\LoanRiskAssessmentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

function contractWorkflowLoan(User $client): Loan
{
    $type = LoanType::create([
        'name' => 'contract-test',
        'display_name' => 'Contract Test Loan',
        'min_amount' => 1000,
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
        'installment_count' => 1,
        'repayment_period_days' => 30,
        'status' => Loan::STATUS_PENDING,
        'loan_code' => 'CONTRACT-001',
    ]);
}

it('requires a signed contract before final loan approval', function () {
    Notification::fake();
    Storage::fake('local');
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client', 'first_name' => 'Maria', 'last_name' => 'Santos', 'name' => 'Maria Santos']);
    $loan = contractWorkflowLoan($client);

    $this->actingAs($admin)->post(route('admin.loan.approve', $loan))
        ->assertSessionHas('error');
    expect($loan->fresh()->status)->toBe(Loan::STATUS_PENDING);

    $this->actingAs($admin)->post(route('admin.loan.contract.send', $loan))
        ->assertSessionHas('success');
    Notification::assertSentTo($client, ContractReadyNotification::class);

    $this->actingAs($client)->get(route('loan.contract.download', $loan))
        ->assertDownload('CONTRACT-001-contract.pdf');

    $this->actingAs($client)->get(route('loan.contract.show', $loan))
        ->assertOk()
        ->assertSee('Send signed PDF to administrator');

    $this->actingAs($client)->post(route('loan.contract.sign', $loan), [
        'signed_contract' => UploadedFile::fake()->create('maria-santos-signed-contract.pdf', 120, 'application/pdf'),
        'contract_accepted' => '1',
    ])->assertRedirect(route('dashboard'));

    $loan->refresh();
    Storage::disk('local')->assertExists($loan->signed_contract_path);

    $this->actingAs($admin)->get(route('loan.contract.signed.download', $loan))
        ->assertDownload('CONTRACT-001-signed-contract.pdf');

    $this->actingAs($admin)->post(route('admin.loan.approve', $loan))
        ->assertSessionHas('success');

    expect($loan->fresh()->status)->toBe(Loan::STATUS_APPROVED)
        ->and($loan->fresh()->contract_signed_at)->not->toBeNull()
        ->and($loan->fresh()->signed_contract_path)->not->toBeNull()
        ->and($loan->fresh()->paymentSchedules)->toHaveCount(1);
    Notification::assertSentTo($client, LoanApprovedNotification::class);
});

it('notifies the client when a new overdue penalty is calculated', function () {
    Notification::fake();
    $client = User::factory()->create(['role' => 'client']);
    $loan = contractWorkflowLoan($client);
    $loan->update(['status' => Loan::STATUS_APPROVED]);
    $loan->paymentSchedules()->create([
        'installment_number' => 1,
        'scheduled_amount' => 1000,
        'due_date' => now()->subDays(2)->toDateString(),
        'paid_amount' => 0,
        'status' => PaymentSchedule::STATUS_PENDING,
        'penalty_amount' => 0,
    ]);

    $this->artisan('app:calculate-loan-penalties')->assertSuccessful();

    Notification::assertSentTo($client, PenaltyAppliedNotification::class);
    expect((float) $loan->fresh()->penalty_amount)->toBeGreaterThan(0);
});

it('shows the notification bell and unread count on the client dashboard', function () {
    $client = User::factory()->create(['role' => 'client']);
    $loan = contractWorkflowLoan($client);
    $loan->update(['contract_sent_at' => now()]);
    $client->notify(new ContractReadyNotification($loan));

    $this->actingAs($client)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Open notifications')
        ->assertSee('notification-badge', false)
        ->assertSee('Loan contract ready for signature');
});

it('only lets the owning client access and sign a contract', function () {
    $client = User::factory()->create(['role' => 'client']);
    $otherClient = User::factory()->create(['role' => 'client']);
    $loan = contractWorkflowLoan($client);
    $loan->update(['contract_sent_at' => now()]);

    $this->actingAs($otherClient)->get(route('loan.contract.show', $loan))->assertForbidden();
    $this->actingAs($otherClient)->post(route('loan.contract.sign', $loan), [
        'signed_contract' => UploadedFile::fake()->create('not-my-contract.pdf', 50, 'application/pdf'),
        'contract_accepted' => '1',
    ])->assertForbidden();
});

it('does not accept a typed name in place of a returned signed PDF', function () {
    Storage::fake('local');
    $client = User::factory()->create(['role' => 'client']);
    $loan = contractWorkflowLoan($client);
    $loan->update(['contract_sent_at' => now()]);

    $this->actingAs($client)->post(route('loan.contract.sign', $loan), [
        'signature_name' => $client->full_name,
        'contract_accepted' => '1',
    ])->assertSessionHasErrors('signed_contract');

    expect($loan->fresh()->contract_signed_at)->toBeNull()
        ->and($loan->fresh()->signed_contract_path)->toBeNull();
});

it('computes income-based risk and a lower amount suggestion', function () {
    $client = User::factory()->create(['role' => 'client']);
    ClientVerification::create([
        'user_id' => $client->id,
        'status' => ClientVerification::STATUS_APPROVED,
        'employment_status' => 'employed',
        'monthly_income' => 10000,
        'employment_length_months' => 24,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'National ID',
        'valid_id_number' => 'TEST-123',
        'valid_id_path' => 'test/id.pdf',
        'selfie_with_id_path' => 'test/selfie.jpg',
        'submitted_at' => now(),
    ]);
    $loan = contractWorkflowLoan($client);

    $assessment = app(LoanRiskAssessmentService::class)->assess($loan);

    expect($assessment['ratio'])->toBe(55.0)
        ->and($assessment['level'])->toBe('High')
        ->and($assessment['suggestedPrincipal'])->toBe(2700.0);
});
