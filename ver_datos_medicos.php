<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $medicos = DB::table('medicos')->get();
    echo "Datos de médicos:\n";
    foreach ($medicos as $med) {
        echo "CI: {$med->ci} (tipo: " . gettype($med->ci) . ")\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
