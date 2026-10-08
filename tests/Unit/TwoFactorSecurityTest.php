<?php

namespace Tests\Unit;

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use App\Services\EmailOtpService;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TwoFactorSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'session.driver' => 'array']);
        DB::purge('sqlite');
        // Isolated SQLite database; MySQL-only views, trigger, and enum changes are unrelated to authentication.
        foreach (glob(database_path('migrations/*.php')) as $path) {
            if (preg_match('/create_loan_payment_trigger|create_database_views|update_loan_applications_status_enum/', $path)) {
                continue;
            }
            (require $path)->up();
        }
        Notification::fake();
    }

    public function test_switch_confirms_email_without_asking_for_password_and_can_turn_off(): void
    {
        foreach ([User::ROLE_CLIENT, User::ROLE_ADMIN, User::ROLE_SUPERADMIN] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->post(route('two-factor.enable'))->assertRedirect(route('two-factor.setup.show'));
            $code = Notification::sent($user, EmailOtpNotification::class)->last()->code;
            $this->post(route('two-factor.setup.verify'), ['code' => $code])->assertSessionHas('status', 'two-factor-enabled');
            $this->assertTrue($user->fresh()->hasTwoFactorAuthenticationEnabled());
            $this->delete(route('two-factor.disable'))->assertSessionHas('status', 'two-factor-disabled');
            $this->assertFalse($user->fresh()->hasTwoFactorAuthenticationEnabled());
        }
    }

    public function test_login_requires_the_emailed_code_and_blocks_early_resend(): void
    {
        $user = User::factory()->create(['password' => 'Password1!', 'ui_preferences' => ['security' => ['two_factor_enabled_at' => now()->toIso8601String()]]]);
        $this->post(route('login'), ['email' => $user->email, 'password' => 'Password1!'])->assertRedirect(route('two-factor.login.show'));
        $this->assertGuest();
        $firstCode = Notification::sent($user, EmailOtpNotification::class)->last()->code;
        $this->get(route('two-factor.login.show'))->assertOk()->assertSee('Resend code')->assertSee('every five minutes');
        $this->post(route('two-factor.login.resend'))->assertSessionHasErrors('code');
        $this->assertCount(1, Notification::sent($user, EmailOtpNotification::class));
        $this->travel(301)->seconds();
        $this->post(route('two-factor.login.resend'))->assertSessionHas('status', 'A new sign-in code has been sent.');
        $this->assertCount(2, Notification::sent($user, EmailOtpNotification::class));
        $newCode = Notification::sent($user, EmailOtpNotification::class)->last()->code;
        if ($newCode !== $firstCode) {
            $this->post(route('two-factor.login.verify'), ['code' => $firstCode])->assertSessionHasErrors('code');
        }
        $this->post(route('two-factor.login.verify'), ['code' => $newCode])->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertNull(session(EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY));
    }

    public function test_mail_failure_never_leaves_a_user_signed_in(): void
    {
        $user = User::factory()->create(['password' => 'Password1!', 'ui_preferences' => ['security' => ['two_factor_enabled_at' => now()->toIso8601String()]]]);
        Notification::swap(\Mockery::mock(Dispatcher::class, function ($mock) {
            $mock->shouldReceive('sendNow')->andThrow(new \RuntimeException('SMTP unavailable'));
        }));
        $this->post(route('login'), ['email' => $user->email, 'password' => 'Password1!'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNull(session(EmailOtpService::TWO_FACTOR_LOGIN_SESSION_KEY));
        $this->assertSame(0, app(EmailOtpService::class)->resendSeconds('two-factor-login', $user->email));
    }

    public function test_accessibility_changes_preserve_two_factor_for_clients_and_admins(): void
    {
        foreach ([User::ROLE_CLIENT, User::ROLE_ADMIN] as $role) {
            $user = User::factory()->create(['role' => $role, 'ui_preferences' => ['security' => ['two_factor_enabled_at' => now()->toIso8601String()]]]);
            $route = $role === User::ROLE_CLIENT ? 'profile.motion.update' : 'admin.settings.motion.update';
            $this->actingAs($user)->patch(route($route), ['reduce_motion' => '1'])->assertSessionHas('status', 'motion-updated');
            $this->assertTrue($user->fresh()->hasTwoFactorAuthenticationEnabled());
            $this->assertTrue($user->fresh()->ui_preferences['reduce_motion']);
        }
    }

    public function test_settings_show_switches_and_admin_dashboard_renders(): void
    {
        $client = User::factory()->create();
        $this->actingAs($client)->get(route('profile.security.edit'))->assertOk()->assertSee('role="switch"', false)->assertDontSee('enable-two-factor-password');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)->get(route('admin.settings.edit', ['section' => 'security']))->assertOk()->assertSee('role="switch"', false)->assertDontSee('admin-enable-two-factor-password');
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Pending work')->assertSee('Portfolio balance')->assertSee('Projected earnings');
    }

    public function test_expired_and_wrong_codes_do_not_authenticate(): void
    {
        $user = User::factory()->create(['password' => 'Password1!', 'ui_preferences' => ['security' => ['two_factor_enabled_at' => now()->toIso8601String()]]]);
        $this->post(route('login'), ['email' => $user->email, 'password' => 'Password1!']);
        $code = Notification::sent($user, EmailOtpNotification::class)->last()->code;
        $this->post(route('two-factor.login.verify'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertGuest();
        $this->travel(601)->seconds();
        $this->post(route('two-factor.login.verify'), ['code' => $code])->assertSessionHasErrors('code');
        $this->assertGuest();
        $this->post(route('two-factor.login.resend'))->assertSessionHas('status', 'A new sign-in code has been sent.');
    }
}
