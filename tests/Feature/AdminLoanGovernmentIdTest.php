<?php

use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanType;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function loanWithGovernmentId(): Loan
{
    $client = User::factory()->create(['role' => 'client']);
    $type = LoanType::create([
        'name' => 'document-test',
        'display_name' => 'Document Test Loan',
        'min_amount' => 1000,
        'max_amount' => 10000,
        'interest_rate' => 10,
        'due_days' => 30,
        'is_active' => true,
    ]);
    $loan = Loan::create([
        'user_id' => $client->id,
        'loan_type_id' => $type->id,
        'amount' => 5000,
        'total_payable' => 5500,
        'installment_count' => 1,
        'repayment_period_days' => 30,
        'status' => Loan::STATUS_PENDING,
    ]);

    Storage::disk('public')->put('ids/government-id.pdf', '%PDF-1.4 test document');
    $loan->loanDocuments()->create([
        'document_type' => LoanDocument::TYPE_ID,
        'file_path' => 'ids/government-id.pdf',
        'original_filename' => 'government-id.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 22,
    ]);

    return $loan;
}

it('opens the government ID in a modal backed by a protected document route', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $loan = loanWithGovernmentId();

    $this->actingAs($admin)->get(route('admin.loan.show', $loan))
        ->assertOk()
        ->assertSee('id="government-id-modal"', false)
        ->assertSee('data-government-id-open', false)
        ->assertSee(route('admin.loan.government-id', $loan), false)
        ->assertDontSee('/storage/ids/government-id.pdf', false);

    $this->actingAs($admin)->get(route('admin.loan.government-id', $loan))
        ->assertOk()
        ->assertHeader('content-disposition', 'inline; filename=government-id.pdf')
        ->assertHeader('cache-control', 'no-store, private');
});

it('does not expose a government ID to a client', function () {
    Storage::fake('public');
    $loan = loanWithGovernmentId();

    $this->actingAs($loan->user)->get(route('admin.loan.government-id', $loan))
        ->assertForbidden();
});

it('returns not found when the stored government ID file is missing', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $loan = loanWithGovernmentId();
    Storage::disk('public')->delete('ids/government-id.pdf');

    $this->actingAs($admin)->get(route('admin.loan.government-id', $loan))
        ->assertNotFound();
});
