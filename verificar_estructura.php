<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Conectar a la base de datos
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verificando estructura de tablas...\n\n";

// Verificar estructura de medicos
echo "=== ESTRUCTURA DE MÉDICOS ===\n";
$medicos_structure = DB::select("PRAGMA table_info(medicos)");
foreach ($medicos_structure as $column) {
    echo "- {$column->name}: {$column->type} (NOT NULL: " . ($column->notnull ? 'Sí' : 'No') . ", PK: " . ($column->pk ? 'Sí' : 'No') . ")\n";
}

// Verificar índices únicos de medicos
echo "\n=== ÍNDICES ÚNICOS DE MÉDICOS ===\n";
$medicos_indexes = DB::select("PRAGMA index_list(medicos)");
foreach ($medicos_indexes as $index) {
    if ($index->unique) {
        echo "- {$index->name}: Único\n";
        $index_info = DB::select("PRAGMA index_info(medicos, '{$index->name}')");
        foreach ($index_info as $info) {
            echo "  - Columna: {$info->name}\n";
        }
    }
}

// Verificar datos existentes en medicos
echo "\n=== DATOS EXISTENTES EN MÉDICOS ===\n";
$medicos_data = DB::table('medicos')->get();
foreach ($medicos_data as $medico) {
    echo "- CI: {$medico->ci}, ID Usuario: {$medico->id_usuario}\n";
}

// Verificar usuarios existentes
echo "\n=== USUARIOS EXISTENTES ===\n";
$users_data = DB::table('users')->select('id', 'name')->get();
foreach ($users_data as $user) {
    echo "- ID: {$user->id}, Nombre: {$user->name}\n";
}
