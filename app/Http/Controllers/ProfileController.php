<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
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
    public function security(Request $request): View
    {
        return view('profile.security', [
            'user' => $request->user(),
            'hasExistingLoans' => $request->user()->loans()->exists(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->loans()->exists()) {
            return Redirect::route('profile.security.edit')->withErrors([
                'account_deletion' => 'Your account cannot be deleted while it has existing loan records.',
            ], 'userDeletion');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
