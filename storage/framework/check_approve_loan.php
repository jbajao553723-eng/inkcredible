<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$result = DB::select("SHOW CREATE PROCEDURE approve_loan");
echo "APPROVE_LOAN PROCEDURE:\n";
echo $result[0]->{'Create Procedure'} . "\n";
