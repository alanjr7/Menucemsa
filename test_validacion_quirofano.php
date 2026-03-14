<?php

require_once 'vendor/autoload.php';

use App\Models\CitaQuirurgica;
use App\Models\Quirofano;
use App\Models\Paciente;
use App\Models\Medico;
use Illuminate\Support\Facades\DB;

echo "=== Test de Validación de Disponibilidad de Quirófano ===\n\n";

// Test 1: Verificar que el método validarDisponibilidadQuirofano existe
echo "1. Verificando método de validación...\n";
if (method_exists(CitaQuirurgica::class, 'validarDisponibilidadQuirofano')) {
    echo "✓ Método validarDisponibilidadQuirofano existe\n";
} else {
    echo "✗ Método validarDisponibilidadQuirofano no encontrado\n";
}

// Test 2: Crear una cita de prueba
echo "\n2. Creando cita de prueba...\n";
$cita = new CitaQuirurgica([
    'ci_paciente' => 12345678,
    'ci_cirujano' => 87654321,
    'nro_quirofano' => 1,
    'tipo_cirugia' => 'menor',
    'fecha' => '2026-03-15',
    'hora_inicio_estimada' => '15:19',
    'estado' => 'programada'
]);

echo "✓ Cita de prueba creada\n";
echo "  - Paciente: {$cita->ci_paciente}\n";
echo "  - Cirujano: {$cita->ci_cirujano}\n";
echo "  - Quirófano: {$cita->nro_quirofano}\n";
echo "  - Fecha: {$cita->fecha}\n";
echo "  - Hora: {$cita->hora_inicio_estimada}\n";
echo "  - Tipo: {$cita->tipo_cirugia}\n";

// Test 3: Verificar duración estimada
echo "\n3. Verificando duración estimada...\n";
$duracion = $cita->duracion_estimada;
echo "✓ Duración estimada: {$duracion} minutos\n";

$horaFin = $cita->hora_fin_estimada;
echo "✓ Hora fin estimada: {$horaFin->format('H:i')}\n";

// Test 4: Simular validación de disponibilidad
echo "\n4. Simulando validación de disponibilidad...\n";
try {
    $conflicto = $cita->validarDisponibilidadQuirofano();
    if ($conflicto) {
        echo "✗ Hay conflicto de horario en el quirófano\n";
    } else {
        echo "✓ No hay conflictos de horario\n";
    }
} catch (Exception $e) {
    echo "✗ Error en validación: " . $e->getMessage() . "\n";
}

// Test 5: Crear segunda cita en mismo horario para detectar conflicto
echo "\n5. Creando segunda cita en mismo horario...\n";
$cita2 = new CitaQuirurgica([
    'ci_paciente' => 87654321,
    'ci_cirujano' => 12345678,
    'nro_quirofano' => 1,  // Mismo quirófano
    'tipo_cirugia' => 'menor',
    'fecha' => '2026-03-15',  // Misma fecha
    'hora_inicio_estimada' => '15:21',  // Horario cercano
    'estado' => 'programada'
]);

echo "✓ Segunda cita creada\n";
echo "  - Paciente: {$cita2->ci_paciente}\n";
echo "  - Quirófano: {$cita2->nro_quirofano}\n";
echo "  - Fecha: {$cita2->fecha}\n";
echo "  - Hora: {$cita2->hora_inicio_estimada}\n";

// Test 6: Verificar que se detecta el conflicto
echo "\n6. Verificando detección de conflictos...\n";
try {
    // Simular que la primera cita ya existe
    $conflicto2 = $cita2->validarDisponibilidadQuirofano();
    if ($conflicto2) {
        echo "✓ Conflicto detectado correctamente\n";
    } else {
        echo "✗ No se detectó el conflicto esperado\n";
    }
} catch (Exception $e) {
    echo "✗ Error en validación: " . $e->getMessage() . "\n";
}

// Test 7: Verificar cita en diferente quirofano mismo horario
echo "\n7. Verificando cita en diferente quirofano mismo horario...\n";
$cita3 = new CitaQuirurgica([
    'ci_paciente' => 11111111,
    'ci_cirujano' => 22222222,
    'nro_quirofano' => 2,  // Diferente quirofano
    'tipo_cirugia' => 'menor',
    'fecha' => '2026-03-15',  // Misma fecha
    'hora_inicio_estimada' => '15:19',  // Mismo horario
    'estado' => 'programada'
]);

try {
    $conflicto3 = $cita3->validarDisponibilidadQuirofano();
    if ($conflicto3) {
        echo "✗ Se detectó conflicto cuando no debería haberlo\n";
    } else {
        echo "✓ No hay conflicto (diferente quirofano)\n";
    }
} catch (Exception $e) {
    echo "✗ Error en validación: " . $e->getMessage() . "\n";
}

echo "\n=== Test Completado ===\n";
echo "La validación debería:\n";
echo "- Permitir cirugías en el mismo horario si están en quirofanos diferentes\n";
echo "- Rechazar cirugías en el mismo quirofano en horarios solapados\n";
echo "- Permitir cirugías en el mismo quirofano en horarios diferentes\n";
