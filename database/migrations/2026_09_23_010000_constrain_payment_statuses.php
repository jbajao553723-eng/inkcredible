<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payments')->where('status', 'paid')->update(['status' => 'approved']);
        DB::table('payments')->whereIn('status', ['failed', 'refunded'])->update(['status' => 'rejected']);
        DB::table('payments')->whereNotIn('status', ['pending', 'approved', 'rejected'])->update(['status' => 'pending']);

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_status_check');
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pending', 'approved', 'rejected'))");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending'");
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_status_check');
        }
    }
};
