<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
        $request->authenticate();

        $user = $request->user();

        if ($user->hasTwoFactorAuthenticationEnabled()) {
            $request->session()->regenerate();
            Auth::guard('web')->logout();
            $challenge = $otp->challenge($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY);
            if ($challenge && (int) ($challenge['user_id'] ?? 0) === (int) $user->id && $otp->resendSeconds('two-factor-login', $user->email) > 0 && ($challenge['expires_at'] ?? 0) > now()->timestamp && hash_equals(hash('sha256', $user->password), (string) data_get($challenge, 'context.password_version', ''))) {
                return redirect()->route('two-factor.login.show');
            }
            try {
                $otp->issue(
                    $request,
                    EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY,
                    $user,
                    'two-factor-login',
                    $user->email,
                    ['remember' => $request->boolean('remember'), 'password_version' => hash('sha256', $user->password)]
                );
            } catch (ValidationException $exception) {
                throw ValidationException::withMessages(['email' => $exception->errors()['code'][0]]);
            }

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
