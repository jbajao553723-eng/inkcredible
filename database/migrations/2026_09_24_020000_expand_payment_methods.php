<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE payments MODIFY method VARCHAR(50) NOT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('payments')
                ->whereNotIn('method', ['gcash', 'cash'])
                ->update(['method' => 'gcash']);

            DB::statement("ALTER TABLE payments MODIFY method ENUM('gcash', 'cash') NOT NULL");
        }
    }
};
