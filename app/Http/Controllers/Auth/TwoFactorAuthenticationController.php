<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorAuthenticationController extends Controller
{
    public function enable(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasTwoFactorAuthenticationEnabled()) {
            return $this->settingsRedirect($user)->with('status', 'two-factor-already-enabled');
        }

        $challenge = $otp->challenge($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY);
        if ($challenge && (int) ($challenge['user_id'] ?? 0) === (int) $user->id && ($challenge['email'] ?? '') === $user->email && ($challenge['expires_at'] ?? 0) > now()->timestamp && $otp->resendSeconds('two-factor-setup', $user->email) > 0) {
            return redirect()->route('two-factor.setup.show');
        }

        try {
            $otp->issue(
                $request,
                EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY,
                $user,
                'two-factor-setup',
                $user->email
            );
        } catch (ValidationException $exception) {
            return $this->settingsRedirect($user)->withErrors(['two_factor' => $exception->errors()['code'][0]]);
        }

        return redirect()->route('two-factor.setup.show');
    }

    public function showSetup(Request $request, EmailOtpService $otp): View|RedirectResponse
    {
        $challenge = $otp->challenge($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY);

        if (! $challenge || (int) ($challenge['user_id'] ?? 0) !== (int) $request->user()->getKey()) {
            return $this->settingsRedirect($request->user())
                ->withErrors(['two_factor' => 'Start two-factor setup again to receive a new code.']);
        }

        return view('auth.otp-challenge', [
            'title' => 'Enable two-factor authentication',
            'eyebrow' => 'Account security',
            'description' => 'Enter the six-digit code sent to '.$otp->maskedEmail($request->user()->email).'.',
            'verifyRoute' => route('two-factor.setup.verify'),
            'resendRoute' => route('two-factor.setup.resend'),
            'backRoute' => $this->settingsUrl($request->user()),
            'backLabel' => 'Back to security settings',
            'resendAvailableAt' => $challenge['resend_available_at'] ?? 0,
        ]);
    }

    public function verifySetup(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $validated = $request->validate(['code' => ['required', 'digits:6']]);
        $challenge = $otp->challenge($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY);

        if (! $challenge || (int) ($challenge['user_id'] ?? 0) !== (int) $request->user()->getKey()) {
            $otp->clear($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY);

            return $this->settingsRedirect($request->user())
                ->withErrors(['two_factor' => 'This two-factor setup request is no longer valid.']);
        }

        if (($challenge['email'] ?? '') !== $request->user()->email) {
            $otp->clear($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY);

            return $this->settingsRedirect($request->user())->withErrors(['two_factor' => 'Your email changed. Start setup again.']);
        }

        $result = $otp->verify($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY, $validated['code']);

        if ($result !== 'valid') {
            return back()->withErrors(['code' => $this->errorMessage($result)])->withInput();
        }

        $user = $request->user();
        $preferences = $user->ui_preferences ?? [];
        data_set($preferences, 'security.two_factor_enabled_at', now()->toIso8601String());
        $user->forceFill(['ui_preferences' => $preferences, 'remember_token' => Str::random(60)])->save();
        $this->deleteOtherSessions($request, $user);
        $otp->clear($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY);

        return $this->settingsRedirect($request->user())->with('status', 'two-factor-enabled');
    }

    public function resendSetup(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $user = $request->user();
        $challenge = $otp->challenge($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY);

        if (! $challenge || (int) ($challenge['user_id'] ?? 0) !== (int) $user->getKey()) {
            return $this->settingsRedirect($user)
                ->withErrors(['two_factor' => 'Start two-factor setup again before requesting a new code.']);
        }

        $otp->issue($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY, $user, 'two-factor-setup', $user->email);

        return back()->with('status', 'A new two-factor confirmation code has been sent.');
    }

    public function disable(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $user = $request->user();
        $preferences = $user->ui_preferences ?? [];
        data_forget($preferences, 'security.two_factor_enabled_at');
        $user->forceFill([
            'ui_preferences' => $preferences,
            'remember_token' => Str::random(60),
        ])->save();

        $otp->clear($request, EmailOtpService::TWO_FACTOR_SETUP_SESSION_KEY);
        $this->deleteOtherSessions($request, $user);

        return $this->settingsRedirect($user)->with('status', 'two-factor-disabled');
    }

    public function showLogin(Request $request, EmailOtpService $otp): View|RedirectResponse
    {
        $challenge = $otp->challenge($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY);

        if (! $challenge) {
            return redirect()->route('login');
        }

        return view('auth.otp-challenge', [
            'title' => 'Verify your sign-in',
            'eyebrow' => 'Two-factor authentication',
            'description' => 'Enter the six-digit code sent to '.$otp->maskedEmail($challenge['email'] ?? null).'.',
            'verifyRoute' => route('two-factor.login.verify'),
            'resendRoute' => route('two-factor.login.resend'),
            'backRoute' => route('login'),
            'backLabel' => 'Back to sign in',
            'resendAvailableAt' => $challenge['resend_available_at'] ?? 0,
        ]);
    }

    public function verifyLogin(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $validated = $request->validate(['code' => ['required', 'digits:6']]);
        $challenge = $otp->challenge($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY);

        if (! $challenge) {
            $otp->clear($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY);

            return redirect()->route('login')->withErrors(['email' => 'This sign-in verification request is no longer valid.']);
        }

        $user = User::query()->whereKey($challenge['user_id'] ?? null)->where('is_active', true)->first();

        if (! $user || ! $user->hasTwoFactorAuthenticationEnabled() || $user->email !== ($challenge['email'] ?? '') || ! hash_equals(hash('sha256', $user->password), (string) data_get($challenge, 'context.password_version', ''))) {
            $otp->clear($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY);

            return redirect()->route('login')->withErrors(['email' => 'This sign-in verification request is no longer valid.']);
        }

        $result = $otp->verify($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY, $validated['code']);

        if ($result !== 'valid') {
            return back()->withErrors(['code' => $this->errorMessage($result)])->withInput();
        }

        $remember = (bool) data_get($challenge, 'context.remember', false);
        $otp->clear($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY);
        Auth::guard('web')->login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function resendLogin(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $challenge = $otp->challenge($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY);

        if (! $challenge) {
            return redirect()->route('login');
        }

        $user = User::query()->whereKey($challenge['user_id'] ?? null)->where('is_active', true)->first();

        if (! $user || ! $user->hasTwoFactorAuthenticationEnabled() || $user->email !== ($challenge['email'] ?? '') || ! hash_equals(hash('sha256', $user->password), (string) data_get($challenge, 'context.password_version', ''))) {
            $otp->clear($request, EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY);

            return redirect()->route('login');
        }

        $otp->issue(
            $request,
            EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY,
            $user,
            'two-factor-login',
            $user->email,
            ['remember' => (bool) data_get($challenge, 'context.remember', false), 'password_version' => hash('sha256', $user->password)]
        );

        return back()->with('status', 'A new sign-in code has been sent.');
    }

    private function errorMessage(string $result): string
    {
        return match ($result) {
            'expired' => 'This code has expired. Request a new code and try again.',
            'locked' => 'Too many incorrect attempts. Request a new code to continue.',
            'missing' => 'Your verification session has ended. Start again to receive a new code.',
            default => 'The verification code is incorrect.',
        };
    }

    private function settingsRedirect(User $user): RedirectResponse
    {
        return redirect()->to($this->settingsUrl($user));
    }

    private function settingsUrl(User $user): string
    {
        return $user->isAdministrator()
            ? route('admin.settings.edit', ['section' => 'security'])
            : route('profile.security.edit');
    }

    private function deleteOtherSessions(Request $request, User $user): void
    {
        if (! Schema::hasTable('sessions')) {
            return;
        }

        DB::table('sessions')
            ->where('user_id', $user->getKey())
            ->where('id', '!=', $request->session()->getId())
            ->delete();
    }
}
