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
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('penalty_percentage', 5, 2)->default(5.00)->after('payment_count');
            $table->decimal('penalty_amount', 10, 2)->default(0)->after('penalty_percentage');
            $table->boolean('is_overdue')->default(false)->after('penalty_amount');
            $table->date('last_penalty_calculated_date')->nullable()->after('is_overdue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['penalty_percentage', 'penalty_amount', 'is_overdue', 'last_penalty_calculated_date']);
        });
    }
};
