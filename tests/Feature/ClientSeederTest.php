<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ClientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_local_client_account_idempotently(): void
    {
        config()->set('seed_accounts.client_email', 'demo@example.test');
        config()->set('seed_accounts.client_password', 'test-only-password');

        $this->seed(ClientSeeder::class);
        $this->seed(ClientSeeder::class);

        $client = User::query()
            ->where('email', 'demo@example.test')
            ->sole();

        $this->assertSame('Demo Client', $client->name);
        $this->assertSame('client', $client->role);
        $this->assertTrue(Hash::check('test-only-password', $client->password));
        $this->assertNotNull($client->email_verified_at);
    }
}
