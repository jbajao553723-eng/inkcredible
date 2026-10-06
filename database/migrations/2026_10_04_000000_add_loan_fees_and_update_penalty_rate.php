<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('finance_fee', 10, 2)->default(0)->after('amount');
            $table->decimal('processing_fee', 10, 2)->default(0)->after('finance_fee');
            $table->decimal('penalty_percentage', 5, 2)->default(10.00)->change();
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->decimal('finance_fee', 10, 2)->default(0)->after('calculated_interest');
            $table->decimal('processing_fee', 10, 2)->default(0)->after('finance_fee');
        });

        DB::table('loans')->update(['penalty_percentage' => 10.00]);
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn(['finance_fee', 'processing_fee']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['finance_fee', 'processing_fee']);
            $table->decimal('penalty_percentage', 5, 2)->default(5.00)->change();
        });

        DB::table('loans')->update(['penalty_percentage' => 5.00]);
    }
};
