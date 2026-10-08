<?php

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Support\Facades\Notification;

test('a client can enable email two factor authentication', function () {
    Notification::fake();
    $user = User::factory()->create(['password' => 'Password1!']);

    $this->actingAs($user)
        ->post(route('two-factor.enable'), ['current_password' => 'Password1!'])
        ->assertRedirect(route('two-factor.setup.show'));

    $code = null;
    Notification::assertSentTo($user, EmailOtpNotification::class, function (EmailOtpNotification $notification) use (&$code) {
        $code = $notification->code;

        return $notification->purpose === 'two-factor-setup';
    });

    $this->post(route('two-factor.setup.verify'), ['code' => $code])
        ->assertRedirect(route('profile.security.edit'))
        ->assertSessionHas('status', 'two-factor-enabled');

    expect($user->fresh()->hasTwoFactorAuthenticationEnabled())->toBeTrue();
});

test('two factor enabled accounts need an email code after their password', function () {
    Notification::fake();
    $user = User::factory()->create([
        'password' => 'Password1!',
        'ui_preferences' => ['security' => ['two_factor_enabled_at' => now()->toIso8601String()]],
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'Password1!',
    ])->assertRedirect(route('two-factor.login.show'));

    $this->assertGuest();

    $code = null;
    Notification::assertSentTo($user, EmailOtpNotification::class, function (EmailOtpNotification $notification) use (&$code) {
        $code = $notification->code;

        return $notification->purpose === 'two-factor-login';
    });

    $this->post(route('two-factor.login.verify'), ['code' => $code])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('client and administrator security pages show the two factor option', function () {
    $client = User::factory()->create();
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($client)
        ->get(route('profile.security.edit'))
        ->assertOk()
        ->assertSee('Two-factor authentication');

    $this->actingAs($admin)
        ->get(route('admin.settings.edit', ['section' => 'security']))
        ->assertOk()
        ->assertSee('Two-factor authentication');
});
