<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display registration view
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms_accepted' => ['accepted'],

            'contact_number' => ['required', 'string', 'max:30'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'address' => ['required', 'string', 'max:500'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name.' '.$request->last_name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'contact_number' => $request->contact_number,
            'age' => $request->age,
            'address' => $request->address,
            'terms_accepted_at' => now(),
            'terms_version' => config('legal.account_terms_version'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
