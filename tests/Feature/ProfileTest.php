<?php

use App\Models\User;
use App\Models\Loan;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk()
        ->assertSee('Personal profile')
        ->assertSee('First name')
        ->assertSee('Last name')
        ->assertDontSee('Update password');

    $this->actingAs($user)
        ->get(route('profile.verification.edit'))
        ->assertOk()
        ->assertSee('Client verification');

    $this->actingAs($user)
        ->get(route('profile.security.edit'))
        ->assertOk()
        ->assertSee('Update password')
        ->assertSee('Delete account');
});

test('required personal information cannot be cleared from a profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patch('/profile', [
        'first_name' => '',
        'last_name' => '',
        'email' => $user->email,
        'contact_number' => '',
        'age' => '',
        'address' => '',
    ])->assertSessionHasErrors(['first_name', 'last_name', 'contact_number', 'age', 'address']);
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'first_name' => 'Updated',
            'last_name' => 'Client',
            'email' => 'test@example.com',
            'contact_number' => '09171234567',
            'age' => 26,
            'address' => '123 Test Street, Manila',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Updated', $user->first_name);
    $this->assertSame('Client', $user->last_name);
    $this->assertSame('Updated Client', $user->name);
    $this->assertSame('Updated Client', $user->full_name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $user->email,
            'contact_number' => '09171234567',
            'age' => 26,
            'address' => '123 Test Street, Manila',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('user cannot delete their account when an existing loan belongs to it', function () {
    $user = User::factory()->create();

    Loan::create([
        'user_id' => $user->id,
        'amount' => 1000,
        'total_payable' => 1100,
        'paid_amount' => 0,
        'status' => Loan::STATUS_PENDING,
    ]);

    $this->actingAs($user)
        ->get(route('profile.security.edit'))
        ->assertOk()
        ->assertSee('Account deletion is locked')
        ->assertDontSee('Confirm your password');

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'account_deletion')
        ->assertRedirect(route('profile.security.edit'));

    $this->assertAuthenticatedAs($user);
    $this->assertNotNull($user->fresh());
    $this->assertDatabaseHas('loans', ['user_id' => $user->id]);
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
