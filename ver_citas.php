<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $citas = DB::table('citas')
        ->join('pacientes', 'citas.ci_paciente', '=', 'pacientes.ci')
        ->join('medicos', 'citas.ci_medico', '=', 'medicos.ci')
        ->join('users', 'medicos.id_usuario', '=', 'users.id')
        ->join('especialidades', 'citas.codigo_especialidad', '=', 'especialidades.codigo')
        ->select(
            'citas.*',
            'pacientes.nombre as paciente_nombre',
            'users.name as medico_nombre',
            'especialidades.nombre as especialidad_nombre'
        )
        ->get();

    echo "Citas creadas:\n";
    foreach ($citas as $cita) {
        echo "- {$cita->fecha} {$cita->hora}: {$cita->paciente_nombre} con Dr. {$cita->medico_nombre} ({$cita->especialidad_nombre}) - {$cita->motivo}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
