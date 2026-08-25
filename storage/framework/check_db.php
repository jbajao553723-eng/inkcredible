<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
$db = Illuminate\Support\Facades\DB::connection()->getDatabaseName();
$colsLoans = Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM loans");
$colsPayments = Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM payments");
echo "LOANS COLUMNS:\n"; var_export($colsLoans); echo "\nPAYMENTS COLUMNS:\n"; var_export($colsPayments);
