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
                'description' => 'Short-term loan for immediate needs',
                'min_amount' => 1000.00,
                'max_amount' => 2000.00,
                'interest_rate' => 10.00,
                'due_days' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'weekly',
                'display_name' => 'Weekly Loan',
                'description' => 'Weekly payment loan for medium-term needs',
                'min_amount' => 10000.00,
                'max_amount' => 20000.00,
                'interest_rate' => 8.00,
                'due_days' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'emergency',
                'display_name' => 'Emergency Loan',
                'description' => 'Emergency loan for urgent financial needs',
                'min_amount' => 5000.00,
                'max_amount' => 50000.00,
                'interest_rate' => 12.00,
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
