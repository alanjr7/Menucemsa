<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Support\Money;

class CuentaCobroDetalle extends Model
{
    use HasFactory;

    protected $table = 'cuenta_cobro_detalles';
    
    protected $fillable = [
        'cuenta_cobro_id',
        'tipo_item',
        'codigo_item',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'origen_type',
        'origen_id',
        'observaciones',
        'area_origen',
        'user_id',
        'deshabilitado_en',
        'deshabilitado_por',
        'motivo_deshabilitacion',
        'liquidado_en',
        'liquidado_pago_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'deshabilitado_en' => 'datetime',
        'liquidado_en' => 'datetime',
    ];

    // Relaciones
    public function cuentaCobro(): BelongsTo
    {
        return $this->belongsTo(CuentaCobro::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deshabilitadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deshabilitado_por');
    }

    public function liquidadoPago(): BelongsTo
    {
        return $this->belongsTo(PagoCuenta::class, 'liquidado_pago_id');
    }

    /** Historial de anulaciones (parciales/totales) de esta línea. */
    public function anulaciones(): HasMany
    {
        return $this->hasMany(CuentaCobroDetalleEliminado::class, 'cuenta_cobro_detalle_id');
    }

    public function origen(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    // Calcular subtotal automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detalle) {
            if (empty($detalle->subtotal)) {
                $detalle->subtotal = Money::mul($detalle->cantidad, $detalle->precio_unitario);
            }

            // Estampar el código interno de producto/servicio que se imprime en el
            // comprobante. Chokepoint ÚNICO: todo cargo (venga del camino que venga)
            // pasa por aquí. El resolver es defensivo y nunca lanza.
            if (empty($detalle->codigo_item)) {
                $detalle->codigo_item = \App\Support\ResolverCodigoItem::paraDetalle($detalle);
            }
        });

        static::updating(function ($detalle) {
            if ($detalle->isDirty('cantidad') || $detalle->isDirty('precio_unitario')) {
                $detalle->subtotal = Money::mul($detalle->cantidad, $detalle->precio_unitario);
            }
        });

        // Mantener al día el total denormalizado de la cirugía dueña del cargo.
        // Chokepoint ÚNICO: cualquier alta/edición/anulación de un cargo cuyo origen
        // sea una CitaQuirurgica —venga de ejecutar, actualizarDetalles, agregar en
        // tiempo real, anulación de cargos o ajustes de paciente— resincroniza
        // CitaQuirurgica->costo_final. Resuelve el drift del cache de raíz.
        static::saved(function ($detalle) {
            if ($detalle->wasRecentlyCreated
                || $detalle->wasChanged(['subtotal', 'cantidad', 'deshabilitado_en', 'origen_id', 'origen_type'])) {
                $detalle->sincronizarCostoCirugia();
            }
        });

