<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('loan_types')->where('name', 'arawan')->update([
            'description' => 'A one-month loan repaid in 30 daily installments.',
            'due_days' => 1,
        ]);

        DB::table('loan_types')->where('name', 'weekly')->update([
            'description' => 'A one-month loan repaid in four equal weekly installments.',
            'due_days' => 7,
        ]);

        DB::table('loan_types')->where('name', 'emergency')->update([
            'description' => 'A one-month emergency loan repaid in four equal weekly installments.',
            'due_days' => 7,
        ]);

        $loanTypeIds = DB::table('loan_types')->pluck('id', 'name');

        if (isset($loanTypeIds['arawan'])) {
            DB::table('loans')
                ->where('loan_type_id', $loanTypeIds['arawan'])
                ->where('status', 'pending')
                ->update(['installment_count' => 30, 'repayment_period_days' => 30]);

            DB::table('loan_applications')
                ->where('loan_type_id', $loanTypeIds['arawan'])
                ->where('status', 'pending')
                ->update(['installment_count' => 30, 'repayment_period_days' => 30]);
        }

        foreach (['weekly', 'emergency'] as $type) {
            if (! isset($loanTypeIds[$type])) {
                continue;
            }

            DB::table('loans')
                ->where('loan_type_id', $loanTypeIds[$type])
                ->where('status', 'pending')
                ->update(['installment_count' => 4, 'repayment_period_days' => 28]);

            DB::table('loan_applications')
                ->where('loan_type_id', $loanTypeIds[$type])
                ->where('status', 'pending')
                ->update(['installment_count' => 4, 'repayment_period_days' => 28]);
        }
    }

    public function down(): void
    {
        // Existing repayment agreements are intentionally not rewritten on rollback.
    }
};
