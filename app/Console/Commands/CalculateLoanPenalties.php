<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Notifications\PenaltyAppliedNotification;
use Illuminate\Console\Command;

class CalculateLoanPenalties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-loan-penalties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and update penalty amounts for all overdue loans';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $updatedCount = 0;

        Loan::where('status', Loan::STATUS_APPROVED)
            ->chunkById(100, function ($loans) use (&$updatedCount) {
                foreach ($loans as $loan) {
                    $previousPenalty = (float) $loan->penalty_amount;
                    $currentPenalty = $loan->calculatePenalty();

                    if ($currentPenalty > $previousPenalty) {
                        $loan->user?->notify(new PenaltyAppliedNotification(
                            $loan,
                            round($currentPenalty - $previousPenalty, 2)
                        ));
                    }

                    $updatedCount++;
                }
            });

        $this->info("Updated daily penalties for {$updatedCount} active loans at ".Loan::DAILY_PENALTY_RATE.'% per overdue day.');

        return self::SUCCESS;
    }
}
