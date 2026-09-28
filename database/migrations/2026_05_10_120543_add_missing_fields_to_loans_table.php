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
            if (! Schema::hasColumn('loans', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('total_payable');
            }
            if (! Schema::hasColumn('loans', 'payment_count')) {
                $table->integer('payment_count')->default(0)->after('paid_amount');
            }
            if (! Schema::hasColumn('loans', 'next_payment_date')) {
                $table->date('next_payment_date')->nullable()->after('payment_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'payment_count', 'next_payment_date']);
        });
    }
};
