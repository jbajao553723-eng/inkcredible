<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $client = User::query()->firstOrNew([
            'email' => 'joshuajoaquin2006@gmail.com',
        ]);

        $client->forceFill([
            'first_name' => 'Joshua',
            'last_name' => 'Joaquin',
            'name' => 'Joshua Joaquin',
            'password' => Hash::make('admin123'),
            'role' => User::ROLE_CLIENT,
            'is_active' => true,
            'email_verified_at' => now(),
            'disabled_at' => null,
            'disabled_by' => null,
            'contact_number' => 'Not provided',
            'age' => 18,
            'address' => 'Not provided',
        ])->save();
    }
}
