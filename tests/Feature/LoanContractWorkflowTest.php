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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

function contractDigitalSignature(): string
{
    $image = imagecreatetruecolor(720, 220);
    $white = imagecolorallocate($image, 255, 255, 255);
    $ink = imagecolorallocate($image, 23, 32, 51);
    imagefill($image, 0, 0, $white);
    imagesetthickness($image, 5);
    imageline($image, 100, 140, 260, 70, $ink);
    imageline($image, 260, 70, 420, 145, $ink);
    imageline($image, 420, 145, 610, 85, $ink);
    ob_start();
    imagepng($image);
    $png = ob_get_clean();
    imagedestroy($image);

    return 'data:image/png;base64,'.base64_encode($png);
}

function addVerifiedSignature(User $client): ClientVerification
{
    return ClientVerification::create([
        'user_id' => $client->id,
        'status' => ClientVerification::STATUS_APPROVED,
        'employment_status' => 'employed',
        'monthly_income' => 25000,
        'employment_length_months' => 24,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'National ID',
        'valid_id_number' => 'TEST-123',
        'valid_id_path' => 'test/id.pdf',
        'selfie_with_id_path' => 'test/selfie.jpg',
        'digital_signature' => contractDigitalSignature(),
        'signature_captured_at' => now(),
        'submitted_at' => now(),
    ]);
}

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

it('shows current loan states instead of cumulative borrowed and paid cards', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Active loans')
        ->assertSee('Pending requests')
        ->assertDontSee('Total borrowed')
        ->assertDontSee('Confirmed across all loans');
});

it('requires a signed contract before final loan approval', function () {
    Notification::fake();
    Storage::fake('local');
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client', 'first_name' => 'Maria', 'last_name' => 'Santos', 'name' => 'Maria Santos']);
    addVerifiedSignature($client);
    $loan = contractWorkflowLoan($client);

    $this->actingAs($admin)->post(route('admin.loan.approve', $loan))
        ->assertSessionHas('error');
    expect($loan->fresh()->status)->toBe(Loan::STATUS_PENDING);

    $this->actingAs($admin)->post(route('admin.loan.contract.send', $loan))
        ->assertSessionHas('success');
    Notification::assertSentTo(
        $client,
        ContractReadyNotification::class,
        fn (ContractReadyNotification $notification) => in_array('mail', $notification->via($client), true)
            && $notification->toMail($client)->subject === 'Your loan contract is ready to sign'
            && $notification->toMail($client)->salutation === 'Regards, Inkcredible'
    );

    $this->actingAs($client)->get(route('loan.contract.download', $loan))
        ->assertDownload('CONTRACT-001-contract.pdf');

    $this->actingAs($client)->get(route('loan.contract.show', $loan))
        ->assertOk()
        ->assertSee('Sign contract and send to admin')
        ->assertSee('Download PDF')
        ->assertSee('Your affordability assessment')
        ->assertSee('data-no-transition', false)
        ->assertSee('download="CONTRACT-001-contract.pdf"', false);

    $this->actingAs($client)->post(route('loan.contract.sign', $loan), [
        'contract_accepted' => '1',
    ])->assertRedirect(route('dashboard'));

    $loan->refresh();
    Storage::disk('local')->assertExists($loan->signed_contract_path);

    $this->actingAs($admin)->get(route('loan.contract.signed.download', $loan))
        ->assertDownload('CONTRACT-001-signed-contract.pdf');

    $clientSignedPath = $loan->signed_contract_path;

    $this->actingAs($admin)->post(route('admin.loan.approve', $loan))
        ->assertSessionHasErrors(['admin_signature', 'approval_accepted']);
    expect($loan->fresh()->status)->toBe(Loan::STATUS_PENDING);

    $adminSignature = contractDigitalSignature();
    $this->actingAs($admin)->post(route('admin.loan.approve', $loan), [
        'admin_signature' => $adminSignature,
        'approval_accepted' => '1',
    ])
        ->assertSessionHas('success');

    $loan->refresh();

    expect($loan->status)->toBe(Loan::STATUS_APPROVED)
        ->and($loan->contract_signed_at)->not->toBeNull()
        ->and($loan->admin_signed_at)->not->toBeNull()
        ->and($loan->admin_signature)->toBe($adminSignature)
        ->and($loan->admin_signature_name)->toBe($admin->full_name)
        ->and($loan->admin_signed_by)->toBe($admin->id)
        ->and($loan->signed_contract_path)->not->toBe($clientSignedPath)
        ->and($loan->paymentSchedules)->toHaveCount(1);
    expect(DB::table('loans')->where('id', $loan->id)->value('admin_signature'))->not->toBe($adminSignature);
    Storage::disk('local')->assertMissing($clientSignedPath);
    Storage::disk('local')->assertExists($loan->signed_contract_path);

    $this->actingAs($admin)->get(route('admin.loan.show', $loan))
        ->assertOk()
        ->assertSee('Download final signed contract');
    $this->actingAs($admin)->get(route('loan.contract.signed.download', $loan))
        ->assertDownload('CONTRACT-001-final-signed-contract.pdf');
    Notification::assertSentTo(
        $client,
        LoanApprovedNotification::class,
        fn (LoanApprovedNotification $notification) => in_array('mail', $notification->via($client), true)
            && $notification->toMail($client)->subject === 'Your loan has been approved'
            && $notification->toMail($client)->salutation === 'Regards, Inkcredible'
    );
});

