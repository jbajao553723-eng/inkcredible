<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ContainsNumberOrSymbol;
use App\Services\EmailOtpService;
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
    public function store(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::min(8), new ContainsNumberOrSymbol],
            'terms_accepted' => ['accepted'],

            'contact_number' => ['required', 'string', 'max:30'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:150'],
            'city_municipality' => ['required', 'string', 'max:150'],
            'province' => ['required', 'string', 'max:100', 'in:'.implode(',', config('philippine_locations'))],
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
            'street_address' => trim($request->street_address),
            'barangay' => $request->barangay,
            'city_municipality' => $request->city_municipality,
            'province' => $request->province,
            'address' => implode(', ', [
                trim($request->street_address),
                $request->barangay,
                $request->city_municipality,
                $request->province,
            ]),
            'terms_accepted_at' => now(),
            'terms_version' => config('legal.account_terms_version'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $otp->issue(
            $request,
            EmailOtpService::EMAIL_VERIFICATION_SESSION_KEY,
            $user,
            'email-verification',
            $user->email
        );

        return redirect()->route('verification.notice')
            ->with('status', 'verification-code-sent');
    }
}
