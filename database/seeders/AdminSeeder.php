<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('seed_accounts.admin_email');
        $password = config('seed_accounts.admin_password');

        if (! $email || ! $password) {
            return;
        }

        $admin = User::query()->firstOrNew(['email' => $email]);
        $admin->forceFill([
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'name' => 'System Administrator',
            'password' => Hash::make($password),
            'role' => 'admin',
            'contact_number' => 'Not provided',
            'age' => 18,
            'address' => 'Not provided',
        ])->save();
    }
}
