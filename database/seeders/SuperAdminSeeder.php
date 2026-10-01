<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('seed_accounts.superadmin_email') ?: config('seed_accounts.admin_email');
        $password = config('seed_accounts.superadmin_password') ?: config('seed_accounts.admin_password');

        if (! $email || ! $password) {
            return;
        }

        $superadmin = User::query()->firstOrNew(['email' => $email]);
        $superadmin->forceFill([
            'first_name' => 'System',
            'last_name' => 'Superadministrator',
            'name' => 'System Superadministrator',
            'password' => Hash::make($password),
            'role' => User::ROLE_SUPERADMIN,
            'is_active' => true,
            'email_verified_at' => now(),
            'contact_number' => 'Not provided',
            'age' => 18,
            'address' => 'Administrative account',
            'disabled_at' => null,
            'disabled_by' => null,
        ])->save();
    }
}
