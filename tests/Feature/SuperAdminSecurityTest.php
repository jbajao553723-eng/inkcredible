<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('keeps the superadmin workspace focused on security', function () {
    $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

    $this->actingAs($superadmin)->get(route('admin.security.dashboard'))
        ->assertOk()
        ->assertSee('Security dashboard')
        ->assertSee('Administrator access')
        ->assertSee('Audit activity')
        ->assertSee('Threat signals')
        ->assertSee('Control readiness')
        ->assertSee('Active-account enforcement')
        ->assertDontSee('Multi-factor authentication')
        ->assertDontSee('Loan requests')
        ->assertDontSee('Payments')
        ->assertDontSee('Reports');

    $this->actingAs($superadmin)->get(route('admin.settings.edit'))
        ->assertOk()
        ->assertSee('Administrator account')
        ->assertSee('Motion accessibility');

    $this->actingAs($superadmin)->get(route('admin.loans'))->assertForbidden();
    $this->actingAs($superadmin)->get(route('admin.clients'))->assertForbidden();
    $this->actingAs($superadmin)->get(route('admin.payments.index'))->assertForbidden();
    $this->actingAs($superadmin)->get(route('admin.reports.index'))->assertForbidden();
});

it('keeps operational modules available to standard admins', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('admin.loans'))->assertOk();
    $this->actingAs($admin)->get(route('admin.clients'))->assertOk();
    $this->actingAs($admin)->get(route('admin.payments.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.reports.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.audit-logs.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('admin.dashboard'))->assertDontSee('Audit logs');
});

it('lets a superadmin update an administrator profile and password securely', function () {
    $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'first_name' => 'Old',
        'last_name' => 'Name',
        'name' => 'Old Name',
        'email' => 'old-admin@example.com',
    ]);

    DB::table('sessions')->insert([
        'id' => 'administrator-session',
        'user_id' => $admin->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Feature test',
        'payload' => 'test-payload',
        'last_activity' => now()->timestamp,
    ]);
    DB::table('password_reset_tokens')->insert([
        'email' => $admin->email,
        'token' => 'test-token',
        'created_at' => now(),
    ]);

    $this->actingAs($superadmin)->patch(route('admin.access.admins.update', $admin), [
        'editing_admin_id' => $admin->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane.admin@example.com',
        'contact_number' => '09171234567',
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertRedirect(route('admin.access.index'));

    $admin->refresh();

    expect($admin->first_name)->toBe('Jane')
        ->and($admin->last_name)->toBe('Doe')
        ->and($admin->name)->toBe('Jane Doe')
        ->and($admin->email)->toBe('jane.admin@example.com')
        ->and($admin->contact_number)->toBe('09171234567')
        ->and(Hash::check('NewPassword1!', $admin->password))->toBeTrue();

    $this->assertDatabaseMissing('sessions', ['user_id' => $admin->id]);
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'old-admin@example.com']);
});

it('does not let a standard admin edit another administrator', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $otherAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)->patch(route('admin.access.admins.update', $otherAdmin), [
        'editing_admin_id' => $otherAdmin->id,
        'first_name' => 'Changed',
        'last_name' => 'Name',
        'email' => $otherAdmin->email,
        'contact_number' => $otherAdmin->contact_number,
    ])->assertForbidden();
});

it('shows edit controls in the superadmin access module', function () {
    $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($superadmin)->get(route('admin.access.index'))
        ->assertOk()
        ->assertSee('data-edit-admin', false)
        ->assertSee('edit-admin-modal');
});
