<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

// Drop the existing procedure
DB::statement("DROP PROCEDURE IF EXISTS make_payment");

// Create the updated procedure
$sql = "
CREATE PROCEDURE make_payment(
    IN p_loan_id INT,
    IN p_amount DECIMAL(10,2),
    IN p_method ENUM('gcash','cash'),
    IN p_proof VARCHAR(255),
    IN p_status ENUM('pending','approved','rejected')
)
BEGIN
    START TRANSACTION;
    UPDATE loans
    SET paid_amount = IFNULL(paid_amount, 0) + p_amount,
        payment_count = IFNULL(payment_count, 0) + 1
    WHERE id = p_loan_id;

    INSERT INTO payments (loan_id, amount, method, proof, status, created_at, updated_at)
    VALUES (p_loan_id, p_amount, p_method, p_proof, p_status, NOW(), NOW());
    COMMIT;
END;
";
DB::statement($sql);
echo "make_payment procedure updated successfully!\n";
