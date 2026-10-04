<?php

use App\Models\ClientVerification;
use App\Models\User;
use App\Notifications\ClientVerificationApprovedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

function verificationSignatureDataUri(): string
{
    $image = imagecreatetruecolor(720, 220);
    $white = imagecolorallocate($image, 255, 255, 255);
    $ink = imagecolorallocate($image, 20, 30, 50);
    imagefill($image, 0, 0, $white);
    imagesetthickness($image, 5);
    imageline($image, 90, 145, 250, 65, $ink);
    imageline($image, 250, 65, 420, 150, $ink);
    imageline($image, 420, 150, 630, 80, $ink);
    ob_start();
    imagepng($image);
    $png = ob_get_clean();
    imagedestroy($image);

    return 'data:image/png;base64,'.base64_encode($png);
}

it('captures and encrypts a digital signature during client verification', function () {
    Notification::fake();
    Storage::fake('local');
    $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $signature = verificationSignatureDataUri();

    $this->actingAs($client)->post(route('profile.verification.store'), [
        'employment_status' => 'employed',
        'company_name' => 'Inkcredible Test Co.',
        'job_title' => 'Analyst',
        'monthly_income' => 30000,
        'employment_length_months' => 24,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'Philippine National ID',
        'valid_id_number' => 'ID-12345',
        'valid_id' => UploadedFile::fake()->image('id.png'),
        'selfie_with_id' => UploadedFile::fake()->image('selfie.png'),
        'payslip' => UploadedFile::fake()->create('payslip.pdf', 120, 'application/pdf'),
        'digital_signature' => $signature,
    ])->assertSessionHasNoErrors();

    $verification = $client->fresh()->clientVerification;

    expect($verification->digital_signature)->toBe($signature)
        ->and($verification->signature_captured_at)->not->toBeNull()
        ->and($verification->status)->toBe(ClientVerification::STATUS_PENDING);

    expect(DB::table('client_verifications')->where('id', $verification->id)->value('digital_signature'))
        ->not->toBe($signature);
    Storage::disk('local')->assertExists($verification->payslip_path);

    $this->actingAs($admin)->post(route('admin.verifications.approve', $verification))
        ->assertSessionHas('success');

    expect($verification->fresh()->payslip_verified_at)->not->toBeNull()
        ->and($verification->fresh()->status)->toBe(ClientVerification::STATUS_APPROVED);

    Notification::assertSentTo(
        $client,
        ClientVerificationApprovedNotification::class,
        fn (ClientVerificationApprovedNotification $notification) => in_array('mail', $notification->via($client), true)
            && in_array('database', $notification->via($client), true)
            && $notification->toMail($client)->subject === 'Your Inkcredible account is fully verified'
            && $notification->toMail($client)->salutation === 'Regards, Inkcredible Lending Company'
    );
});

it('rejects a blank digital signature image', function () {
    Storage::fake('local');
    $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
    $image = imagecreatetruecolor(720, 220);
    $white = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $white);
    ob_start();
    imagepng($image);
    $blankSignature = 'data:image/png;base64,'.base64_encode(ob_get_clean());
    imagedestroy($image);

    $this->actingAs($client)->post(route('profile.verification.store'), [
        'employment_status' => 'employed',
        'company_name' => 'Test Co.',
        'job_title' => 'Analyst',
        'monthly_income' => 30000,
        'employment_length_months' => 24,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'Philippine National ID',
        'valid_id_number' => 'ID-12345',
        'valid_id' => UploadedFile::fake()->image('id.png'),
        'selfie_with_id' => UploadedFile::fake()->image('selfie.png'),
        'digital_signature' => $blankSignature,
    ])->assertSessionHasErrors(['digital_signature'], null, 'verification');
});

it('lets a previously verified client add a missing signature once', function () {
    $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
    $verification = ClientVerification::create([
        'user_id' => $client->id,
        'status' => ClientVerification::STATUS_APPROVED,
        'employment_status' => 'employed',
        'monthly_income' => 30000,
        'employment_length_months' => 24,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'National ID',
        'valid_id_number' => 'LEGACY-1',
        'valid_id_path' => 'test/id.png',
        'selfie_with_id_path' => 'test/selfie.png',
        'submitted_at' => now(),
    ]);

    $this->actingAs($client)->post(route('profile.verification.signature.store'), [
        'digital_signature' => verificationSignatureDataUri(),
    ])->assertSessionHasNoErrors();

    expect($verification->fresh()->digital_signature)->not->toBeNull();

    $this->actingAs($client)->post(route('profile.verification.signature.store'), [
        'digital_signature' => verificationSignatureDataUri(),
    ])->assertSessionHas('error');
});

it('returns an updated payslip to administrator review before applying the score bonus', function () {
    Storage::fake('local');
    $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
    $verification = ClientVerification::create([
        'user_id' => $client->id,
        'status' => ClientVerification::STATUS_APPROVED,
        'employment_status' => 'employed',
        'monthly_income' => 30000,
        'employment_length_months' => 24,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'National ID',
        'valid_id_number' => 'PAYSLIP-1',
        'valid_id_path' => 'test/id.png',
        'selfie_with_id_path' => 'test/selfie.png',
        'digital_signature' => verificationSignatureDataUri(),
        'signature_captured_at' => now(),
        'submitted_at' => now(),
    ]);

    $this->actingAs($client)->post(route('profile.verification.payslip.store'), [
        'payslip' => UploadedFile::fake()->create('new-payslip.pdf', 100, 'application/pdf'),
    ])->assertSessionHasNoErrors();

    $verification->refresh();

    expect($verification->status)->toBe(ClientVerification::STATUS_PENDING)
        ->and($verification->payslip_path)->not->toBeNull()
        ->and($verification->payslip_verified_at)->toBeNull();
    Storage::disk('local')->assertExists($verification->payslip_path);
});
