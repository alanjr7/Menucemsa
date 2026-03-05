<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $especialidades = DB::table('especialidades')->get();
    echo "Especialidades:\n";
    foreach ($especialidades as $esp) {
        echo "- {$esp->codigo}: {$esp->nombre}\n";
    }

    $medicos = DB::table('medicos')->get();
    echo "\nMédicos:\n";
    foreach ($medicos as $med) {
        echo "- CI: {$med->ci}, Especialidad: {$med->codigo_especialidad}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
