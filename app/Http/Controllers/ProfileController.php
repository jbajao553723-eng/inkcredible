<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['profile_photo']);
        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);

        $previousPhoto = $request->user()->profile_photo_path;

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if (isset($validated['profile_photo_path']) && $previousPhoto) {
            Storage::disk('public')->delete($previousPhoto);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Display the authenticated client's profile photo.
     */
    public function photo(Request $request): StreamedResponse
    {
        $path = $request->user()->profile_photo_path;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
