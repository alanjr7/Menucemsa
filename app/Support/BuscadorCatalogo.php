<?php

namespace App\Support;

use App\Models\AlmacenCatalogo;
use App\Models\AlmacenLote;
use App\Models\CodigoItem;
use App\Models\IngresoPrecio;
use App\Models\Procedimiento;
use App\Models\TipoCirugia;

/**
 * Buscador único de ítems facturables sobre todos los catálogos del sistema.
 *
 * Fuente compartida por la pantalla de Correcciones (AjustesPacienteController)
 * y por las Proformas: ambas necesitan el mismo autocompletado concepto + código
 * interno + precio sugerido + tipo_item. Tener un solo buscador evita que las dos
 * vistas se desincronicen cuando se agregue una nueva familia de códigos.
 *
 * Devuelve un arreglo de filas con la forma:
 *   ['codigo', 'descripcion', 'precio' (string|null), 'tipo_item', 'grupo']
 */
final class BuscadorCatalogo
{
    /** Mínimo de caracteres para disparar la búsqueda. */
    public const MIN_LONGITUD = 2;

    public static function buscar(string $q): array
    {
        $q = trim($q);
        if (mb_strlen($q) < self::MIN_LONGITUD) {
            return [];
        }

        $like = '%' . $q . '%';
        $resultados = [];

        // Medicamentos / insumos (familia 1). Precio sugerido = precio de venta de
        // un lote vigente del catálogo (puede no existir si aún no tiene stock).
        foreach (AlmacenCatalogo::where('activo', true)->where('nombre', 'LIKE', $like)
                     ->whereNotNull('codigo')->orderBy('nombre')->limit(8)->get() as $c) {
            $precio = AlmacenLote::where('catalogo_id', $c->id)
                ->where('precio_venta', '>', 0)
                ->orderByDesc('id')
                ->value('precio_venta');

            $resultados[] = [
                'codigo'      => $c->codigo,
                'descripcion' => $c->nombre,
                'precio'      => $precio !== null ? (string) $precio : null,
                'tipo_item'   => $c->tipo === 'insumo' ? 'material' : 'medicamento',
                'grupo'       => $c->tipo === 'insumo' ? 'Insumo' : 'Medicamento',
            ];
        }

        // Procedimientos (familia 5)
        foreach (Procedimiento::where('activo', true)->where('nombre', 'LIKE', $like)
                     ->whereNotNull('codigo')->orderBy('nombre')->limit(8)->get() as $p) {
            $resultados[] = [
                'codigo'      => $p->codigo,
                'descripcion' => $p->nombre,
                'precio'      => (string) $p->precio,
                'tipo_item'   => 'procedimiento',
                'grupo'       => 'Procedimiento',
            ];
        }

        // Tipos de cirugía (familia 6) — precio sugerido = costo base
        foreach (TipoCirugia::where('activo', true)->where('nombre', 'LIKE', $like)
                     ->whereNotNull('codigo')->orderBy('nombre')->limit(5)->get() as $t) {
            $resultados[] = [
                'codigo'      => $t->codigo,
                'descripcion' => 'Cirugía ' . $t->nombre,
                'precio'      => (string) $t->costo_base,
                'tipo_item'   => 'procedimiento',
                'grupo'       => 'Cirugía',
            ];
        }

        // Admisiones (familia 2)
        foreach (IngresoPrecio::where('activo', true)->whereNotNull('codigo')
                     ->where('tipo_ingreso', 'LIKE', $like)->limit(5)->get() as $i) {
            $resultados[] = [
                'codigo'      => $i->codigo,
                'descripcion' => 'Admisión de ' . $i->tipo_ingreso_label,
                'precio'      => (string) $i->precio,
                'tipo_item'   => 'servicio',
                'grupo'       => 'Admisión',
            ];
        }

        // Ítems ya registrados en el diccionario (familia 9): lab, imagen, etc.
        foreach (CodigoItem::whereNotNull('codigo')
                     ->where('descripcion_original', 'LIKE', $like)->orderBy('descripcion_original')->limit(8)->get() as $d) {
            $resultados[] = [
                'codigo'      => $d->codigo,
                'descripcion' => $d->descripcion_original,
                'precio'      => null,
                'tipo_item'   => $d->tipo_item,
                'grupo'       => 'Registrado',
            ];
        }

        return $resultados;
    }
}
