<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmailOtpService
{
    public const RECOVERY_SESSION_KEY = 'security.recovery_otp';

    public const EMAIL_VERIFICATION_SESSION_KEY = 'security.email_verification_otp';

    public const TWO_FACTOR_LOGIN_SESSION_KEY = 'security.two_factor_login_otp';

    public const TWO_FACTOR_SETUP_SESSION_KEY = 'security.two_factor_setup_otp';

    public const EXPIRES_IN_MINUTES = 10;

    public const MAX_ATTEMPTS = 5;

    /**
     * Create and email a new one-time code while storing only its hash.
     *
     * @param  array<string, mixed>  $context
     */
    public function issue(Request $request, string $sessionKey, ?User $user, string $purpose, string $email, array $context = []): void
    {
        $code = (string) random_int(100000, 999999);

        $request->session()->put($sessionKey, [
            'user_id' => $user?->getKey(),
            'email' => $email,
            'code_hash' => Hash::make($code),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(self::EXPIRES_IN_MINUTES)->timestamp,
            'attempts' => 0,
            'context' => $context,
        ]);

        if ($user) {
            $user->notify(new EmailOtpNotification($code, $purpose));
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function challenge(Request $request, string $sessionKey): ?array
    {
        $challenge = $request->session()->get($sessionKey);

        return is_array($challenge) ? $challenge : null;
    }

    public function verify(Request $request, string $sessionKey, string $code): string
    {
        $challenge = $this->challenge($request, $sessionKey);

        if (! $challenge) {
            return 'missing';
        }

        if (($challenge['expires_at'] ?? 0) < now()->timestamp) {
            $request->session()->forget($sessionKey);

            return 'expired';
        }

        if (($challenge['attempts'] ?? 0) >= self::MAX_ATTEMPTS) {
            return 'locked';
        }

        if (! Hash::check($code, (string) ($challenge['code_hash'] ?? ''))) {
            $challenge['attempts'] = ($challenge['attempts'] ?? 0) + 1;
            $request->session()->put($sessionKey, $challenge);

            return $challenge['attempts'] >= self::MAX_ATTEMPTS ? 'locked' : 'invalid';
        }

        return 'valid';
    }

    public function clear(Request $request, string $sessionKey): void
    {
        $request->session()->forget($sessionKey);
    }

    public function maskedEmail(?string $email): string
    {
        if (! $email || ! str_contains($email, '@')) {
            return 'your registered email';
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = mb_substr($local, 0, 1);

        return $visible.str_repeat('*', max(3, min(8, mb_strlen($local) - 1))).'@'.$domain;
    }
}
