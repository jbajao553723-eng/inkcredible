<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailOtpService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationOtpController extends Controller
{
    public function verify(Request $request, EmailOtpService $otp): RedirectResponse
    {
        if ($request->user()->isAdministrator()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();
        $challenge = $otp->challenge($request, EmailOtpService::EMAIL_VERIFICATION_SESSION_KEY);

        if (! $challenge
            || (int) ($challenge['user_id'] ?? 0) !== (int) $user->getKey()
            || strcasecmp((string) ($challenge['email'] ?? ''), $user->email) !== 0) {
            $otp->clear($request, EmailOtpService::EMAIL_VERIFICATION_SESSION_KEY);

            return back()->withErrors([
                'code' => 'This verification request is no longer valid. Send a new code to continue.',
            ]);
        }

        $result = $otp->verify(
            $request,
            EmailOtpService::EMAIL_VERIFICATION_SESSION_KEY,
            $validated['code']
        );

        if ($result !== 'valid') {
            return back()->withErrors([
                'code' => match ($result) {
                    'expired' => 'This code has expired. Send a new code to continue.',
                    'locked' => 'Too many incorrect attempts. Send a new code to try again.',
                    default => 'The verification code is incorrect.',
                },
            ])->withInput();
        }

        if (! $user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $otp->clear($request, EmailOtpService::EMAIL_VERIFICATION_SESSION_KEY);

        return redirect(route('dashboard', ['verified' => 1], false))
            ->with('status', 'email-verified');
    }
}
