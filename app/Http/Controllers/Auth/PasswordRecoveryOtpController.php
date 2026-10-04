<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordRecoveryOtpController extends Controller
{
    public function show(Request $request, EmailOtpService $otp): View|RedirectResponse
    {
        $challenge = $otp->challenge($request, EmailOtpService::RECOVERY_SESSION_KEY);

        if (! $challenge) {
            return redirect()->route('password.request');
        }

        return view('auth.otp-challenge', [
            'title' => 'Verify password recovery',
            'eyebrow' => 'Password recovery',
            'description' => 'Enter the six-digit code sent to '.$otp->maskedEmail($challenge['email'] ?? null).'.',
            'verifyRoute' => route('password.otp.verify'),
            'resendRoute' => route('password.otp.resend'),
            'backRoute' => route('password.request'),
            'backLabel' => 'Use another email',
        ]);
    }

    public function verify(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $result = $otp->verify($request, EmailOtpService::RECOVERY_SESSION_KEY, $validated['code']);

        if ($result !== 'valid') {
            return back()->withErrors(['code' => $this->errorMessage($result)]);
        }

        $challenge = $otp->challenge($request, EmailOtpService::RECOVERY_SESSION_KEY);
        $user = User::query()->whereKey($challenge['user_id'] ?? null)->where('is_active', true)->first();

        if (! $user) {
            return back()->withErrors(['code' => 'The verification code is incorrect.']);
        }

        $token = Password::broker()->createToken($user);
        $otp->clear($request, EmailOtpService::RECOVERY_SESSION_KEY);

        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);
    }

    public function resend(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $challenge = $otp->challenge($request, EmailOtpService::RECOVERY_SESSION_KEY);

        if (! $challenge) {
            return redirect()->route('password.request');
        }

        $user = User::query()->whereKey($challenge['user_id'] ?? null)->where('is_active', true)->first();
        $otp->issue(
            $request,
            EmailOtpService::RECOVERY_SESSION_KEY,
            $user,
            'password-recovery',
            (string) ($challenge['email'] ?? '')
        );

        return back()->with('status', 'If the email matches an account, a new verification code has been sent.');
    }

    private function errorMessage(string $result): string
    {
        return match ($result) {
            'expired' => 'This code has expired. Request a new code and try again.',
            'locked' => 'Too many incorrect attempts. Request a new code to continue.',
            'missing' => 'Your recovery session has ended. Start the password recovery process again.',
            default => 'The verification code is incorrect.',
        };
    }
}
