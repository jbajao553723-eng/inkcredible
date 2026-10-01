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

it('only exposes the audit module to superadministrators', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

    $this->actingAs($client)->get(route('admin.audit-logs.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('admin.audit-logs.index'))->assertForbidden();
    $this->actingAs($superadmin)->get(route('admin.audit-logs.index'))
        ->assertOk()
        ->assertSee('Security audit logs')
        ->assertSee('Severity')
        ->assertSee('data-async-filter', false)
        ->assertSee('data-async-filter-region', false)
        ->assertSee('name="event_action"', false)
        ->assertDontSee('name="action"', false);
});

it('returns only matching audit rows while preserving the asynchronous result region', function () {
    $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
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

it('filters audit events by severity and searchable security context', function () {
    $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    AuditLog::create(['action' => 'login_failed', 'description' => 'Suspicious sign-in from a new source', 'ip_address' => '203.0.113.8', 'occurred_at' => now()]);
    AuditLog::create(['user_id' => $superadmin->id, 'action' => 'login', 'description' => 'Normal sign-in', 'ip_address' => '127.0.0.1', 'occurred_at' => now()]);

    $this->actingAs($superadmin)
        ->get(route('admin.audit-logs.index', ['severity' => 'high', 'q' => '203.0.113.8']))
        ->assertOk()
        ->assertSee('Suspicious sign-in from a new source')
        ->assertSee('severity-high', false)
        ->assertDontSee('Normal sign-in');
});

it('records denied access to protected administrator routes as a high severity event', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)->get(route('admin.audit-logs.index'))->assertForbidden();

    $event = AuditLog::where('action', 'authorization_failed')->latest('id')->firstOrFail();

    expect($event->user_id)->toBe($admin->id)
        ->and($event->severity)->toBe('high')
        ->and(data_get($event->metadata, 'response_status'))->toBe(403);
});

it('enables asynchronous filtering on the server-backed payment directory', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.payments.index'))
        ->assertOk()
        ->assertSee('id="payment-directory"', false)
        ->assertSee('data-async-filter', false);
});
