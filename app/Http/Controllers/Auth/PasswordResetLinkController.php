<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->string('email')->toString();
        $user = User::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->first();

        $request->session()->regenerate();
        $otp->issue(
            $request,
            EmailOtpService::RECOVERY_SESSION_KEY,
            $user,
            'password-recovery',
            $email
        );

        return redirect()->route('password.otp.show')->with(
            'status',
            'If an account matches that email, a six-digit verification code has been sent.'
        );
    }
}
