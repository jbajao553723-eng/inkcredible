<?php

use App\Models\ClientVerification;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanType;
use App\Models\User;
use Database\Seeders\LoanTypesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function verifiedLoanApplicant(): User
{
    $client = User::factory()->create(['role' => 'client']);

    ClientVerification::create([
        'user_id' => $client->id,
        'status' => ClientVerification::STATUS_APPROVED,
        'employment_status' => 'employed',
        'monthly_income' => 30_000,
        'employment_length_months' => 24,
        'source_of_income' => 'Employment',
        'valid_id_type' => 'Passport',
        'valid_id_number' => 'TEST-123',
        'valid_id_path' => 'client-verifications/test/id.jpg',
        'selfie_with_id_path' => 'client-verifications/test/selfie.jpg',
        'submitted_at' => now(),
    ]);

    return $client;
}

function activeDailyLoanType(): LoanType
{
    return LoanType::create([
        'name' => 'arawan',
        'display_name' => 'Arawan Loan',
        'description' => 'Daily repayment product',
        'min_amount' => 1_000,
        'max_amount' => 15_000,
        'interest_rate' => 20,
        'due_days' => 30,
        'is_active' => true,
    ]);
}

it('uses the current Arawan and Weekly product limits and rates', function () {
    $this->seed(LoanTypesSeeder::class);

    $arawan = LoanType::where('name', 'arawan')->firstOrFail();
    $weekly = LoanType::where('name', 'weekly')->firstOrFail();

    expect((float) $arawan->max_amount)->toBe(15000.0)
        ->and((float) $arawan->interest_rate)->toBe(20.0)
        ->and((float) $weekly->max_amount)->toBe(50000.0)
        ->and((float) $weekly->interest_rate)->toBe(20.0);
});

it('shows the client the same affordability calculation used during review', function () {
    $client = verifiedLoanApplicant();
    $loanType = activeDailyLoanType();

    Loan::create([
        'user_id' => $client->id,
        'loan_type_id' => $loanType->id,
        'amount' => 2_500,
        'status' => Loan::STATUS_APPROVED,
        'total_payable' => 3_000,
        'installment_count' => 30,
        'repayment_period_days' => 30,
    ]);

    $this->actingAs($client)->get(route('loan.create'))
        ->assertOk()
        ->assertSee('Application readiness preview')
        ->assertSee('Verified monthly income')
        ->assertSee('Existing approved commitments')
        ->assertSee('Verified payslip');
});

it('submits the application, loan, and supporting document together', function () {
    Storage::fake('public');
    $client = verifiedLoanApplicant();
    $loanType = activeDailyLoanType();

    $response = $this->actingAs($client)->post(route('loan.store'), [
        'loan_type' => $loanType->name,
        'amount' => 5_000,
        'purpose_choice' => 'allowance',
        'government_id' => UploadedFile::fake()->create('government-id.pdf', 100, 'application/pdf'),
        'loan_terms_accepted' => '1',
    ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success');

    $loan = Loan::firstOrFail();

    expect(LoanApplication::count())->toBe(1)
        ->and($loan->user_id)->toBe($client->id)
        ->and((float) $loan->total_payable)->toBe(6_000.0)
        ->and($loan->installment_count)->toBe(30)
        ->and($loan->loanDocuments)->toHaveCount(1);

    Storage::disk('public')->assertExists($loan->loanDocuments->first()->file_path);
});

it('rejects amounts outside the selected product limits', function () {
    Storage::fake('public');
    $client = verifiedLoanApplicant();
    $loanType = activeDailyLoanType();

    $response = $this->actingAs($client)->from(route('loan.create'))->post(route('loan.store'), [
        'loan_type' => $loanType->name,
        'amount' => 15_001,
        'purpose_choice' => 'allowance',
        'government_id' => UploadedFile::fake()->create('government-id.pdf', 100, 'application/pdf'),
        'loan_terms_accepted' => '1',
    ]);

    $response->assertRedirect(route('loan.create'));
    $response->assertSessionHas('error');
    expect(Loan::count())->toBe(0)
        ->and(LoanApplication::count())->toBe(0);
});
