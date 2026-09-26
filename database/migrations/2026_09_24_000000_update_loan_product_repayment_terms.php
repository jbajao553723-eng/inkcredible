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
            $table->unsignedTinyInteger('installment_count')->default(1)->after('total_payable');
            $table->unsignedSmallInteger('repayment_period_days')->default(30)->after('installment_count');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->unsignedTinyInteger('installment_count')->default(1)->after('total_payable');
            $table->unsignedSmallInteger('repayment_period_days')->default(30)->after('installment_count');
        });

        DB::table('loan_types')->where('name', 'arawan')->update([
            'display_name' => 'Arawan Loan',
            'description' => 'A one-month loan repaid in 30 daily installments.',
            'min_amount' => 2000,
            'max_amount' => 10000,
            'interest_rate' => 10,
            'due_days' => 1,
        ]);

        DB::table('loan_types')->where('name', 'weekly')->update([
            'description' => 'Repay in four equal installments, with one payment due each week.',
            'interest_rate' => 12,
            'due_days' => 7,
        ]);

        DB::table('loan_types')->where('name', 'emergency')->update([
            'description' => 'A one-month emergency loan repaid in four equal weekly installments.',
            'max_amount' => 200000,
            'interest_rate' => 20,
            'due_days' => 7,
        ]);

        $loanTypeIds = DB::table('loan_types')->pluck('id', 'name');

        if (isset($loanTypeIds['arawan'])) {
            DB::table('loans')->where('loan_type_id', $loanTypeIds['arawan'])->update([
                'installment_count' => 30,
                'repayment_period_days' => 30,
            ]);
            DB::table('loan_applications')->where('loan_type_id', $loanTypeIds['arawan'])->update([
                'installment_count' => 30,
                'repayment_period_days' => 30,
            ]);
        }

        if (isset($loanTypeIds['weekly'])) {
            DB::table('loans')->where('loan_type_id', $loanTypeIds['weekly'])->update([
                'installment_count' => 4,
                'repayment_period_days' => 28,
            ]);
            DB::table('loan_applications')->where('loan_type_id', $loanTypeIds['weekly'])->update([
                'installment_count' => 4,
                'repayment_period_days' => 28,
            ]);
        }

        if (isset($loanTypeIds['emergency'])) {
            DB::table('loans')->where('loan_type_id', $loanTypeIds['emergency'])->update([
                'installment_count' => 4,
                'repayment_period_days' => 28,
            ]);
            DB::table('loan_applications')->where('loan_type_id', $loanTypeIds['emergency'])->update([
                'installment_count' => 4,
                'repayment_period_days' => 28,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('loan_types')->where('name', 'arawan')->update([
            'description' => 'Short-term loan for immediate needs',
            'min_amount' => 1000,
            'max_amount' => 2000,
            'interest_rate' => 10,
            'due_days' => 1,
        ]);

        DB::table('loan_types')->where('name', 'weekly')->update([
            'description' => 'Weekly payment loan for medium-term needs',
            'interest_rate' => 8,
            'due_days' => 7,
        ]);

        DB::table('loan_types')->where('name', 'emergency')->update([
            'description' => 'Emergency loan for urgent financial needs',
            'max_amount' => 50000,
            'interest_rate' => 12,
            'due_days' => 7,
        ]);

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn(['installment_count', 'repayment_period_days']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['installment_count', 'repayment_period_days']);
        });
    }
};
