<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ContainsNumberOrSymbol;
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
            'password' => ['required', 'confirmed', Rules\Password::min(8), new ContainsNumberOrSymbol],
            'terms_accepted' => ['accepted'],

            'contact_number' => ['required', 'string', 'max:30'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'address' => ['nullable', 'string', 'max:500', 'required_without_all:street_address,address_location'],
            'street_address' => ['nullable', 'string', 'max:255', 'required_with:address_location'],
            'address_location' => ['nullable', 'string', 'max:100', 'in:'.implode(',', config('philippine_locations')), 'required_with:street_address'],
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
            'address' => $request->filled('street_address')
                ? trim($request->street_address).', '.$request->address_location
                : $request->address,
            'terms_accepted_at' => now(),
            'terms_version' => config('legal.account_terms_version'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
