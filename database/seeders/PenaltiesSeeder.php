<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\PaymentSchedule;
use App\Models\Penalty;
use Illuminate\Database\Seeder;

class PenaltiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if we have loans and payment schedules
        $loansCount = Loan::count();
        $schedulesCount = PaymentSchedule::count();

        if ($loansCount === 0 || $schedulesCount === 0) {
            $this->command->info('No loans or payment schedules found. Skipping penalties seeding.');

            return;
        }

        // Get some sample loans and payment schedules
        $loans = Loan::take(2)->get();
        $schedules = PaymentSchedule::where('status', '!=', 'paid')->take(3)->get();

        foreach ($loans as $loan) {
            // Create some penalties for loans
            Penalty::create([
                'loan_id' => $loan->id,
                'type' => Penalty::TYPE_LATE_PAYMENT,
                'amount' => 50.00,
                'applied_date' => now()->subDays(3),
                'reason' => 'Late payment penalty for installment',
                'status' => Penalty::STATUS_ACTIVE,
            ]);

            Penalty::create([
                'loan_id' => $loan->id,
                'type' => Penalty::TYPE_PROCESSING_FEE,
                'amount' => 25.00,
                'applied_date' => now()->subDays(10),
                'reason' => 'Additional processing fee',
                'status' => Penalty::STATUS_ACTIVE,
            ]);
        }

        foreach ($schedules as $schedule) {
            // Create penalties linked to specific payment schedules
            Penalty::create([
                'loan_id' => $schedule->loan_id,
                'payment_schedule_id' => $schedule->id,
                'type' => Penalty::TYPE_LATE_PAYMENT,
                'amount' => 30.00,
                'applied_date' => now()->subDays(2),
                'reason' => 'Overdue payment penalty',
                'status' => Penalty::STATUS_ACTIVE,
            ]);
        }

        // Create a waived penalty example
        if ($loans->count() > 0) {
            Penalty::create([
                'loan_id' => $loans->first()->id,
                'type' => Penalty::TYPE_ADDITIONAL_FEE,
                'amount' => 75.00,
                'applied_date' => now()->subDays(7),
                'reason' => 'Additional fee waived due to customer circumstances',
                'status' => Penalty::STATUS_WAIVED,
            ]);
        }

        $this->command->info('Penalties seeded successfully!');
    }
}
