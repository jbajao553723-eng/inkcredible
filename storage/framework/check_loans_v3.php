<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$cols = DB::select("SHOW COLUMNS FROM loans");
echo "LOANS COLUMNS:\n";
foreach($cols as $col) {
    echo $col->Field . ' (' . $col->Type . ")\n";
}
