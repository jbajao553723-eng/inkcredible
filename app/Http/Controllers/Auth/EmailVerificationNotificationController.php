<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request, EmailOtpService $otp): RedirectResponse
    {
        if ($request->user()->isAdministrator() || $request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $otp->issue(
            $request,
            EmailOtpService::EMAIL_VERIFICATION_SESSION_KEY,
            $request->user(),
            'email-verification',
            $request->user()->email
        );

        return back()->with('status', 'verification-code-sent');
    }
}
