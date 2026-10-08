<?php

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(200);
    $response->assertSee('Enter your verification code')
        ->assertSee('Six-digit verification code');
});

test('email can be verified with an emailed one time code', function () {
    Notification::fake();
    Event::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect()
        ->assertSessionHas('status', 'verification-code-sent');

    $code = null;
    Notification::assertSentTo($user, EmailOtpNotification::class, function (EmailOtpNotification $notification) use (&$code, $user) {
        $code = $notification->code;

        return $notification->purpose === 'email-verification'
            && $notification->toMail($user)->salutation === 'Regards, Inkcredible';
    });

    $this->post(route('verification.otp.verify'), ['code' => $code])
        ->assertRedirect(route('dashboard', ['verified' => 1], false));

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('an incorrect email verification code does not verify the address', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->post(route('verification.send'));

    $this->post(route('verification.otp.verify'), ['code' => '000000'])
        ->assertSessionHasErrors('code');

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('only unverified clients are redirected from protected dashboards', function () {
    $client = User::factory()->unverified()->create();
    $admin = User::factory()->unverified()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($client)->get(route('dashboard'))
        ->assertRedirect(route('verification.notice'));

    $this->actingAs($client)->get(route('payments.index'))
        ->assertRedirect(route('verification.notice'));

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk();

    $this->actingAs($admin)->get(route('admin.settings.edit'))
        ->assertOk();

    $this->actingAs($admin)->get(route('verification.notice'))
        ->assertRedirect(route('dashboard', absolute: false));
});

test('email can be verified', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
