<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS loan_summary_view');
        DB::statement('DROP VIEW IF EXISTS payment_summary_view');
        DB::statement('DROP VIEW IF EXISTS overdue_loans_view');
        DB::statement('DROP VIEW IF EXISTS loan_performance_view');
        DB::statement('DROP VIEW IF EXISTS user_loan_summary_view');

        // =========================
        // LOAN SUMMARY VIEW (FIXED)
        // =========================
        DB::statement('
            CREATE VIEW loan_summary_view AS
            SELECT
                l.id,
                l.loan_code,
                l.user_id,
                u.name as user_name,
                u.email as user_email,

                lt.name as loan_type,
                lt.display_name as loan_type_display,
                lt.interest_rate,

                l.amount as loan_amount,
                l.total_payable,

                -- SAFE TOTAL PAID (NO DUPLICATION EVER)
                COALESCE((
                    SELECT SUM(p.amount)
                    FROM payments p
                    WHERE p.loan_id = l.id
                ), 0) as paid_amount,

                -- SAFE BALANCE
                (
                    l.total_payable - COALESCE((
                        SELECT SUM(p.amount)
                        FROM payments p
                        WHERE p.loan_id = l.id
                    ), 0)
                ) as remaining_balance,

                l.status as loan_status,
                l.created_at,

                -- schedule info ONLY
                ps.next_due_date,
                ps.overdue_count,
                ps.total_installments

            FROM loans l
            LEFT JOIN users u ON l.user_id = u.id
            LEFT JOIN loan_types lt ON l.loan_type_id = lt.id

            LEFT JOIN (
                SELECT
                    loan_id,
                    MIN(CASE WHEN status != "paid" THEN due_date END) as next_due_date,
                    SUM(CASE WHEN status = "overdue" THEN 1 ELSE 0 END) as overdue_count,
                    COUNT(*) as total_installments
                FROM payment_schedules
                GROUP BY loan_id
            ) ps ON l.id = ps.loan_id
        ');

        // =========================
        // PAYMENT SUMMARY VIEW
        // =========================
        DB::statement('
            CREATE VIEW payment_summary_view AS
            SELECT
                ps.id as payment_schedule_id,
                ps.loan_id,
                l.loan_code,
                l.user_id,
                u.name as user_name,
                u.email as user_email,
                ps.installment_number,
                ps.scheduled_amount,
                ps.paid_amount,
                ps.penalty_amount,
                ps.due_date,
                ps.paid_date,
                ps.status,

                CASE
                    WHEN ps.due_date < NOW() AND ps.status != "paid" THEN 1
                    ELSE 0
                END as is_overdue

            FROM payment_schedules ps
            LEFT JOIN loans l ON ps.loan_id = l.id
            LEFT JOIN users u ON l.user_id = u.id
        ');

        // =========================
        // OVERDUE VIEW
        // =========================
        DB::statement('
            CREATE VIEW overdue_loans_view AS
            SELECT
                l.id,
                l.loan_code,
                l.user_id,
                u.name as user_name,
                u.email as user_email,

                lt.name as loan_type,
                l.amount,
                l.total_payable,

                COALESCE((
                    SELECT SUM(p.amount)
                    FROM payments p
                    WHERE p.loan_id = l.id
                ), 0) as paid_amount,

                ps.total_overdue_amount,
                ps.next_due_date,
                ps.days_overdue,
                ps.overdue_installments

            FROM loans l
            LEFT JOIN users u ON l.user_id = u.id
            LEFT JOIN loan_types lt ON l.loan_type_id = lt.id

            LEFT JOIN (
                SELECT
                    loan_id,
                    SUM(scheduled_amount - COALESCE(paid_amount,0)) as total_overdue_amount,
                    MIN(due_date) as next_due_date,
                    MAX(DATEDIFF(NOW(), due_date)) as days_overdue,
                    COUNT(*) as overdue_installments
                FROM payment_schedules
                WHERE status != "paid" AND due_date < NOW()
                GROUP BY loan_id
            ) ps ON l.id = ps.loan_id
        ');

        // =========================
        // PERFORMANCE VIEW
        // =========================
        DB::statement('
            CREATE VIEW loan_performance_view AS
            SELECT
                lt.name as loan_type,
                COUNT(l.id) as total_loans,
                SUM(l.amount) as total_amount,
                AVG(l.amount) as avg_amount
            FROM loans l
            LEFT JOIN loan_types lt ON l.loan_type_id = lt.id
            GROUP BY lt.name
        ');

        // =========================
        // USER SUMMARY VIEW
        // =========================
        DB::statement('
            CREATE VIEW user_loan_summary_view AS
            SELECT
                u.id,
                u.name,
                u.email,
                COUNT(l.id) as total_loans,

                SUM(l.amount) as total_borrowed,

                SUM(CASE WHEN l.status = "approved" THEN 1 ELSE 0 END) as approved_loans

            FROM users u
            LEFT JOIN loans l ON u.id = l.user_id
            GROUP BY u.id, u.name, u.email
        ');
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS user_loan_summary_view');
        DB::statement('DROP VIEW IF EXISTS loan_performance_view');
        DB::statement('DROP VIEW IF EXISTS overdue_loans_view');
        DB::statement('DROP VIEW IF EXISTS payment_summary_view');
        DB::statement('DROP VIEW IF EXISTS loan_summary_view');
    }
};
