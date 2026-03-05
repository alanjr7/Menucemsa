<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Obtener datos
    $pacientes = DB::table('pacientes')->limit(2)->get();
    $medicos = DB::table('medicos')->limit(1)->get();

    if ($pacientes->count() >= 1 && $medicos->count() >= 1) {
        // Insertar citas con códigos correctos
        DB::table('citas')->insertOrIgnore([
            [
                'ci_paciente' => $pacientes[0]->ci,
                'ci_medico' => (int)$medicos[0]->ci,
                'codigo_especialidad' => 'GENERAL',
                'fecha' => date('Y-m-d'),
                'hora' => '08:00:00',
                'motivo' => 'Control de presión arterial',
                'estado' => 'programado',
                'confirmado' => 1,
                'id_usuario_registro' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ci_paciente' => $pacientes[0]->ci,
                'ci_medico' => (int)$medicos[0]->ci,
                'codigo_especialidad' => 'CAR-003',
                'fecha' => date('Y-m-d'),
                'hora' => '10:30:00',
                'motivo' => 'Dolor de cabeza persistente',
                'estado' => 'programado',
                'confirmado' => 0,
                'id_usuario_registro' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        echo "Citas de prueba insertadas exitosamente\n";
    } else {
        echo "No hay suficientes pacientes o médicos\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
