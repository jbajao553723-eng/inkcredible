<?php

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/profile');

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
        ->assertDontSee('Delete account');

    $this->actingAs($user)
        ->get(route('profile.motion.edit'))
        ->assertOk()
        ->assertSee('Motion accessibility')
        ->assertSee('Reduce motion');
});

test('a client can reduce motion across the client workspace', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patch(route('profile.motion.update'), [
        'reduce_motion' => '1',
    ])->assertRedirect(route('profile.motion.edit'));

    expect($user->fresh()->ui_preferences)->toBe(['reduce_motion' => true]);

    $this->actingAs($user->fresh())->get(route('dashboard'))
        ->assertOk()
        ->assertSee('transition-duration:.01ms', false);

    $this->actingAs($user->fresh())->patch(route('profile.motion.update'))
        ->assertRedirect(route('profile.motion.edit'));

    expect($user->fresh()->ui_preferences)->toBe(['reduce_motion' => false]);
});

test('required personal information cannot be cleared from a profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patch('/profile', [
        'first_name' => '',
        'last_name' => '',
        'email' => $user->email,
        'contact_number' => '',
        'age' => '',
        'street_address' => '',
        'barangay' => '',
        'city_municipality' => '',
        'province' => '',
    ])->assertSessionHasErrors(['first_name', 'last_name', 'contact_number', 'age', 'street_address', 'barangay', 'city_municipality', 'province']);
});

test('profile information can be updated', function () {
    Notification::fake();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch('/profile', [
        'first_name' => 'Updated',
        'last_name' => 'Client',
        'email' => 'test@example.com',
        'contact_number' => '09171234567',
        'age' => 26,
        'street_address' => $user->street_address,
        'barangay' => $user->barangay,
        'city_municipality' => $user->city_municipality,
        'province' => $user->province,
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect(route('verification.notice'));

    $user->refresh();

    $this->assertSame('Updated', $user->first_name);
    $this->assertSame('Client', $user->last_name);
    $this->assertSame('Updated Client', $user->name);
    $this->assertSame('Updated Client', $user->full_name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);

    Notification::assertSentTo($user, EmailOtpNotification::class, fn (EmailOtpNotification $notification) => $notification->purpose === 'email-verification'
    );
});

test('profile address settings use the same fields as account creation and update the dashboard identity', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->patch(route('profile.update'), [
        'first_name' => 'Dashboard',
        'last_name' => 'Client',
        'email' => $user->email,
        'contact_number' => '09171234567',
        'age' => 27,
        'street_address' => '42 Example Street',
        'barangay' => 'Barangay Uno',
        'city_municipality' => 'Batangas City',
        'province' => 'Batangas',
    ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

    expect($user->fresh()->address)->toBe('42 Example Street, Barangay Uno, Batangas City, Batangas')
        ->and($user->fresh()->street_address)->toBe('42 Example Street')
        ->and($user->fresh()->barangay)->toBe('Barangay Uno')
        ->and($user->fresh()->city_municipality)->toBe('Batangas City')
        ->and($user->fresh()->province)->toBe('Batangas')
        ->and($user->fresh()->name)->toBe('Dashboard Client');

    $this->actingAs($user->fresh())->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Welcome back, Dashboard')
        ->assertSee('Update dashboard profile');
});

test('a client can upload and privately view a profile picture', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $image = UploadedFile::fake()->createWithContent(
        'profile.png',
        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
    );

    $this->actingAs($user)->patch('/profile', [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'contact_number' => $user->contact_number,
        'age' => $user->age,
        'street_address' => $user->street_address,
        'barangay' => $user->barangay,
        'city_municipality' => $user->city_municipality,
        'province' => $user->province,
        'profile_photo' => $image,
    ])->assertSessionHasNoErrors()->assertRedirect('/profile');

    $photoPath = $user->refresh()->profile_photo_path;

    expect($photoPath)->not->toBeNull();
    Storage::disk('public')->assertExists($photoPath);

    $this->actingAs($user)->get(route('profile.photo'))->assertOk();
});

test('admins can view client profile pictures from the client workspace', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $client->update(['profile_photo_path' => 'profile-photos/client.png']);
    Storage::disk('public')->put($client->profile_photo_path, 'profile-image');

    $this->actingAs($admin)
        ->get(route('admin.clients.photo', $client))
        ->assertOk();

    $this->actingAs($client)
        ->get(route('admin.clients.photo', $client))
        ->assertForbidden();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch('/profile', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => $user->email,
        'contact_number' => '09171234567',
        'age' => 26,
        'street_address' => $user->street_address,
        'barangay' => $user->barangay,
        'city_municipality' => $user->city_municipality,
        'province' => $user->province,
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('clients cannot delete their account', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete('/profile', [
        'password' => 'password',
    ]);

    $response->assertStatus(405);
    $this->assertAuthenticatedAs($user);
    $this->assertNotNull($user->fresh());
});
