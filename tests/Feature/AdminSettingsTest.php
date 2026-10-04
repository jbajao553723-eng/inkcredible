<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('shows administrator settings to admins and superadmins only', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

    $this->actingAs($admin)->get(route('admin.settings.edit'))
        ->assertOk()
        ->assertSee('Profile information')
        ->assertSee('Password and sign-in security')
        ->assertSee('Motion accessibility')
        ->assertSee('Reduce motion')
        ->assertDontSee('Interface preferences')
        ->assertDontSee('Accent color')
        ->assertDontSee('Workspace density')
        ->assertDontSee('Higher contrast')
        ->assertDontSee('two-factor');

    $this->actingAs($superadmin)->get(route('admin.settings.edit'))->assertOk();
    $this->actingAs($client)->get(route('admin.settings.edit'))->assertForbidden();
});

it('lets an administrator update their own profile information', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'first_name' => 'Old',
        'last_name' => 'Admin',
        'name' => 'Old Admin',
        'contact_number' => 'Not provided',
    ]);

    $this->actingAs($admin)->patch(route('admin.settings.profile.update'), [
        'first_name' => 'Jamie',
        'last_name' => 'Reyes',
        'email' => $admin->email,
        'contact_number' => '09171234567',
    ])->assertRedirect(route('admin.settings.edit', ['section' => 'profile']));

    $admin->refresh();
    expect($admin->name)->toBe('Jamie Reyes')
        ->and($admin->first_name)->toBe('Jamie')
        ->and($admin->last_name)->toBe('Reyes')
        ->and($admin->contact_number)->toBe('09171234567');
});

it('requires the current password before changing an administrator email', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)->patch(route('admin.settings.profile.update'), [
        'first_name' => $admin->first_name,
        'last_name' => $admin->last_name,
        'email' => 'changed-admin@example.test',
        'contact_number' => $admin->contact_number,
    ])->assertSessionHasErrors('current_password', errorBag: 'profile');

    expect($admin->fresh()->email)->toBe($admin->email);
});

it('lets an administrator change their email without client email verification', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $verifiedAt = $admin->email_verified_at;

    $this->actingAs($admin)->patch(route('admin.settings.profile.update'), [
        'first_name' => $admin->first_name,
        'last_name' => $admin->last_name,
        'email' => 'changed-admin@example.test',
        'contact_number' => $admin->contact_number,
        'current_password' => 'password',
    ])->assertRedirect(route('admin.settings.edit', ['section' => 'profile']));

    $admin->refresh();
    expect($admin->email)->toBe('changed-admin@example.test')
        ->and($admin->email_verified_at?->equalTo($verifiedAt))->toBeTrue();
});

it('lets an administrator change their password and revokes other sessions', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    DB::table('sessions')->insert([
        'id' => 'other-admin-session',
        'user_id' => $admin->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Feature test',
        'payload' => 'test-payload',
        'last_activity' => now()->timestamp,
    ]);

    $this->actingAs($admin)->put(route('admin.settings.password.update'), [
        'current_password' => 'password',
        'password' => 'NewAdminPassword1!',
        'password_confirmation' => 'NewAdminPassword1!',
    ])->assertRedirect(route('admin.settings.edit', ['section' => 'security']));

    expect(Hash::check('NewAdminPassword1!', $admin->fresh()->password))->toBeTrue();
    $this->assertDatabaseMissing('sessions', ['id' => 'other-admin-session']);
});

it('persists the administrator motion preference across the workspace', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)->patch(route('admin.settings.motion.update'), [
        'reduce_motion' => '1',
    ])->assertRedirect(route('admin.settings.edit', ['section' => 'motion']));

    expect($admin->fresh()->ui_preferences)->toBe(['reduce_motion' => true]);

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('--primary: #4f46e5', false)
        ->assertSee('--sidebar-width: 268px', false)
        ->assertSee('transition-duration:.01ms', false);
});
