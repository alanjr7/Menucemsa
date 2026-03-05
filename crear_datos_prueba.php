<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Especialidad;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Medico;

// Crear especialidades
$especialidades = [
    ['nombre' => 'Medicina General', 'descripcion' => 'Medicina general y consultas de rutina'],
    ['nombre' => 'Cardiología', 'descripcion' => 'Especialidad del corazón y sistema circulatorio'],
    ['nombre' => 'Pediatría', 'descripcion' => 'Atención médica infantil'],
];

foreach ($especialidades as $esp) {
    Especialidad::firstOrCreate(['nombre' => $esp['nombre']], $esp);
}

echo "Especialidades creadas\n";

// Crear citas de prueba
$pacientes = Paciente::limit(2)->get();
$medicos = Medico::limit(1)->get();
$especialidades = Especialidad::limit(3)->get();

if ($pacientes->count() >= 1 && $medicos->count() >= 1) {
    $citasData = [
        [
            'ci_paciente' => $pacientes[0]->ci,
            'ci_medico' => $medicos[0]->ci,
            'codigo_especialidad' => $especialidades[0]->codigo,
            'fecha' => now()->toDateString(),
            'hora' => '08:00:00',
            'motivo' => 'Control de presión arterial',
            'estado' => 'programado',
            'confirmado' => true,
            'id_usuario_registro' => 1
        ],
        [
            'ci_paciente' => $pacientes[0]->ci,
            'ci_medico' => $medicos[0]->ci,
            'codigo_especialidad' => $especialidades[1]->codigo,
            'fecha' => now()->toDateString(),
            'hora' => '10:30:00',
            'motivo' => 'Dolor de cabeza persistente',
            'estado' => 'programado',
            'confirmado' => false,
            'id_usuario_registro' => 1
        ]
    ];

    foreach ($citasData as $citaData) {
        Cita::firstOrCreate([
            'ci_paciente' => $citaData['ci_paciente'],
            'fecha' => $citaData['fecha'],
            'hora' => $citaData['hora']
        ], $citaData);
    }

    echo "Citas de prueba creadas\n";
}

echo "Datos de prueba creados exitosamente\n";
