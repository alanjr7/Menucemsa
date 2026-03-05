<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "Tablas en la base de datos:\n";
    foreach ($tables as $table) {
        echo "- " . $table->name . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
