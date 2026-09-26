<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('loan_types')->where('name', 'arawan')->update([
            'interest_rate' => 10,
        ]);

        DB::table('loan_types')->where('name', 'emergency')->update([
            'max_amount' => 200000,
        ]);
    }

    public function down(): void
    {
        DB::table('loan_types')->where('name', 'arawan')->update([
            'interest_rate' => 20,
        ]);

        DB::table('loan_types')->where('name', 'emergency')->update([
            'max_amount' => 50000,
        ]);
    }
};
