<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $request->user()->load('clientVerification');

        return view('profile.edit', [
            'user' => $request->user(),
            'streetAddress' => $request->user()->street_address ?: $request->user()->address,
            'barangay' => $request->user()->barangay,
            'cityMunicipality' => $request->user()->city_municipality,
            'province' => $request->user()->province,
        ]);
    }

    /**
     * Display the client's verification settings.
     */
    public function verification(Request $request): View
    {
        $request->user()->load('clientVerification');

        return view('profile.verification', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display password and account security settings.
     */
    public function security(): View
    {
        return view('profile.security');
    }

    /**
     * Display motion accessibility settings.
     */
    public function motion(Request $request): View
    {
        return view('profile.motion', [
            'reduceMotion' => (bool) data_get($request->user()->ui_preferences, 'reduce_motion', false),
        ]);
    }

    /**
     * Update the client's motion accessibility setting.
     */
    public function updateMotion(Request $request): RedirectResponse
    {
        $request->validate([
            'reduce_motion' => ['nullable', 'boolean'],
        ]);

        $request->user()->forceFill([
            'ui_preferences' => [
                ...($request->user()->ui_preferences ?? []),
                'reduce_motion' => $request->boolean('reduce_motion'),
            ],
        ])->save();

        return Redirect::route('profile.motion.edit')->with('status', 'motion-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, EmailOtpService $otp): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['profile_photo']);
        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);
        $validated['street_address'] = trim($validated['street_address']);
        $validated['address'] = implode(', ', [
            $validated['street_address'],
            $validated['barangay'],
            $validated['city_municipality'],
            $validated['province'],
        ]);

        $previousPhoto = $request->user()->profile_photo_path;

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', config('filesystems.public_disk'));
        }

        $request->user()->fill($validated);

        $emailChanged = $request->user()->isDirty('email');
        $requiresEmailVerification = $emailChanged && ! $request->user()->isAdministrator();

        if ($requiresEmailVerification) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if (isset($validated['profile_photo_path']) && $previousPhoto) {
            Storage::disk(config('filesystems.public_disk'))->delete($previousPhoto);
        }

        if ($requiresEmailVerification) {
            $otp->issue(
                $request,
                EmailOtpService::EMAIL_VERIFICATION_SESSION_KEY,
                $request->user(),
                'email-verification',
                $request->user()->email
            );

            return Redirect::route('verification.notice')
                ->with('status', 'verification-code-sent');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Display the authenticated client's profile photo.
     */
    public function photo(Request $request): StreamedResponse
    {
        $path = $request->user()->profile_photo_path;

        $disk = Storage::disk(config('filesystems.public_disk'));

        abort_unless($path && $disk->exists($path), 404);

        return $disk->response($path, null, [
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
