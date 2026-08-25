<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$result = DB::select("SHOW CREATE PROCEDURE make_payment");
echo "MAKE_PAYMENT PROCEDURE:\n";
echo $result[0]->{'Create Procedure'} . "\n";
