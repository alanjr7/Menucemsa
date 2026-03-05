<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Verificar estructura de tabla medicos
    $medicos = DB::select("PRAGMA table_info(medicos)");
    echo "Estructura de tabla medicos:\n";
    foreach ($medicos as $col) {
        echo "- {$col->name}: {$col->type}\n";
    }

    echo "\nEstructura de tabla citas:\n";
    $citas = DB::select("PRAGMA table_info(citas)");
    foreach ($citas as $col) {
        echo "- {$col->name}: {$col->type}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
