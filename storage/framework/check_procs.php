<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$procs = DB::select("SHOW PROCEDURE STATUS WHERE Db = DATABASE()");
echo "STORED PROCEDURES:\n";
foreach($procs as $proc) {
    echo $proc->Name . "\n";
}
