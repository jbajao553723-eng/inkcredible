<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ContainsNumberOrSymbol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.settings.edit', [
            'user' => $request->user(),
            'reduceMotion' => (bool) data_get($request->user()->ui_preferences, 'reduce_motion', false),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $emailChanged = Str::lower((string) $request->input('email')) !== Str::lower($user->email);

        $validated = $request->validateWithBag('profile', [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'contact_number' => ['required', 'string', 'max:30'],
            'current_password' => [$emailChanged ? 'required' : 'nullable', 'current_password'],
        ]);

        unset($validated['current_password']);
        $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);

        $user->fill($validated)->save();

        return redirect()->route('admin.settings.edit', ['section' => 'profile'])
            ->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8), new ContainsNumberOrSymbol],
        ]);

        $user = $request->user();
        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        if (Schema::hasTable('sessions')) {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return redirect()->route('admin.settings.edit', ['section' => 'security'])
            ->with('status', 'password-updated');
    }

    public function updateMotion(Request $request): RedirectResponse
    {
        $request->validateWithBag('motion', [
            'reduce_motion' => ['nullable', 'boolean'],
        ]);

        $request->user()->forceFill([
            'ui_preferences' => [
                'reduce_motion' => $request->boolean('reduce_motion'),
            ],
        ])->save();

        return redirect()->route('admin.settings.edit', ['section' => 'motion'])
            ->with('status', 'motion-updated');
    }
}
