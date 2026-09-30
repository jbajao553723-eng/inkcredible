<?php

namespace Database\Seeders;

use App\Models\LoanType;
use Illuminate\Database\Seeder;

class LoanTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loanTypes = [
            [
                'name' => 'arawan',
                'display_name' => 'Arawan Loan',
                'description' => 'A one-month loan repaid in 30 daily installments.',
                'min_amount' => 2000.00,
                'max_amount' => 15000.00,
                'interest_rate' => 20.00,
                'due_days' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'weekly',
                'display_name' => 'Weekly Loan',
                'description' => 'Repay in four equal installments, with one payment due each week.',
                'min_amount' => 10000.00,
                'max_amount' => 50000.00,
                'interest_rate' => 20.00,
                'due_days' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'emergency',
                'display_name' => 'Emergency Loan',
                'description' => 'A one-month emergency loan repaid in four equal weekly installments.',
                'min_amount' => 5000.00,
                'max_amount' => 200000.00,
                'interest_rate' => 20.00,
                'due_days' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($loanTypes as $loanType) {
            LoanType::updateOrCreate(
                ['name' => $loanType['name']],
                $loanType,
            );
        }
    }
}
