<?php

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\User;

it('allows admins to download a client report as a PDF', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $client = User::factory()->create(['name' => 'María Dela Cruz']);
    $client->forceFill(['role' => 'client'])->save();

    $loanType = LoanType::create([
        'name' => 'report-test',
        'display_name' => 'Report Test Loan',
        'description' => 'Loan used for report testing',
        'min_amount' => 1000,
        'max_amount' => 5000,
        'interest_rate' => 10,
        'due_days' => 30,
        'is_active' => true,
    ]);

    Loan::create([
        'user_id' => $client->id,
        'loan_type_id' => $loanType->id,
        'amount' => 1000,
        'total_payable' => 1100,
        'paid_amount' => 100,
        'status' => 'approved',
        'loan_code' => 'LN-REPORT-001',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.clients.report', $client));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertDownload('client-report-maria-dela-cruz-'.now('Asia/Manila')->format('Ymd').'.pdf');

    expect($response->getContent())->toStartWith('%PDF');
});

it('protects reports from guests and non-admin users', function () {
    $client = User::factory()->create();
    $client->forceFill(['role' => 'client'])->save();

    $this->get(route('admin.clients.report', $client))->assertRedirectToRoute('login');
    $this->actingAs($client)->get(route('admin.clients.report', $client))->assertForbidden();
});

it('returns not found when an admin requests a report for a non-client account', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $this->actingAs($admin)->get(route('admin.clients.report', $admin))->assertNotFound();
});
