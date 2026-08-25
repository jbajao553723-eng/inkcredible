<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create trigger to automatically update loan status when balance is paid off
        DB::unprepared('
            CREATE TRIGGER update_loan_status_on_payment
            AFTER UPDATE ON payment_schedules
            FOR EACH ROW
            BEGIN
                DECLARE remaining_balance DECIMAL(15,2);

                -- Calculate remaining balance for the loan
                SELECT COALESCE(SUM(scheduled_amount + penalty_amount - COALESCE(paid_amount, 0)), 0)
                INTO remaining_balance
                FROM payment_schedules
                WHERE loan_id = NEW.loan_id;

                -- If remaining balance is 0 or less, mark loan as paid
                IF remaining_balance <= 0 THEN
                    UPDATE loans SET status = "paid", updated_at = NOW() WHERE id = NEW.loan_id;
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_loan_status_on_payment');
    }
};
