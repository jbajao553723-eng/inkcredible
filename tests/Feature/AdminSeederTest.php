<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeding_creates_configured_administrators_and_the_default_client(): void
    {
        config()->set('seed_accounts.admin_email', 'admin@example.test');
        config()->set('seed_accounts.admin_password', 'AdminPassword1!');
        config()->set('seed_accounts.superadmin_email', 'superadmin@example.test');
        config()->set('seed_accounts.superadmin_password', 'SuperPassword1!');

        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 3);

        $admin = User::where('role', User::ROLE_ADMIN)->sole();
        $superadmin = User::where('role', User::ROLE_SUPERADMIN)->sole();
        $client = User::where('role', User::ROLE_CLIENT)->sole();

        $this->assertTrue(Hash::check('AdminPassword1!', $admin->password));
        $this->assertTrue(Hash::check('SuperPassword1!', $superadmin->password));
        $this->assertNotNull($admin->email_verified_at);
        $this->assertNotNull($superadmin->email_verified_at);
        $this->assertSame('joshuajoaquin2006@gmail.com', $client->email);
        $this->assertTrue(Hash::check('admin123', $client->password));
        $this->assertNotNull($client->email_verified_at);
    }
}
