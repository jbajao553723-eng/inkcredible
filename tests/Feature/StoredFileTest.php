<?php

use App\Models\ClientVerification;
use App\Models\StoredFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function storedFileTestSignature(): string
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
    $signature = 'data:image/png;base64,'.base64_encode(ob_get_clean());
    imagedestroy($image);

    return $signature;
}

it('stores and retrieves private files from the database disk', function () {
    $disk = Storage::disk('database-private');

    expect($disk->put('client-verifications/7/id.txt', 'identity document'))->toBeTrue()
        ->and($disk->exists('client-verifications/7/id.txt'))->toBeTrue()
        ->and($disk->get('client-verifications/7/id.txt'))->toBe('identity document')
        ->and($disk->size('client-verifications/7/id.txt'))->toBe(17)
        ->and($disk->mimeType('client-verifications/7/id.txt'))->toBe('text/plain');

    $storedFile = StoredFile::query()->sole();

    expect($storedFile->disk)->toBe('private')
        ->and($storedFile->path)->toBe('client-verifications/7/id.txt')
        ->and(base64_decode($storedFile->contents_base64, true))->toBe('identity document');

    $disk->delete('client-verifications/7/id.txt');

    expect($disk->missing('client-verifications/7/id.txt'))->toBeTrue();
});

it('keeps public and private database disks isolated', function () {
    Storage::disk('database-private')->put('shared/name.txt', 'private value');
    Storage::disk('database-public')->put('shared/name.txt', 'public value');

    expect(Storage::disk('database-private')->get('shared/name.txt'))->toBe('private value')
        ->and(Storage::disk('database-public')->get('shared/name.txt'))->toBe('public value')
        ->and(StoredFile::query()->count())->toBe(2);
});

it('submits verification documents through database-backed storage', function () {
    config([
        'filesystems.private_disk' => 'database-private',
        'filesystems.public_disk' => 'database-public',
    ]);

    $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

    $this->actingAs($client)->post(route('profile.verification.store'), [
        'employment_status' => 'employed',
        'company_name' => 'Inkcredible Test Co.',
        'job_title' => 'Analyst',
        'monthly_income' => 30000,
        'employment_length_months' => 24,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'Philippine National ID',
        'valid_id_number' => 'ID-DB-12345',
        'profile_photo' => UploadedFile::fake()->image('profile-photo.jpg', 600, 600),
        'valid_id' => UploadedFile::fake()->image('id.png'),
        'selfie_with_id' => UploadedFile::fake()->image('selfie.png'),
        'payslip' => UploadedFile::fake()->create('payslip.pdf', 120, 'application/pdf'),
        'digital_signature' => storedFileTestSignature(),
    ])->assertSessionHasNoErrors();

    $verification = $client->fresh()->clientVerification;

    expect($verification->status)->toBe(ClientVerification::STATUS_PENDING)
        ->and(Storage::disk('database-private')->exists($verification->valid_id_path))->toBeTrue()
        ->and(Storage::disk('database-private')->exists($verification->selfie_with_id_path))->toBeTrue()
        ->and(Storage::disk('database-private')->exists($verification->payslip_path))->toBeTrue()
        ->and(Storage::disk('database-public')->exists($client->fresh()->profile_photo_path))->toBeTrue()
        ->and(StoredFile::query()->count())->toBe(4);
});

it('rejects a combined verification upload that exceeds the serverless request budget', function () {
    $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

    $this->actingAs($client)->post(route('profile.verification.store'), [
        'employment_status' => 'employed',
        'company_name' => 'Inkcredible Test Co.',
        'job_title' => 'Analyst',
        'monthly_income' => 30000,
        'employment_length_months' => 24,
        'source_of_income' => 'Salary',
        'valid_id_type' => 'Philippine National ID',
        'valid_id_number' => 'ID-LARGE-12345',
        'profile_photo' => UploadedFile::fake()->image('profile.jpg')->size(1300),
        'valid_id' => UploadedFile::fake()->image('id.jpg')->size(1300),
        'selfie_with_id' => UploadedFile::fake()->image('selfie.jpg')->size(1300),
        'digital_signature' => storedFileTestSignature(),
    ])->assertSessionHasErrors(['valid_id'], null, 'verification');

    expect(StoredFile::query()->count())->toBe(0)
        ->and($client->fresh()->clientVerification)->toBeNull();
});

it('renders a helpful response when PHP rejects an oversized verification request', function () {
    $this->from(route('profile.verification.edit'))
        ->withServerVariables(['CONTENT_LENGTH' => 20 * 1024 * 1024])
        ->post(route('profile.verification.store'))
        ->assertStatus(413)
        ->assertSee('Please choose smaller files')
        ->assertSee('Return to verification');
});
