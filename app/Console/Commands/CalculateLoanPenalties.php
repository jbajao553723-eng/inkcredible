<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;

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
    public function handle()
    {
        $loans = Loan::where('status', 'approved')->get();
        $updatedCount = 0;

        foreach ($loans as $loan) {
            $loan->calculatePenalty();
            $updatedCount++;
        }

        $this->info("Successfully updated penalties for {$updatedCount} loans.");
    }
}
