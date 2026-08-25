<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

// Check new tables
$newTables = ['loan_types', 'loan_applications', 'loan_documents', 'payment_schedules'];
echo "NEW TABLES CREATED:\n";
foreach($newTables as $tableName) {
    echo "- $tableName\n";
    
    // Show columns for each table
    $cols = DB::select("SHOW COLUMNS FROM $tableName");
    echo "  Columns: ";
    $columnNames = array_map(function($col) { return $col->Field; }, $cols);
    echo implode(', ', $columnNames) . "\n";
    
    // Show data count
    $count = DB::table($tableName)->count();
    echo "  Records: $count\n\n";
}

// Check loan_types data
echo "LOAN TYPES DATA:\n";
$loanTypes = DB::table('loan_types')->get();
foreach($loanTypes as $type) {
    echo "- {$type->name}: {$type->display_name} (Min: ?{$type->min_amount}, Max: ?{$type->max_amount}, Rate: {$type->interest_rate}%, Due: {$type->due_days} days)\n";
}
