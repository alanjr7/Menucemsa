<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Conectar a la base de datos
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Ejecutando seeders básicos sin relaciones...\n\n";

// 1. Insertar médicos básicos (sin relaciones)
echo "1. Insertando médicos básicos...\n";
$medicos_basicos = [
    ['ci' => 12345678, 'id_usuario' => 3, 'telefono' => 5551234, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
    ['ci' => 23456789, 'id_usuario' => 9, 'telefono' => 5555678, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
    ['ci' => 34567890, 'id_usuario' => 13, 'telefono' => 5559012, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
    ['ci' => 45678901, 'id_usuario' => 16, 'telefono' => 5553456, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
    ['ci' => 56789012, 'id_usuario' => 17, 'telefono' => 5557890, 'estado' => 'Activo', 'id_asistente' => null, 'codigo_especialidad' => null],
];

foreach ($medicos_basicos as $medico) {
    DB::table('medicos')->insert($medico);
}
echo "Médicos insertados: " . count($medicos_basicos) . "\n\n";

// 2. Insertar internos básicos
echo "2. Insertando internos básicos...\n";
$internos_basicos = [
    ['ci' => 98765432, 'id_usuario_medico' => 21, 'nombre' => 'Roberto Sánchez López', 'fecha_inicio' => '2024-01-15', 'fecha_fin' => '2024-06-15', 'telefono' => 5554321, 'lugar_asignado' => 'Servicio de Medicina Interna', 'detalle' => 'Rotación clínica general', 'estado_formulario' => 'Aprobado'],
    ['ci' => 87654321, 'id_usuario_medico' => 13, 'nombre' => 'Ana Martínez Gómez', 'fecha_inicio' => '2024-02-01', 'fecha_fin' => '2024-07-01', 'telefono' => 5555432, 'lugar_asignado' => 'Servicio de Pediatría', 'detalle' => 'Rotación pediátrica', 'estado_formulario' => 'Aprobado'],
];

foreach ($internos_basicos as $interno) {
    DB::table('internos')->insert($interno);
}
echo "Internos insertados: " . count($internos_basicos) . "\n\n";

// 3. Insertar enfermeras básicas
echo "3. Insertando enfermeras básicas...\n";
$enfermeras_basicas = [
    ['ci' => 11223344, 'id_usuario' => 4, 'telefono' => 5551111, 'tipo' => 'Enfermera General', 'estado' => 'Activo', 'id_asistente' => null],
    ['ci' => 22334455, 'id_usuario' => 6, 'telefono' => 5552222, 'tipo' => 'Enfermera Jefe', 'estado' => 'Activo', 'id_asistente' => null],
];

foreach ($enfermeras_basicas as $enfermera) {
    DB::table('enfermeras')->insert($enfermera);
}
echo "Enfermeras insertadas: " . count($enfermeras_basicas) . "\n\n";

// 4. Insertar triages básicos
echo "4. Insertando triages básicos...\n";
$triages_basicos = [
    ['id' => 'TRI001', 'color' => 'Rojo', 'descripcion' => 'Reanimación inmediata', 'prioridad' => 'Alta', 'id_usuario' => 22],
    ['id' => 'TRI002', 'color' => 'Naranja', 'descripcion' => 'Emergencia urgente', 'prioridad' => 'Alta', 'id_usuario' => 22],
    ['id' => 'TRI003', 'color' => 'Amarillo', 'descripcion' => 'Urgencia', 'prioridad' => 'Media', 'id_usuario' => 5],
    ['id' => 'TRI004', 'color' => 'Verde', 'descripcion' => 'Urgencia menor', 'prioridad' => 'Baja', 'id_usuario' => 5],
    ['id' => 'TRI005', 'color' => 'Azul', 'descripcion' => 'No urgente', 'prioridad' => 'Baja', 'id_usuario' => 4],
];

foreach ($triages_basicos as $triage) {
    DB::table('triages')->insert($triage);
}
echo "Triages insertados: " . count($triages_basicos) . "\n\n";

// 5. Insertar pacientes básicos
echo "5. Insertando pacientes básicos...\n";
$pacientes_basicos = [
    ['ci' => 123456789, 'nombre' => 'Juan Carlos Pérez Rodríguez', 'sexo' => 'Masculino', 'direccion' => 'Av. Principal #123, Zona 1', 'telefono' => 5551234, 'correo' => 'juan.perez@email.com', 'codigo_seguro' => '1001', 'id_triage' => 'TRI004', 'codigo_registro' => 'REG001'],
    ['ci' => 234567890, 'nombre' => 'María Elena García López', 'sexo' => 'Femenino', 'direccion' => 'Calle Secundaria #456, Zona 2', 'telefono' => 5555678, 'correo' => 'maria.garcia@email.com', 'codigo_seguro' => '1002', 'id_triage' => 'TRI003', 'codigo_registro' => 'REG002'],
    ['ci' => 345678901, 'nombre' => 'Carlos Andrés Rodríguez Martínez', 'sexo' => 'Masculino', 'direccion' => 'Plaza Central #789, Zona 3', 'telefono' => 5559012, 'correo' => 'carlos.rodriguez@email.com', 'codigo_seguro' => '1003', 'id_triage' => 'TRI002', 'codigo_registro' => 'REG003'],
];

foreach ($pacientes_basicos as $paciente) {
    DB::table('pacientes')->insert($paciente);
}
echo "Pacientes insertados: " . count($pacientes_basicos) . "\n\n";

// 6. Insertar emergencias básicas
echo "6. Insertando emergencias básicas...\n";
$emergencias_basicas = [
    ['nro' => 'EM001', 'descripcion' => 'Paro cardiorrespiratorio', 'estado' => 'Atendido', 'tipo' => 'Crítica', 'id_triage' => 'TRI001'],
    ['nro' => 'EM002', 'descripcion' => 'Trauma severo por accidente', 'estado' => 'En tratamiento', 'tipo' => 'Grave', 'id_triage' => 'TRI002'],
    ['nro' => 'EM003', 'descripcion' => 'Dolor torácico agudo', 'estado' => 'En observación', 'tipo' => 'Urgente', 'id_triage' => 'TRI002'],
    ['nro' => 'EM004', 'descripcion' => 'Fractura expuesta', 'estado' => 'Atendido', 'tipo' => 'Grave', 'id_triage' => 'TRI002'],
    ['nro' => 'EM005', 'descripcion' => 'Hemorragia digestiva', 'estado' => 'En tratamiento', 'tipo' => 'Grave', 'id_triage' => 'TRI002'],
];

foreach ($emergencias_basicas as $emergencia) {
    DB::table('emergencias')->insert($emergencia);
}
echo "Emergencias insertadas: " . count($emergencias_basicas) . "\n\n";

echo "Seeders básicos ejecutados correctamente!\n";
echo "\nResumen:\n";
echo "- Médicos: " . count($medicos_basicos) . "\n";
echo "- Internos: " . count($internos_basicos) . "\n";
echo "- Enfermeras: " . count($enfermeras_basicas) . "\n";
echo "- Triages: " . count($triages_basicos) . "\n";
echo "- Pacientes: " . count($pacientes_basicos) . "\n";
echo "- Emergencias: " . count($emergencias_basicas) . "\n";
