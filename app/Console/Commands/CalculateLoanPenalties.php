<?php

namespace App\Console\Commands;

use App\Models\Loan;
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
                    $loan->calculatePenalty();
                    $updatedCount++;
                }
            });

        $this->info("Updated daily penalties for {$updatedCount} active loans at 5% per overdue day.");

        return self::SUCCESS;
    }
}
