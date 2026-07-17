<?php

namespace App\Console\Commands;

use App\Models\CitaQuirurgica;
use App\Support\Money;
use Illuminate\Console\Command;

/**
 * Recalcula citas_quirurgicas.costo_final desde los cargos reales de la cuenta.
 *
 * Corrige el drift histórico del cache denormalizado: antes el total se calculaba
 * con fórmulas divergentes (omitían insumos o equipos), por lo que algunas filas
 * quedaron por debajo de lo realmente facturado. Esta es la misma fórmula única
 * que ahora mantiene el evento de dominio en CuentaCobroDetalle.
 *
 * Idempotente y no destructivo: sólo reescribe costo_final cuando difiere; nunca
 * toca los cargos ni los montos cobrados.
 */
class RecalcularCostosCirugia extends Command
{
    protected $signature = 'quirofano:recalcular-costos';

    protected $description = 'Recalcula costo_final de las cirugías desde sus cargos reales (corrige drift del denormalizado)';

    public function handle(): int
    {
        $revisadas = 0;
        $corregidas = 0;

        CitaQuirurgica::query()->chunkById(200, function ($citas) use (&$revisadas, &$corregidas) {
            foreach ($citas as $cita) {
                $revisadas++;
                $antes = Money::format($cita->costo_final ?? '0');
                $despues = $cita->recalcularCostoFinal();

                if (Money::cmp($antes, $despues) !== 0) {
                    $corregidas++;
                    $this->line(sprintf('  Cita #%-6d %s → %s', $cita->id, $antes, $despues));
                }
            }
        });

        $this->info("Listo. {$revisadas} cirugía(s) revisada(s), {$corregidas} corregida(s).");

        return self::SUCCESS;
    }
}
