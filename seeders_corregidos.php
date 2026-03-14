<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Conectar a la base de datos
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Ejecutando seeders corregidos...\n\n";

// Obtener IDs de usuarios disponibles (que no estén en medicos)
echo "1. Obteniendo IDs de usuarios disponibles...\n";
$usuarios_usados = DB::table('medicos')->pluck('id_usuario')->toArray();
$usuarios_disponibles = DB::table('users')
    ->whereNotIn('id', $usuarios_usados)
    ->pluck('id')
    ->take(20)
    ->toArray();

echo "Usuarios disponibles: " . implode(', ', $usuarios_disponibles) . "\n\n";

// Insertar médicos con IDs disponibles
echo "2. Insertando médicos corregidos...\n";
$medicos_corregidos = [
    ['ci' => 12345678, 'id_usuario' => $usuarios_disponibles[0] ?? 99, 'telefono' => 5551234, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
    ['ci' => 23456789, 'id_usuario' => $usuarios_disponibles[1] ?? 98, 'telefono' => 5555678, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
    ['ci' => 34567890, 'id_usuario' => $usuarios_disponibles[2] ?? 97, 'telefono' => 5559012, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
    ['ci' => 45678901, 'id_usuario' => $usuarios_disponibles[3] ?? 96, 'telefono' => 5553456, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
    ['ci' => 56789012, 'id_usuario' => $usuarios_disponibles[4] ?? 95, 'telefono' => 5557890, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
];

$medicos_insertados = 0;
foreach ($medicos_corregidos as $medico) {
    try {
        DB::table('medicos')->insert($medico);
        $medicos_insertados++;
    } catch (Exception $e) {
        echo "Error insertando médico CI {$medico['ci']}: " . $e->getMessage() . "\n";
    }
}
echo "Médicos insertados: {$medicos_insertados}\n\n";

// Insertar internos
echo "3. Insertando internos...\n";
$internos = [
    ['ci' => 98765432, 'id_usuario_medico' => $usuarios_disponibles[5] ?? 94, 'nombre' => 'Roberto Sánchez López', 'fecha_inicio' => '2024-01-15', 'fecha_fin' => '2024-06-15', 'telefono' => 5554321, 'lugar_asignado' => 'Servicio de Medicina Interna', 'detalle' => 'Rotación clínica general', 'estado_formulario' => 'Aprobado'],
    ['ci' => 87654321, 'id_usuario_medico' => $usuarios_disponibles[6] ?? 93, 'nombre' => 'Ana Martínez Gómez', 'fecha_inicio' => '2024-02-01', 'fecha_fin' => '2024-07-01', 'telefono' => 5555432, 'lugar_asignado' => 'Servicio de Pediatría', 'detalle' => 'Rotación pediátrica', 'estado_formulario' => 'Aprobado'],
];

$internos_insertados = 0;
foreach ($internos as $interno) {
    try {
        DB::table('internos')->insert($interno);
        $internos_insertados++;
    } catch (Exception $e) {
        echo "Error insertando interno CI {$interno['ci']}: " . $e->getMessage() . "\n";
    }
}
echo "Internos insertados: {$internos_insertados}\n\n";

// Insertar enfermeras
echo "4. Insertando enfermeras...\n";
$enfermeras = [
    ['ci' => 11223344, 'id_usuario' => $usuarios_disponibles[7] ?? 92, 'telefono' => 5551111, 'tipo' => 'Enfermera General', 'estado' => 'Activo', 'id_asistente' => null],
    ['ci' => 22334455, 'id_usuario' => $usuarios_disponibles[8] ?? 91, 'telefono' => 5552222, 'tipo' => 'Enfermera Jefe', 'estado' => 'Activo', 'id_asistente' => null],
];

$enfermeras_insertadas = 0;
foreach ($enfermeras as $enfermera) {
    try {
        DB::table('enfermeras')->insert($enfermera);
        $enfermeras_insertadas++;
    } catch (Exception $e) {
        echo "Error insertando enfermera CI {$enfermera['ci']}: " . $e->getMessage() . "\n";
    }
}
echo "Enfermeras insertadas: {$enfermeras_insertadas}\n\n";

// Insertar triages
echo "5. Insertando triages...\n";
$triages = [
    ['id' => 'TRI001', 'color' => 'Rojo', 'descripcion' => 'Reanimación inmediata', 'prioridad' => 'Alta', 'id_usuario' => $usuarios_disponibles[0] ?? 99],
    ['id' => 'TRI002', 'color' => 'Naranja', 'descripcion' => 'Emergencia urgente', 'prioridad' => 'Alta', 'id_usuario' => $usuarios_disponibles[1] ?? 98],
    ['id' => 'TRI003', 'color' => 'Amarillo', 'descripcion' => 'Urgencia', 'prioridad' => 'Media', 'id_usuario' => $usuarios_disponibles[2] ?? 97],
    ['id' => 'TRI004', 'color' => 'Verde', 'descripcion' => 'Urgencia menor', 'prioridad' => 'Baja', 'id_usuario' => $usuarios_disponibles[3] ?? 96],
    ['id' => 'TRI005', 'color' => 'Azul', 'descripcion' => 'No urgente', 'prioridad' => 'Baja', 'id_usuario' => $usuarios_disponibles[4] ?? 95],
];

$triages_insertados = 0;
foreach ($triages as $triage) {
    try {
        DB::table('triages')->insert($triage);
        $triages_insertados++;
    } catch (Exception $e) {
        echo "Error insertando triage {$triage['id']}: " . $e->getMessage() . "\n";
    }
}
echo "Triages insertados: {$triages_insertados}\n\n";

// Insertar pacientes
echo "6. Insertando pacientes...\n";
$pacientes = [
    ['ci' => 123456789, 'nombre' => 'Juan Carlos Pérez Rodríguez', 'sexo' => 'Masculino', 'direccion' => 'Av. Principal #123, Zona 1', 'telefono' => 5551234, 'correo' => 'juan.perez@email.com', 'codigo_seguro' => '1001', 'id_triage' => 'TRI004', 'codigo_registro' => 'REG001'],
    ['ci' => 234567890, 'nombre' => 'María Elena García López', 'sexo' => 'Femenino', 'direccion' => 'Calle Secundaria #456, Zona 2', 'telefono' => 5555678, 'correo' => 'maria.garcia@email.com', 'codigo_seguro' => '1002', 'id_triage' => 'TRI003', 'codigo_registro' => 'REG002'],
    ['ci' => 345678901, 'nombre' => 'Carlos Andrés Rodríguez Martínez', 'sexo' => 'Masculino', 'direccion' => 'Plaza Central #789, Zona 3', 'telefono' => 5559012, 'correo' => 'carlos.rodriguez@email.com', 'codigo_seguro' => '1003', 'id_triage' => 'TRI002', 'codigo_registro' => 'REG003'],
];

$pacientes_insertados = 0;
foreach ($pacientes as $paciente) {
    try {
        DB::table('pacientes')->insert($paciente);
        $pacientes_insertados++;
    } catch (Exception $e) {
        echo "Error insertando paciente CI {$paciente['ci']}: " . $e->getMessage() . "\n";
    }
}
echo "Pacientes insertados: {$pacientes_insertados}\n\n";

// Insertar emergencias
echo "7. Insertando emergencias...\n";
$emergencias = [
    ['nro' => 'EM001', 'descripcion' => 'Paro cardiorrespiratorio', 'estado' => 'Atendido', 'tipo' => 'Crítica', 'id_triage' => 'TRI001'],
    ['nro' => 'EM002', 'descripcion' => 'Trauma severo por accidente', 'estado' => 'En tratamiento', 'tipo' => 'Grave', 'id_triage' => 'TRI002'],
    ['nro' => 'EM003', 'descripcion' => 'Dolor torácico agudo', 'estado' => 'En observación', 'tipo' => 'Urgente', 'id_triage' => 'TRI002'],
    ['nro' => 'EM004', 'descripcion' => 'Fractura expuesta', 'estado' => 'Atendido', 'tipo' => 'Grave', 'id_triage' => 'TRI002'],
    ['nro' => 'EM005', 'descripcion' => 'Hemorragia digestiva', 'estado' => 'En tratamiento', 'tipo' => 'Grave', 'id_triage' => 'TRI002'],
];

$emergencias_insertadas = 0;
foreach ($emergencias as $emergencia) {
    try {
        DB::table('emergencias')->insert($emergencia);
        $emergencias_insertadas++;
    } catch (Exception $e) {
        echo "Error insertando emergencia {$emergencia['nro']}: " . $e->getMessage() . "\n";
    }
}
echo "Emergencias insertadas: {$emergencias_insertadas}\n\n";

echo "¡Seeders corregidos ejecutados exitosamente!\n";
echo "\nResumen final:\n";
echo "- Médicos: {$medicos_insertados}\n";
echo "- Internos: {$internos_insertados}\n";
echo "- Enfermeras: {$enfermeras_insertadas}\n";
echo "- Triages: {$triages_insertados}\n";
echo "- Pacientes: {$pacientes_insertados}\n";
echo "- Emergencias: {$emergencias_insertadas}\n";
