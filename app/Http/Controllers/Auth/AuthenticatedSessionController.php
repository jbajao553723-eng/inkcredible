<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, EmailOtpService $otp): RedirectResponse
    {
        $otp->clear($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY);
        $request->authenticate();

        $user = $request->user();

        if ($user->hasTwoFactorAuthenticationEnabled()) {
            $request->session()->regenerate();
            $otp->issue(
                $request,
                EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY,
                $user,
                'two-factor-login',
                $user->email,
                ['remember' => $request->boolean('remember')]
            );
            Auth::guard('web')->logout();

            return redirect()->route('two-factor.login.show');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
