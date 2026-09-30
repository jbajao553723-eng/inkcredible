<?php

use App\Models\AuditLog;
use App\Models\User;

it('records successful and failed login activity with timestamps', function () {
    $user = User::factory()->create(['email' => 'audit@example.com']);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    expect(AuditLog::where('action', 'login_failed')->exists())->toBeTrue()
        ->and(AuditLog::where('action', 'login')->where('user_id', $user->id)->exists())->toBeTrue()
        ->and(AuditLog::where('action', 'login')->first()->occurred_at)->not->toBeNull();
});

it('only exposes the audit module to administrators', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($client)->get(route('admin.audit-logs.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('admin.audit-logs.index'))
        ->assertOk()
        ->assertSee('Audit logs')
        ->assertSee('data-async-filter', false)
        ->assertSee('data-async-filter-region', false)
        ->assertSee('name="event_action"', false)
        ->assertDontSee('name="action"', false);
});

it('returns only matching audit rows while preserving the asynchronous result region', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    AuditLog::create([
        'user_id' => $admin->id,
        'action' => 'login',
        'description' => 'Visible matching login event',
        'occurred_at' => now(),
    ]);
    AuditLog::create([
        'user_id' => $admin->id,
        'action' => 'logout',
        'description' => 'Hidden logout event',
        'occurred_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.audit-logs.index', ['event_action' => 'login']))
        ->assertOk()
        ->assertSee('Visible matching login event')
        ->assertDontSee('Hidden logout event')
        ->assertSee('id="audit-results"', false);
});

it('enables asynchronous filtering on the server-backed payment directory', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.payments.index'))
        ->assertOk()
        ->assertSee('id="payment-directory"', false)
        ->assertSee('data-async-filter', false);
});
