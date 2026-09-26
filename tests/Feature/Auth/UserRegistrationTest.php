<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200)
        ->assertSee('Create your account')
        ->assertSee('First name')
        ->assertSee('Last name')
        ->assertSee('Contact number')
        ->assertSee('Confirm password')
        ->assertSee('Sign in');
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'contact_number' => '09171234567',
        'age' => 25,
        'address' => '123 Test Street, Manila',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms_accepted' => '1',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'test@example.com')->sole();
    expect($user->first_name)->toBe('Test')
        ->and($user->last_name)->toBe('User')
        ->and($user->name)->toBe('Test User')
        ->and($user->terms_accepted_at)->not->toBeNull()
        ->and($user->terms_version)->toBe(config('legal.account_terms_version'));
});

test('all registration fields are required', function () {
    $this->post('/register', [
        'email' => 'incomplete@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms_accepted' => '1',
    ])->assertSessionHasErrors(['first_name', 'last_name', 'contact_number', 'age', 'address']);

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'incomplete@example.com']);
});

test('users must accept the terms before registering', function () {
    $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'terms@example.com',
        'contact_number' => '09171234567',
        'age' => 25,
        'address' => '123 Test Street, Manila',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('terms_accepted');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'terms@example.com']);
});
