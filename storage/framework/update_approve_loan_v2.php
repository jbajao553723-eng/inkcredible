<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

// Drop and recreate the procedure without setting next_payment_date
DB::statement("DROP PROCEDURE IF EXISTS approve_loan");

$sql = "
CREATE PROCEDURE approve_loan(IN p_loan_id INT)
BEGIN
    START TRANSACTION;
    UPDATE loans
    SET status = 'approved'
    WHERE id = p_loan_id;
    COMMIT;
END;
";
DB::statement($sql);
echo "approve_loan procedure updated successfully!\n";
