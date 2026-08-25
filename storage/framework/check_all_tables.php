<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$tables = DB::select("SHOW TABLES");
echo "CURRENT TABLES:\n";
foreach($tables as $table) {
    $tableName = $table->{'Tables_in_inkcredible'};
    echo "- $tableName\n";
    
    // Show columns for each table
    $cols = DB::select("SHOW COLUMNS FROM $tableName");
    echo "  Columns: ";
    $columnNames = array_map(function($col) { return $col->Field; }, $cols);
    echo implode(', ', $columnNames) . "\n\n";
}
