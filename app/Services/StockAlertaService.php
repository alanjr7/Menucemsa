<?php

namespace App\Services;

use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use App\Models\UserNotification;

/**
 * Alertas de inventario para el rol almacenista: stock bajo / agotado (por evento de
 * descuento de AlmacenStock) y medicamentos vencidos / por vencer (por comando programado).
 *
 * Fuente única de la lógica de notificación de almacén. NO crea las notificaciones a mano:
 * delega en NotificationService::notifyRole, que entrega una a cada usuario almacenista.
 */
class StockAlertaService
{
    /** Rol que recibe las alertas de inventario. */
    public const ROL_DESTINO = 'almacenista';

    /** Ventana por defecto (días) para considerar un lote "por vencer". */
    public const DIAS_POR_VENCER = 30;

    /**
     * Evalúa un descenso de stock y notifica si cruzó el umbral mínimo o se agotó.
     * Se llama desde el evento `updated` de AlmacenStock SOLO cuando la cantidad baja.
     * La detección por cruce (antes/ahora) evita repetir la alerta en cada descuento
     * mientras ya está bajo: solo dispara en la transición.
     */
    public static function evaluarDescenso(AlmacenStock $stock, int $antes, int $ahora): void
    {
        // Agotado: cruzó de "con existencias" a 0 (o menos).
        if ($antes > 0 && $ahora <= 0) {
            self::notificarStock($stock, 'stock_agotado');
            return;
        }

        // Bajo: cruzó de "sobre el mínimo" a "en o bajo el mínimo", todavía con existencias.
        // Solo aplica si hay un mínimo definido (> 0); con mínimo 0 el único evento útil es agotado.
        $min = (int) $stock->stock_minimo;
        if ($min > 0 && $ahora > 0 && $ahora <= $min && $antes > $min) {
            self::notificarStock($stock, 'stock_bajo');
        }
    }

    /**
     * Escanea lotes con existencias vencidos o por vencer y notifica al almacenista.
     * Pensado para ejecutarse a diario (comando almacen:notificar-vencimientos); deduplica
     * por lote+tipo dentro de una ventana para no repetir la misma alerta cada día.
     *
     * @return array{vencido:int, por_vencer:int} cuántas alertas nuevas se enviaron
     */
    public static function escanearVencimientos(int $dias = self::DIAS_POR_VENCER): array
    {
        $resumen = ['vencido' => 0, 'por_vencer' => 0];

        $conExistencias = fn ($q) => $q->where('cantidad_actual', '>', 0);

        AlmacenLote::vencidos()
            ->whereHas('stocks', $conExistencias)
            ->with('catalogo')
            ->get()
            ->each(function (AlmacenLote $lote) use (&$resumen) {
                if (self::notificarVencimiento($lote, 'medicamento_vencido')) {
                    $resumen['vencido']++;
                }
            });

        AlmacenLote::porVencer($dias)
            ->whereHas('stocks', $conExistencias)
            ->with('catalogo')
            ->get()
            ->each(function (AlmacenLote $lote) use (&$resumen) {
                if (self::notificarVencimiento($lote, 'medicamento_por_vencer')) {
                    $resumen['por_vencer']++;
                }
            });

        return $resumen;
    }

    private static function notificarStock(AlmacenStock $stock, string $tipo): void
    {
        $stock->loadMissing('lote.catalogo');

        $nombre = $stock->nombre;            // accessor → lote.catalogo.nombre
        $area   = $stock->ubicacion_label;   // accessor
        $unidad = $stock->unidad_medida;     // accessor

        if ($tipo === 'stock_agotado') {
            $titulo  = "Sin stock: {$nombre}";
            $mensaje = "Se agotó «{$nombre}» en {$area}.";
        } else {
            $titulo  = "Stock bajo: {$nombre}";
            $mensaje = "«{$nombre}» en {$area} quedó en {$stock->cantidad_actual} {$unidad} (mínimo {$stock->stock_minimo}).";
        }

        NotificationService::notifyRole(
            self::ROL_DESTINO,
            $tipo,
            $titulo,
            $mensaje,
            route('admin.almacen-medicamentos.reporte.bajo-stock'),
            [
                'stock_id'    => $stock->id,
                'ubicacion'   => $stock->ubicacion,
                'catalogo_id' => $stock->lote?->catalogo_id,
            ]
        );
    }

    /** @return bool true si envió una alerta nueva, false si fue deduplicada. */
    private static function notificarVencimiento(AlmacenLote $lote, string $tipo): bool
    {
        // Deduplicación: una alerta por lote+tipo cada N días (el comando corre a diario).
        if (self::yaNotificadoReciente($tipo, $lote->id, 7)) {
            return false;
        }

        $nombre = $lote->catalogo?->nombre ?? 'Medicamento';
        $fecha  = optional($lote->fecha_vencimiento)->format('d/m/Y');

        if ($tipo === 'medicamento_vencido') {
            $titulo  = "Vencido: {$nombre}";
            $mensaje = "El lote {$lote->codigo_lote} de «{$nombre}» venció el {$fecha} y aún tiene existencias.";
        } else {
            $diasRestantes = (int) now()->diffInDays($lote->fecha_vencimiento, false);
            $titulo  = "Por vencer: {$nombre}";
            $mensaje = "El lote {$lote->codigo_lote} de «{$nombre}» vence el {$fecha} (en {$diasRestantes} días).";
        }

        NotificationService::notifyRole(
            self::ROL_DESTINO,
            $tipo,
            $titulo,
            $mensaje,
            route('admin.almacen-medicamentos.reporte.vencimiento'),
            [
                'lote_id'           => $lote->id,
                'catalogo_id'       => $lote->catalogo_id,
                'fecha_vencimiento' => optional($lote->fecha_vencimiento)->toDateString(),
            ]
        );

        return true;
    }

    private static function yaNotificadoReciente(string $tipo, int $loteId, int $dias): bool
    {
        return UserNotification::where('type', $tipo)
            ->where('data->lote_id', $loteId)
            ->where('created_at', '>=', now()->subDays($dias))
            ->exists();
    }
}
