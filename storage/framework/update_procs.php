<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

DB::unprepared('DROP PROCEDURE IF EXISTS approve_loan');
DB::unprepared("CREATE PROCEDURE approve_loan(IN p_loan_id INT)
BEGIN
    START TRANSACTION;
    UPDATE loans
    SET status = 'approved'
    WHERE id = p_loan_id;
    COMMIT;
END");

DB::unprepared('DROP PROCEDURE IF EXISTS make_payment');
DB::unprepared("CREATE PROCEDURE make_payment(
    IN p_loan_id INT,
    IN p_amount DECIMAL(10,2),
    IN p_method ENUM('gcash','cash'),
    IN p_proof VARCHAR(255),
    IN p_status ENUM('pending','approved','rejected')
)
BEGIN
    START TRANSACTION;
    UPDATE loans
    SET paid_amount = IFNULL(paid_amount, 0) + p_amount
    WHERE id = p_loan_id;

    INSERT INTO payments (loan_id, amount, method, proof, status)
    VALUES (p_loan_id, p_amount, p_method, p_proof, p_status);
    COMMIT;
END");

echo "Procedures updated.\n";
