<?php

use App\Models\ClientVerification;
use App\Models\User;

it('combines the client directory and verification queue in one admin workspace', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);

    ClientVerification::create([
        'user_id' => $client->id,
        'status' => ClientVerification::STATUS_PENDING,
        'employment_status' => 'employed',
        'company_name' => 'Example Company',
        'job_title' => 'Staff',
        'monthly_income' => 25000,
        'employment_length_months' => 12,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'National ID',
        'valid_id_number' => 'TEST-1234',
        'valid_id_path' => 'client-verifications/test/id.jpg',
        'selfie_with_id_path' => 'client-verifications/test/selfie.jpg',
        'submitted_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.clients'))
        ->assertOk()
        ->assertSee('Clients and verification')
        ->assertSee('Client directory')
        ->assertSee($client->email);

    $this->actingAs($admin)
        ->get(route('admin.clients', ['section' => 'verifications']))
        ->assertOk()
        ->assertSee('Verification queue')
        ->assertSee('Example Company');

    $this->actingAs($admin)
        ->get(route('admin.verifications.index'))
        ->assertRedirect(route('admin.clients', ['section' => 'verifications']));
});
