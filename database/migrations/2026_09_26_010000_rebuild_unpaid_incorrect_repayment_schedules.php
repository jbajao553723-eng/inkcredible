<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $loans = DB::table('loans')
            ->join('loan_types', 'loans.loan_type_id', '=', 'loan_types.id')
            ->where('loans.status', 'approved')
            ->whereIn('loan_types.name', ['arawan', 'weekly', 'emergency'])
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('payments')
                    ->whereColumn('payments.loan_id', 'loans.id')
                    ->where('payments.status', 'approved');
            })
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('payment_schedules')
                    ->whereColumn('payment_schedules.loan_id', 'loans.id')
                    ->where(function ($query) {
                        $query->where('paid_amount', '>', 0)
                            ->orWhere('penalty_amount', '>', 0)
                            ->orWhere('status', 'paid');
                    });
            })
            ->select('loans.id', 'loans.total_payable', 'loans.approved_at', 'loans.created_at', 'loan_types.name as product_name')
            ->get();

        foreach ($loans as $loan) {
            [$installmentCount, $periodDays] = $loan->product_name === 'arawan'
                ? [30, 30]
                : [4, 28];
            $existingScheduleCount = DB::table('payment_schedules')->where('loan_id', $loan->id)->count();

            if ($existingScheduleCount === $installmentCount) {
                DB::table('loans')->where('id', $loan->id)->update([
                    'installment_count' => $installmentCount,
                    'repayment_period_days' => $periodDays,
                ]);

                continue;
            }

            DB::transaction(function () use ($loan, $installmentCount, $periodDays) {
                DB::table('payment_schedules')->where('loan_id', $loan->id)->delete();

                $totalCents = (int) round(((float) $loan->total_payable) * 100);
                $baseCents = intdiv($totalCents, $installmentCount);
                $remainingCents = $totalCents - ($baseCents * $installmentCount);
                $intervalDays = (int) floor($periodDays / $installmentCount);
                $scheduleStart = Carbon::parse($loan->approved_at ?? $loan->created_at)->startOfDay();
                $timestamp = now();
                $schedules = [];

                for ($installment = 1; $installment <= $installmentCount; $installment++) {
                    $installmentCents = $baseCents + ($installment === $installmentCount ? $remainingCents : 0);
                    $schedules[] = [
                        'loan_id' => $loan->id,
                        'installment_number' => $installment,
                        'scheduled_amount' => number_format($installmentCents / 100, 2, '.', ''),
                        'due_date' => $scheduleStart->copy()->addDays($intervalDays * $installment)->toDateString(),
                        'paid_amount' => 0,
                        'status' => 'pending',
                        'penalty_amount' => 0,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }

                DB::table('payment_schedules')->insert($schedules);
                DB::table('loans')->where('id', $loan->id)->update([
                    'installment_count' => $installmentCount,
                    'repayment_period_days' => $periodDays,
                ]);
            });
        }
    }

    public function down(): void
    {
        // Rebuilt financial schedules are not reverted to an incorrect structure.
    }
};
