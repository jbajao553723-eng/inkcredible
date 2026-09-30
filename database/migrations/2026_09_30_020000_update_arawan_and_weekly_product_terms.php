<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('loan_types')->where('name', 'arawan')->update([
            'max_amount' => 15000,
            'interest_rate' => 20,
            'updated_at' => now(),
        ]);

        DB::table('loan_types')->where('name', 'weekly')->update([
            'max_amount' => 50000,
            'interest_rate' => 20,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('loan_types')->where('name', 'arawan')->update([
            'max_amount' => 10000,
            'interest_rate' => 10,
            'updated_at' => now(),
        ]);

        DB::table('loan_types')->where('name', 'weekly')->update([
            'max_amount' => 20000,
            'interest_rate' => 12,
            'updated_at' => now(),
        ]);
    }
};
