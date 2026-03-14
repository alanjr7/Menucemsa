<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Conectar a la base de datos
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Ejecutando seeders pendientes...\n\n";

// 1. Primero insertar médicos sin claves foráneas
echo "1. Insertando médicos...\n";
$medicos = [
    ['ci' => 12345678, 'id_usuario' => 3, 'telefono' => 5551234, 'estado' => 'Activo', 'id_asistente' => 'ASIST001', 'codigo_especialidad' => 'ESP001'],
    ['ci' => 23456789, 'id_usuario' => 9, 'telefono' => 5555678, 'estado' => 'Activo', 'id_asistente' => 'ASIST004', 'codigo_especialidad' => 'ESP002'],
    ['ci' => 34567890, 'id_usuario' => 13, 'telefono' => 5559012, 'estado' => 'Activo', 'id_asistente' => 'ASIST007', 'codigo_especialidad' => 'ESP003'],
    ['ci' => 45678901, 'id_usuario' => 16, 'telefono' => 5553456, 'estado' => 'Activo', 'id_asistente' => 'ASIST010', 'codigo_especialidad' => 'ESP004'],
    ['ci' => 56789012, 'id_usuario' => 17, 'telefono' => 5557890, 'estado' => 'Activo', 'id_asistente' => 'ASIST013', 'codigo_especialidad' => 'ESP005'],
];

foreach ($medicos as $medico) {
    DB::table('medicos')->insert($medico);
}
echo "Médicos insertados: " . count($medicos) . "\n\n";

// 2. Insertar internos
echo "2. Insertando internos...\n";
$internos = [
    ['ci' => 98765432, 'id_usuario_medico' => 21, 'nombre' => 'Roberto Sánchez López', 'fecha_inicio' => '2024-01-15', 'fecha_fin' => '2024-06-15', 'telefono' => 5554321, 'lugar_asignado' => 'Servicio de Medicina Interna', 'detalle' => 'Rotación clínica general', 'estado_formulario' => 'Aprobado'],
    ['ci' => 87654321, 'id_usuario_medico' => 13, 'nombre' => 'Ana Martínez Gómez', 'fecha_inicio' => '2024-02-01', 'fecha_fin' => '2024-07-01', 'telefono' => 5555432, 'lugar_asignado' => 'Servicio de Pediatría', 'detalle' => 'Rotación pediátrica', 'estado_formulario' => 'Aprobado'],
];

foreach ($internos as $interno) {
    DB::table('internos')->insert($interno);
}
echo "Internos insertados: " . count($internos) . "\n\n";

// 3. Insertar enfermeras
echo "3. Insertando enfermeras...\n";
$enfermeras = [
    ['ci' => 11223344, 'id_usuario' => 4, 'telefono' => 5551111, 'tipo' => 'Enfermera General', 'estado' => 'Activo', 'id_asistente' => 'ASIST003'],
    ['ci' => 22334455, 'id_usuario' => 6, 'telefono' => 5552222, 'tipo' => 'Enfermera Jefe', 'estado' => 'Activo', 'id_asistente' => 'ASIST005'],
];

foreach ($enfermeras as $enfermera) {
    DB::table('enfermeras')->insert($enfermera);
}
echo "Enfermeras insertadas: " . count($enfermeras) . "\n\n";

// 4. Insertar triages
echo "4. Insertando triages...\n";
$triages = [
    ['id' => 'TRI001', 'color' => 'Rojo', 'descripcion' => 'Reanimación inmediata', 'prioridad' => 'Alta', 'id_usuario' => 22],
    ['id' => 'TRI002', 'color' => 'Naranja', 'descripcion' => 'Emergencia urgente', 'prioridad' => 'Alta', 'id_usuario' => 22],
];

foreach ($triages as $triage) {
    DB::table('triages')->insert($triage);
}
echo "Triages insertados: " . count($triages) . "\n\n";

echo "Seeders pendientes ejecutados correctamente!\n";
