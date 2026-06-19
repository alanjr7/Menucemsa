<?php

namespace Database\Seeders;

use App\Models\IngresoPrecio;
use Illuminate\Database\Seeder;

/**
 * Materializa los 4 tipos de admisión con sus precios por defecto (los mismos
 * fallbacks que ya usa el código de cobro) para que cada admisión tenga un
 * código interno familia 2 estable que el comprobante pueda imprimir.
 *
 * Idempotente: `firstOrCreate` por `tipo_ingreso` NO pisa los precios que el
 * admin haya ajustado en "Precio al Ingreso". El código familia 2 lo asigna el
 * trait GeneraCodigoCatalogo (o el backfill, bajo WithoutModelEvents).
 */
class IngresoPrecioSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'consulta_externa' => 150.00,
            'enfermeria'       => 30.00,
            'emergencia'       => 200.00,
            'internacion'      => 150.00,
        ];

        foreach ($defaults as $tipo => $precio) {
            IngresoPrecio::firstOrCreate(
                ['tipo_ingreso' => $tipo],
                ['precio' => $precio, 'activo' => true],
            );
        }
    }
}
