<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaCobroDetalle extends Model
{
    use HasFactory;

    protected $table = 'cuenta_cobro_detalles';
    
    protected $fillable = [
        'cuenta_cobro_id',
        'tipo_item',
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
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
            }
        });

        static::updating(function ($detalle) {
            if ($detalle->isDirty('cantidad') || $detalle->isDirty('precio_unitario')) {
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
            }
        });
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