        static::deleted(function ($detalle) {
            $detalle->sincronizarCostoCirugia();
        });
    }

    /**
     * Si este cargo pertenece a una cirugía, recalcula su total denormalizado.
     * Defensivo: nunca interrumpe la operación de facturación que lo disparó.
     */
    protected function sincronizarCostoCirugia(): void
    {
        if ($this->origen_type !== CitaQuirurgica::class) {
            return;
        }

        try {
            CitaQuirurgica::find($this->origen_id)?->recalcularCostoFinal();
        } catch (\Throwable $e) {
            \Log::warning('No se pudo sincronizar costo_final de cirugía: '.$e->getMessage());
        }
    }

    /**
     * Global scope: por defecto los cargos deshabilitados quedan ocultos en TODO el
     * sistema (cobro, recibo, comprobante, recálculo de totales), igual que SoftDeletes.
     * La pantalla de correcciones hace opt-in con scopeConDeshabilitados().
     */
    protected static function booted(): void
    {
        static::addGlobalScope('habilitado', function ($builder) {
            $builder->whereNull((new static)->qualifyColumn('deshabilitado_en'));
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Anulación segura de cargos (fuente ÚNICA de "eliminaciones seguras")
    //
    //  Toda eliminación de un cargo —parcial o total, desde Correcciones, desde
    //  la cuenta del paciente o desde caja-gestión— pasa por aquí. Nunca se borra
    //  la línea: se reduce su cantidad y se registra un evento reversible en la
    //  bitácora cuenta_cobro_detalle_eliminados. Si la línea llega a 0 unidades se
    //  deshabilita (la oculta el global scope) pero la fila persiste para revertir.
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Anula `cantidad` unidades de este cargo (clamp a [0.01, cantidad actual]).
     * Devuelve el evento de anulación creado.
     *
     * @throws \RuntimeException si el cargo ya fue liquidado por un pago, o si la
     *         anulación tocaría dinero ya pagado/cubierto (excede el saldo pendiente).
     * @throws \InvalidArgumentException si la cantidad es <= 0.
     */
    public function anular($cantidad, string $motivo, int $userId): CuentaCobroDetalleEliminado
    {
        if ($this->liquidado_en !== null) {
            throw new \RuntimeException('No se puede anular un cargo ya liquidado por un pago.');
        }
        if (Money::cmp($cantidad, '0') <= 0) {
            throw new \InvalidArgumentException('La cantidad a anular debe ser mayor a 0.');
        }

        // No se puede anular más de lo que queda vivo en la línea.
        $cant = Money::cmp($cantidad, $this->cantidad) > 0
            ? (string) $this->cantidad
            : Money::format($cantidad);

        $subtotalAnulado = Money::mul($cant, $this->precio_unitario);

        // Invariante de caja: nunca anular dinero ya respaldado. En pago PARCIAL
        // ningún cargo queda "liquidado" (liquidado_en solo se sella al saldar del
        // todo), así que ese flag no protege los cargos ya pagados de una cuenta a
        // medio pagar. El máximo anulable = saldo pendiente = total − cobertura −
        // pagado (misma fórmula que recalcularTotales). Quitar un cargo por encima
        // de eso dejaría dinero pagado sin respaldo (requeriría una devolución).
        if ($cuenta = $this->cuentaCobro) {
            $cobertura = $cuenta->seguro_estado === 'autorizado'
                ? ($cuenta->seguro_monto_cobertura ?? '0')
                : '0';
            $comprometido = Money::add($cuenta->total_pagado, $cobertura);
            $anulable     = Money::sub($cuenta->total_calculado, $comprometido);

            if (Money::cmp($subtotalAnulado, $anulable) > 0) {
                throw new \RuntimeException(sprintf(
                    'No se puede anular Bs %s: la cuenta ya tiene Bs %s pagados/cubiertos por seguro. '
                    . 'Solo se puede anular hasta el saldo pendiente (Bs %s). Para quitar un cargo ya '
                    . 'pagado primero debe gestionarse la devolución.',
                    number_format((float) $subtotalAnulado, 2),
                    number_format((float) $comprometido, 2),
                    number_format(max(0, (float) $anulable), 2)
                ));
            }
        }

        return DB::transaction(function () use ($cant, $subtotalAnulado, $motivo, $userId) {
            // 1) Registrar el evento (snapshot + lo anulado en este movimiento).
            $evento = CuentaCobroDetalleEliminado::create([
                'cuenta_cobro_id'         => $this->cuenta_cobro_id,
                'cuenta_cobro_detalle_id' => $this->id,
                'tipo_item'               => $this->tipo_item,
                'descripcion'             => $this->descripcion,
                'cantidad'                => $cant,
                'precio_unitario'         => $this->precio_unitario,
                'subtotal'                => $subtotalAnulado,
                'origen_type'             => $this->origen_type,
                'origen_id'               => $this->origen_id,
                'area_origen'             => $this->area_origen,
                'observaciones'           => $this->observaciones,
                'usuario_eliminacion_id'  => $userId,
                'motivo_eliminacion'      => $motivo,
                'eliminado_en'            => now(),
            ]);

            // 2) Reducir la línea (el hook updating recalcula el subtotal).
            $nuevaCantidad = Money::sub($this->cantidad, $cant);
            $this->cantidad = $nuevaCantidad;

            // 3) Si quedó en 0, deshabilitar la línea (oculta por el global scope).
            if (Money::cmp($nuevaCantidad, '0') <= 0) {
                $this->deshabilitado_en       = now();
                $this->deshabilitado_por      = $userId;
                $this->motivo_deshabilitacion = $motivo;
            }
            $this->save();

            // 4) Recalcular totales/estado de la cuenta.
            if ($cuenta = $this->cuentaCobro) {
                $cuenta->load('detalles');
                $cuenta->recalcularTotales();
            }

            return $evento;
        });
    }

    /**
     * Revierte una anulación: devuelve sus unidades a la línea y la reactiva si
     * estaba totalmente anulada. Idempotente si ya fue revertida.
     *
     * @throws \InvalidArgumentException si el evento no pertenece a este cargo.
     * @throws \RuntimeException si el cargo ya fue liquidado por un pago.
     */
    public function revertirAnulacion(CuentaCobroDetalleEliminado $evento, int $userId): void
    {
        if ((int) $evento->cuenta_cobro_detalle_id !== (int) $this->id) {
            throw new \InvalidArgumentException('La anulación no pertenece a este cargo.');
        }
        if ($evento->estaRevertida()) {
            return;
        }
        if ($this->liquidado_en !== null) {
            throw new \RuntimeException('No se puede revertir: el cargo ya fue liquidado por un pago.');
        }

        DB::transaction(function () use ($evento, $userId) {
            // 1) Devolver las unidades a la línea.
            $this->cantidad = Money::add($this->cantidad, $evento->cantidad);

            // 2) Si estaba deshabilitada (anulación total), reactivarla.
            if ($this->deshabilitado_en !== null) {
                $this->deshabilitado_en       = null;
                $this->deshabilitado_por      = null;
                $this->motivo_deshabilitacion = null;
            }
            $this->save();

            // 3) Sellar la anulación como revertida.
            $evento->update([
                'revertido_en'  => now(),
                'revertido_por' => $userId,
            ]);

            // 4) Recalcular totales/estado de la cuenta.
            if ($cuenta = $this->cuentaCobro) {
                $cuenta->load('detalles');
                $cuenta->recalcularTotales();
            }
        });
    }

    // Estado de habilitación
    public function estaDeshabilitado(): bool
    {
        return $this->deshabilitado_en !== null;
    }

    /** Incluye también los cargos deshabilitados (quita el global scope). */
    public function scopeConDeshabilitados($query)
    {
        return $query->withoutGlobalScope('habilitado');
    }

    /** Sólo los cargos deshabilitados. */
    public function scopeDeshabilitados($query)
    {
        return $query->withoutGlobalScope('habilitado')->whereNotNull('deshabilitado_en');
    }

    // Estado de liquidación (un cargo se liquida cuando un pago salda la cuenta)
    public function estaLiquidado(): bool
    {
        return $this->liquidado_en !== null;
    }

    /** Cargos del ciclo pendiente: aún no liquidados por ningún pago. */
    public function scopePendientesLiquidacion($query)
    {
        return $query->whereNull('liquidado_en');
    }

    // Scopes
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_item', $tipo);
    }

    public function scopePorArea($query, $area)
    {
        return $query->where('area_origen', $area);
    }

    public function scopeServicios($query)
    {
        return $query->where('tipo_item', 'servicio');
    }

    public function scopeMedicamentos($query)
    {
        return $query->where('tipo_item', 'medicamento');
    }

    public function scopeProcedimientos($query)
    {
        return $query->where('tipo_item', 'procedimiento');
    }

    public function scopeEstadia($query)
    {
        return $query->where('tipo_item', 'estadia');
    }

    // Getters
    public function getTipoItemLabelAttribute(): string
    {
        return match($this->tipo_item) {
            'servicio' => 'Servicio',
            'medicamento' => 'Medicamento',
            'procedimiento' => 'Procedimiento',
            'estadia' => 'Estadía',
            'laboratorio' => 'Laboratorio',
            'imagenologia' => 'Imagenología',
            'farmacia' => 'Farmacia',
            'material' => 'Material/Insumo',
            'equipo_medico' => 'Equipo Médico',
            default => ucfirst($this->tipo_item),
        };
    }
}
