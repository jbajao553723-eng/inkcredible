<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([

            'name' => 'Administrator',

            'email' => 'admin@gmail.com',

            'password' => Hash::make('admin123'),

            'role' => 'admin',

            'contact_number' => '09123456789',

            'age' => 18,

            'address' => 'Davao City'

        ]);
    }
}