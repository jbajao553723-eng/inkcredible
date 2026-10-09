<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccessibilityPreferencesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'session.driver' => 'array']);
        DB::purge('sqlite');
        foreach (glob(database_path('migrations/*.php')) as $path) {
            if (preg_match('/create_loan_payment_trigger|create_database_views|update_loan_applications_status_enum/', $path)) {
                continue;
            }
            (require $path)->up();
        }
    }

    public function test_all_roles_can_save_reading_size_without_losing_security_preferences(): void
    {
        foreach ([User::ROLE_CLIENT, User::ROLE_ADMIN, User::ROLE_SUPERADMIN] as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'ui_preferences' => ['security' => ['two_factor_enabled_at' => now()->toIso8601String()], 'other_preference' => true],
            ]);
            $updateRoute = $role === User::ROLE_CLIENT ? 'profile.motion.update' : 'admin.settings.motion.update';
            $editRoute = $role === User::ROLE_CLIENT ? 'profile.motion.edit' : 'admin.settings.edit';
            foreach (['small' => '0.875', 'normal' => '1', 'large' => '1.125', 'extra-large' => '1.25'] as $size => $scale) {
                $this->actingAs($user)->patch(route($updateRoute), ['text_size' => $size, 'reduce_motion' => '1'])
                    ->assertRedirect()->assertSessionHas('status', 'motion-updated');
                $user->refresh();
                $this->assertSame($size, $user->ui_preferences['text_size']);
                $this->assertTrue($user->ui_preferences['reduce_motion']);
                $this->assertTrue($user->ui_preferences['other_preference']);
                $this->assertTrue($user->hasTwoFactorAuthenticationEnabled());
                $this->get(route($editRoute, ['section' => 'motion']))->assertOk()
                    ->assertSee('--app-text-scale:'.$scale, false)
                    ->assertSee('name="text_size"', false)->assertSee('Save accessibility');
            }
            $this->actingAs($user)->patch(route($updateRoute), ['reduce_motion' => '0'])->assertRedirect();
            $this->assertSame('extra-large', $user->fresh()->ui_preferences['text_size']);
            $this->assertFalse($user->fresh()->ui_preferences['reduce_motion']);
        }
    }

    public function test_invalid_size_is_rejected_without_changing_saved_preferences(): void
    {
        foreach ([User::ROLE_CLIENT, User::ROLE_ADMIN, User::ROLE_SUPERADMIN] as $role) {
            $user = User::factory()->create(['role' => $role, 'ui_preferences' => ['text_size' => 'large', 'reduce_motion' => true]]);
            $route = $role === User::ROLE_CLIENT ? 'profile.motion.update' : 'admin.settings.motion.update';
            $response = $this->actingAs($user)->patch(route($route), ['text_size' => 'giant', 'reduce_motion' => '0']);
            $response->assertSessionHasErrors('text_size', errorBag: $role === User::ROLE_CLIENT ? 'default' : 'motion');
            $this->assertSame(['text_size' => 'large', 'reduce_motion' => true], $user->fresh()->ui_preferences);
        }
    }
}
