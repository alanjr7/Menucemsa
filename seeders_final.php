<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Conectar a la base de datos
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Ejecutando seeders finales...\n\n";

// Insertar enfermeras con id_asistente válido
echo "1. Insertando enfermeras finales...\n";

// Obtener IDs de asistentes disponibles
$asistentes_disponibles = DB::table('asistente_quirofanos')->pluck('id')->toArray();

if (empty($asistentes_disponibles)) {
    echo "No hay asistentes disponibles, insertando valores válidos...\n";
    // Insertar algunos asistentes básicos si no existen
    $asistentes_basicos = [
        ['id' => 'ASIST001', 'nro_quirofano' => 1, 'descripcion' => 'Asistente principal'],
        ['id' => 'ASIST002', 'nro_quirofano' => 2, 'descripcion' => 'Asistente secundario'],
    ];
    
    foreach ($asistentes_basicos as $asistente) {
        try {
            DB::table('asistente_quirofanos')->insert($asistente);
        } catch (Exception $e) {
            // Ignorar si ya existe
        }
    }
    $asistentes_disponibles = ['ASIST001', 'ASIST002'];
}

// Obtener usuarios disponibles para enfermeras
$usuarios_usados_enfermeras = DB::table('enfermeras')->pluck('id_usuario')->toArray();
$usuarios_disponibles_enfermeras = DB::table('users')
    ->whereNotIn('id', $usuarios_usados_enfermeras)
    ->pluck('id')
    ->take(5)
    ->toArray();

$enfermeras = [
    ['ci' => 11223344, 'id_usuario' => $usuarios_disponibles_enfermeras[0] ?? 90, 'telefono' => 5551111, 'tipo' => 'Enfermera General', 'estado' => 'Activo', 'id_asistente' => $asistentes_disponibles[0] ?? 'ASIST001'],
    ['ci' => 22334455, 'id_usuario' => $usuarios_disponibles_enfermeras[1] ?? 89, 'telefono' => 5552222, 'tipo' => 'Enfermera Jefe', 'estado' => 'Activo', 'id_asistente' => $asistentes_disponibles[1] ?? 'ASIST002'],
];

$enfermeras_insertadas = 0;
foreach ($enfermeras as $enfermera) {
    try {
        DB::table('enfermeras')->insert($enfermera);
        $enfermeras_insertadas++;
        echo "- Enfermera CI {$enfermera['ci']} insertada correctamente\n";
    } catch (Exception $e) {
        echo "Error insertando enfermera CI {$enfermera['ci']}: " . $e->getMessage() . "\n";
    }
}
echo "Enfermeras insertadas: {$enfermeras_insertadas}\n\n";

// Insertar recetas
echo "2. Insertando recetas...\n";
$medicos_disponibles = DB::table('medicos')->pluck('id_usuario')->toArray();
$consultas_disponibles = DB::table('consultas')->pluck('nro')->toArray();

$recetas = [
    ['nro' => 'REC001', 'fecha' => '2024-03-14', 'indicaciones' => 'Tomar con alimentos', 'id_usuario_medico' => $medicos_disponibles[0] ?? 7, 'nro_consulta' => $consultas_disponibles[0] ?? 'C001'],
    ['nro' => 'REC002', 'fecha' => '2024-03-14', 'indicaciones' => 'Tomar cada 8 horas', 'id_usuario_medico' => $medicos_disponibles[1] ?? 1, 'nro_consulta' => $consultas_disponibles[1] ?? 'C002'],
    ['nro' => 'REC003', 'fecha' => '2024-03-14', 'indicaciones' => 'Suspender si hay reacción', 'id_usuario_medico' => $medicos_disponibles[2] ?? 5, 'nro_consulta' => $consultas_disponibles[2] ?? 'C003'],
];

$recetas_insertadas = 0;
foreach ($recetas as $receta) {
    try {
        DB::table('recetas')->insert($receta);
        $recetas_insertadas++;
        echo "- Receta {$receta['nro']} insertada correctamente\n";
    } catch (Exception $e) {
        echo "Error insertando receta {$receta['nro']}: " . $e->getMessage() . "\n";
    }
}
echo "Recetas insertadas: {$recetas_insertadas}\n\n";

// Insertar historial médicos
echo "3. Insertando historial médicos...\n";
$pacientes_disponibles = DB::table('pacientes')->pluck('ci')->toArray();

$historial_medicos = [
    ['id' => 'HM001', 'ci_paciente' => $pacientes_disponibles[0] ?? 123456789, 'fecha' => '2024-03-14', 'detalle' => 'Control general de rutina', 'observaciones' => 'Paciente estable', 'alergias' => 'Penicilina', 'id_usuario_medico' => $medicos_disponibles[0] ?? 7],
    ['id' => 'HM002', 'ci_paciente' => $pacientes_disponibles[1] ?? 234567890, 'fecha' => '2024-03-14', 'detalle' => 'Control prenatal', 'observaciones' => 'Embarazo saludable', 'alergias' => 'Ninguna', 'id_usuario_medico' => $medicos_disponibles[1] ?? 1],
];

$historial_insertados = 0;
foreach ($historial_medicos as $historial) {
    try {
        DB::table('historial_medicos')->insert($historial);
        $historial_insertados++;
        echo "- Historial {$historial['id']} insertado correctamente\n";
    } catch (Exception $e) {
        echo "Error insertando historial {$historial['id']}: " . $e->getMessage() . "\n";
    }
}
echo "Historial médico insertado: {$historial_insertados}\n\n";

echo "¡Seeders finales completados!\n";
echo "\nResumen:\n";
echo "- Enfermeras: {$enfermeras_insertadas}\n";
echo "- Recetas: {$recetas_insertadas}\n";
echo "- Historial médico: {$historial_insertados}\n";
