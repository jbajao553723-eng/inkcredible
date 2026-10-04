<?php

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

test('password recovery screen can be rendered', function () {
    $this->get('/forgot-password')
        ->assertOk()
        ->assertSee('Forgot your password?')
        ->assertSee('Send verification code')
        ->assertSee(route('login'));
});

test('password recovery code can be requested', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('password.otp.show'));

    Notification::assertSentTo($user, EmailOtpNotification::class, function (EmailOtpNotification $notification) {
        return $notification->purpose === 'password-recovery'
            && preg_match('/^\d{6}$/', $notification->code) === 1;
    });
});

test('password recovery does not reveal whether an email is registered', function () {
    Notification::fake();

    $this->post('/forgot-password', ['email' => 'unknown@example.test'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', 'If an account matches that email, a six-digit verification code has been sent.')
        ->assertRedirect(route('password.otp.show'));

    $this->get(route('password.otp.show'))
        ->assertOk()
        ->assertSee('Verify password recovery');

    Notification::assertNothingSent();
});

test('a valid recovery code opens the reset password screen', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    $code = null;
    Notification::assertSentTo($user, EmailOtpNotification::class, function (EmailOtpNotification $notification) use (&$code) {
        $code = $notification->code;

        return $notification->purpose === 'password-recovery';
    });

    $verification = $this->post(route('password.otp.verify'), ['code' => $code]);
    $resetUrl = $verification->headers->get('Location');

    expect($resetUrl)->not->toBeNull()
        ->and($resetUrl)->toContain('/reset-password/');

    $this->get($resetUrl)
        ->assertOk()
        ->assertSee('Choose a new password')
        ->assertSee('Reset password');
});

test('an incorrect recovery code cannot open the password reset form', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    $this->post(route('password.otp.verify'), ['code' => '000000'])
        ->assertSessionHasErrors('code');
});

test('password can be reset after recovery verification', function () {
    $user = User::factory()->create();
    $token = Password::broker()->createToken($user);
    DB::table('sessions')->insert([
        'id' => 'existing-account-session',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Feature test',
        'payload' => 'test-payload',
        'last_activity' => now()->timestamp,
    ]);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'Password1',
        'password_confirmation' => 'Password1',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);
});
