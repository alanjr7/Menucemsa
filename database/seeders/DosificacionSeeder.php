<?php

namespace Database\Seeders;

use App\Models\Dosificacion;
use Illuminate\Database\Seeder;

class DosificacionSeeder extends Seeder
{
    public function run(): void
    {
        // Placeholder de dosificación. Reemplazar con la dosificación REAL emitida por
        // el SIN cuando la clínica adopte la facturación (SFE).
        Dosificacion::firstOrCreate(
            ['activa' => true],
            [
                'modalidad' => 'computarizada_en_linea',
                'numero_autorizacion' => 'PENDIENTE-SIN',
                'llave_dosificacion' => 'PENDIENTE-SIN',
                'rango_desde' => 1,
                'rango_hasta' => null,
                'fecha_limite_emision' => null,
                'observaciones' => 'Placeholder — reemplazar con la dosificación real del SIN al adoptar SFE.',
            ]
        );
    }
}
