<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('seed_accounts.client_email');
        $password = config('seed_accounts.client_password');

        if (! $email || ! $password) {
            return;
        }

        $client = User::query()->firstOrNew(['email' => $email]);

        $client->forceFill([
            'first_name' => 'Demo',
            'last_name' => 'Client',
            'name' => 'Demo Client',
            'password' => Hash::make($password),
            'role' => 'client',
            'email_verified_at' => now(),
            'contact_number' => 'Not provided',
            'age' => 18,
            'address' => 'Not provided',
        ])->save();
    }
}
