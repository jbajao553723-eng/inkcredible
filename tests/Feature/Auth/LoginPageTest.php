<?php

use App\Models\User;
use Illuminate\Support\Facades\Notification;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200)
        ->assertSee('Welcome back')
        ->assertSee('Keep me signed in')
        ->assertSee('Forgot password?')
        ->assertSee('Create account');
});

test('users authenticate directly with their email and password', function () {
    Notification::fake();
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
    Notification::assertNothingSent();
});

test('administrator roles sign in without otp or email verification', function (string $role) {
    Notification::fake();
    $user = User::factory()->unverified()->create(['role' => $role]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
    $adminDashboard = $this->get(route('admin.dashboard'));
    $role === User::ROLE_SUPERADMIN
        ? $adminDashboard->assertRedirect(route('admin.security.dashboard'))
        : $adminDashboard->assertOk();
    Notification::assertNothingSent();
})->with([User::ROLE_ADMIN, User::ROLE_SUPERADMIN]);

test('users can not authenticate with invalid password', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    Notification::assertNothingSent();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
