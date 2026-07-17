<?php

namespace App\Console\Commands;

use App\Models\AlmacenCatalogo;
use App\Models\CodigoItem;
use App\Models\CuentaCobroDetalle;
use App\Models\IngresoPrecio;
use App\Models\Procedimiento;
use App\Models\TipoCirugia;
use App\Support\CodigoProducto;
use App\Support\ResolverCodigoItem;
use Illuminate\Console\Command;

/**
 * Asigna códigos internos a filas que aún no los tienen, sin pisar los existentes.
 *
 * Idempotente: correr dos veces no cambia nada. Cubre:
 *  - Catálogos (filas creadas por seeders/inserts raw que saltan el hook Eloquent).
 *  - cuenta_cobro_detalles históricos (cargos creados antes de esta feature).
 *
 * NO toca montos ni nada inmutable: sólo rellena el código que faltaba.
 */
class BackfillCodigosItem extends Command
{
    protected $signature = 'codigos:backfill';

    protected $description = 'Rellena los códigos internos de producto/servicio faltantes (catálogos + cargos)';

    /** Catálogos con el trait GeneraCodigoCatalogo (familia derivada de su constante). */
    private const CATALOGOS = [
        AlmacenCatalogo::class,
        IngresoPrecio::class,
        Procedimiento::class,
        TipoCirugia::class,
        CodigoItem::class,
    ];

    public function handle(): int
    {
        foreach (self::CATALOGOS as $modelo) {
            $n = 0;
            $modelo::whereNull('codigo')->chunkById(200, function ($filas) use (&$n) {
                foreach ($filas as $fila) {
                    $fila->codigo = CodigoProducto::format($fila::FAMILIA_CODIGO, (int) $fila->id);
                    $fila->saveQuietly();
                    $n++;
                }
            });
            $this->line(sprintf('  %-22s %d código(s) asignado(s)', class_basename($modelo) . ':', $n));
        }

        // Cargos históricos sin código (incluye los deshabilitados, para que el
        // comprobante de cualquier cuenta vieja también muestre el código).
        $n = 0;
        CuentaCobroDetalle::withoutGlobalScope('habilitado')
            ->whereNull('codigo_item')
            ->chunkById(200, function ($detalles) use (&$n) {
                foreach ($detalles as $detalle) {
                    $codigo = ResolverCodigoItem::paraDetalle($detalle);
                    if ($codigo) {
                        $detalle->codigo_item = $codigo;
                        $detalle->saveQuietly();
                        $n++;
                    }
                }
            });
        $this->line(sprintf('  %-22s %d cargo(s) actualizado(s)', 'CuentaCobroDetalle:', $n));

        $this->info('Backfill de códigos completado.');

        return self::SUCCESS;
    }
}
