<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penalties', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['waived_by']);

            // Remove unnecessary columns to streamline the table
            $table->dropColumn([
                'rate_percentage',    // Can be calculated if needed
                'days_overdue',       // Can be calculated from dates
                'waived_at',          // Can be derived from status changes
                'waived_by',          // May not be needed for basic tracking
                'waiver_reason',      // Can be combined with general reason field
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penalties', function (Blueprint $table) {
            // Restore removed columns if needed to rollback
            $table->decimal('rate_percentage', 5, 2)->nullable()->after('amount');
            $table->integer('days_overdue')->default(0)->after('rate_percentage');
            $table->timestamp('waived_at')->nullable()->after('status');
            $table->foreignId('waived_by')->nullable()->constrained('users')->onDelete('set null')->after('waived_at');
            $table->text('waiver_reason')->nullable()->after('waived_by');
        });
    }
};