it('notifies the client when a new overdue penalty is calculated', function () {
    expect(Loan::DAILY_PENALTY_RATE)->toBe(10.0);

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

    Notification::assertSentTo(
        $client,
        PenaltyAppliedNotification::class,
        fn (PenaltyAppliedNotification $notification) => in_array('mail', $notification->via($client), true)
            && $notification->toMail($client)->subject === 'Overdue loan payment notice'
            && $notification->toMail($client)->salutation === 'Regards, Inkcredible'
    );
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
        ->assertSee('Loan contract ready for signature')
        ->assertSee('notification-detail-modal', false)
        ->assertSee('data-notification-open', false);

    $notification = $client->unreadNotifications()->firstOrFail();

    $this->postJson(route('notifications.read', $notification->id))
        ->assertOk()
        ->assertJson([
            'read' => true,
            'unread_count' => 0,
        ]);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('only lets the owning client access and sign a contract', function () {
    $client = User::factory()->create(['role' => 'client']);
    $otherClient = User::factory()->create(['role' => 'client']);
    $loan = contractWorkflowLoan($client);
    $loan->update(['contract_sent_at' => now()]);

    $this->actingAs($otherClient)->get(route('loan.contract.show', $loan))->assertForbidden();
    $this->actingAs($otherClient)->post(route('loan.contract.sign', $loan), [
        'contract_accepted' => '1',
    ])->assertForbidden();
});

it('requires a verified digital signature before automatic contract signing', function () {
    Storage::fake('local');
    $client = User::factory()->create(['role' => 'client']);
    $loan = contractWorkflowLoan($client);
    $loan->update(['contract_sent_at' => now()]);

    $this->actingAs($client)->post(route('loan.contract.sign', $loan), [
        'contract_accepted' => '1',
    ])->assertRedirect(route('profile.verification.edit'));

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
        ->and($assessment['isAssessed'])->toBeTrue()
        ->and($assessment['level'])->toBe('High')
        ->and($assessment['suggestedPrincipal'])->toBe(2500.0);
});

it('improves readiness for a verified payslip and early repayment history', function () {
    $client = User::factory()->create(['role' => 'client']);
    ClientVerification::create([
        'user_id' => $client->id,
        'status' => ClientVerification::STATUS_APPROVED,
        'employment_status' => 'employed',
        'monthly_income' => 10000,
        'employment_length_months' => 24,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'National ID',
        'valid_id_number' => 'SCORE-123',
        'valid_id_path' => 'test/id.pdf',
        'selfie_with_id_path' => 'test/selfie.jpg',
        'payslip_path' => 'test/payslip.pdf',
        'payslip_uploaded_at' => now()->subDay(),
        'payslip_verified_at' => now(),
        'submitted_at' => now(),
    ]);
    $candidate = contractWorkflowLoan($client);
    $history = Loan::create([
        'user_id' => $client->id,
        'loan_type_id' => $candidate->loan_type_id,
        'amount' => 1000,
        'total_payable' => 1100,
        'installment_count' => 1,
        'repayment_period_days' => 30,
        'status' => Loan::STATUS_PAID,
    ]);
    $history->paymentSchedules()->create([
        'installment_number' => 1,
        'scheduled_amount' => 1100,
        'due_date' => now()->subDays(2),
        'paid_amount' => 1100,
        'paid_date' => now()->subDays(5),
        'status' => PaymentSchedule::STATUS_PAID,
    ]);

    $assessment = app(LoanRiskAssessmentService::class)->assess($candidate);

    expect($assessment['ratio'])->toBe(55.0)
        ->and($assessment['verifiedPayslip'])->toBeTrue()
        ->and($assessment['earlyPayments'])->toBe(1)
        ->and($assessment['onTimePayments'])->toBe(0)
        ->and($assessment['completedLoans'])->toBe(1)
        ->and($assessment['readinessScore'])->toBeGreaterThan(42)
        ->and($assessment['level'])->toBe('Moderate');
});

it('does not assign a risk level before client verification is approved', function () {
    $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
    ClientVerification::create([
        'user_id' => $client->id,
        'status' => ClientVerification::STATUS_PENDING,
        'employment_status' => 'employed',
        'monthly_income' => 30000,
        'employment_length_months' => 12,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'National ID',
        'valid_id_number' => 'PENDING-123',
        'valid_id_path' => 'test/id.pdf',
        'selfie_with_id_path' => 'test/selfie.jpg',
        'submitted_at' => now(),
    ]);

    $profile = app(LoanRiskAssessmentService::class)->profile($client);

    expect($profile['isAssessed'])->toBeFalse()
        ->and($profile['readinessScore'])->toBeNull()
        ->and($profile['income'])->toBe(0.0)
        ->and($profile['ratio'])->toBeNull()
        ->and($profile['level'])->toBe('Not assessed')
        ->and($profile['tone'])->toBe('neutral')
        ->and($profile['verificationStatus'])->toBe(ClientVerification::STATUS_PENDING);

    $this->actingAs($client)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Not assessed yet')
        ->assertSee('Awaiting verification')
        ->assertDontSee('Very high risk');
});
