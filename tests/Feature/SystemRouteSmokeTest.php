<?php

use App\Models\ClientVerification;
use App\Models\User;

it('serves every primary public page without a 404 response', function () {
    foreach (['home', 'login', 'register', 'terms', 'loan.terms', 'password.request'] as $routeName) {
        $this->get(route($routeName))->assertSuccessful();
    }
});

it('serves every primary admin navigation page without a 404 response', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    foreach ([
        'admin.dashboard',
        'admin.loans',
        'admin.clients',
        'admin.payments.index',
        'admin.reports.index',
        'admin.audit-logs.index',
    ] as $routeName) {
        $this->actingAs($admin)->get(route($routeName))->assertSuccessful();
    }

    $this->actingAs($admin)
        ->get(route('admin.verifications.index'))
        ->assertRedirect(route('admin.clients', ['section' => 'verifications']));
});

it('serves every primary client navigation page without a 404 response', function () {
    $client = User::factory()->create(['role' => 'client']);
    ClientVerification::create([
        'user_id' => $client->id,
        'status' => ClientVerification::STATUS_APPROVED,
        'employment_status' => 'employed',
        'monthly_income' => 25000,
        'employment_length_months' => 12,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'National ID',
        'valid_id_number' => 'SMOKE-TEST-ID',
        'valid_id_path' => 'tests/id.pdf',
        'selfie_with_id_path' => 'tests/selfie.jpg',
        'submitted_at' => now(),
    ]);

    foreach ([
        'dashboard',
        'loan.create',
        'payments.index',
        'profile.edit',
        'profile.verification.edit',
        'profile.security.edit',
    ] as $routeName) {
        $this->actingAs($client)->get(route($routeName))->assertSuccessful();
    }
});
