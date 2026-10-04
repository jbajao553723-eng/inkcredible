<?php

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Support\Facades\Notification;

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
    Notification::fake();

    $response = $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'contact_number' => '09171234567',
        'age' => 25,
        'street_address' => '123 Test Street',
        'barangay' => 'Barangay 1',
        'city_municipality' => 'City of Manila',
        'province' => 'Metro Manila',
        'password' => 'Password1',
        'password_confirmation' => 'Password1',
        'terms_accepted' => '1',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('verification.notice', absolute: false));

    $user = User::where('email', 'test@example.com')->sole();
    expect($user->first_name)->toBe('Test')
        ->and($user->last_name)->toBe('User')
        ->and($user->name)->toBe('Test User')
        ->and($user->terms_accepted_at)->not->toBeNull()
        ->and($user->terms_version)->toBe(config('legal.account_terms_version'))
        ->and($user->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, EmailOtpNotification::class, fn (EmailOtpNotification $notification) => $notification->purpose === 'email-verification'
    );
});

test('all registration fields are required', function () {
    $this->post('/register', [
        'email' => 'incomplete@example.com',
        'password' => 'Password1',
        'password_confirmation' => 'Password1',
        'terms_accepted' => '1',
    ])->assertSessionHasErrors(['first_name', 'last_name', 'contact_number', 'age', 'street_address', 'barangay', 'city_municipality', 'province']);

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
        'street_address' => '123 Test Street',
        'barangay' => 'Barangay 1',
        'city_municipality' => 'City of Manila',
        'province' => 'Metro Manila',
        'password' => 'Password1',
        'password_confirmation' => 'Password1',
    ])->assertSessionHasErrors('terms_accepted');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'terms@example.com']);
});

test('registration requires a number or special character in an eight character password', function () {
    $this->post('/register', [
        'first_name' => 'Policy',
        'last_name' => 'Test',
        'email' => 'policy@example.com',
        'contact_number' => '09171234567',
        'age' => 25,
        'street_address' => '12 Rizal Street',
        'barangay' => 'Barangay Uno',
        'city_municipality' => 'Batangas City',
        'province' => 'Batangas',
        'password' => 'abcdefgh',
        'password_confirmation' => 'abcdefgh',
        'terms_accepted' => '1',
    ])->assertSessionHasErrors('password');

    $this->assertGuest();
});

test('registration combines the street input and selected location', function () {
    Notification::fake();

    $this->post('/register', [
        'first_name' => 'Address',
        'last_name' => 'Test',
        'email' => 'address@example.com',
        'contact_number' => '09171234567',
        'age' => 25,
        'street_address' => '12 Rizal Street',
        'barangay' => 'Barangay Uno',
        'city_municipality' => 'Batangas City',
        'province' => 'Batangas',
        'password' => 'Password1',
        'password_confirmation' => 'Password1',
        'terms_accepted' => '1',
    ])->assertRedirect(route('verification.notice', absolute: false));

    expect(User::where('email', 'address@example.com')->value('address'))
        ->toBe('12 Rizal Street, Barangay Uno, Batangas City, Batangas');
});
