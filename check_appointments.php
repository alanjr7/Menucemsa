<?php

require_once 'vendor/autoload.php';

use App\Models\CitaQuirurgica;

echo "=== Checking Existing Surgical Appointments ===\n\n";

$citas = CitaQuirurgica::all();

echo "Total appointments: " . $citas->count() . "\n\n";

foreach ($citas as $cita) {
    echo "ID: {$cita->id}\n";
    echo "  Date: {$cita->fecha}\n";
    echo "  Operating Room: {$cita->nro_quirofano}\n";
    echo "  Time: {$cita->hora_inicio_estimada}\n";
    echo "  Duration: {$cita->duracion_estimada} mins\n";
    echo "  Status: {$cita->estado}\n";
    echo "  Patient: {$cita->ci_paciente}\n";
    echo "  Surgeon: {$cita->ci_cirujano}\n";
    echo "  -----------------------------------\n";
}

echo "\n=== End of Appointments ===\n";
