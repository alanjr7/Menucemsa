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
        'tarifa_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'origen_type',
        'origen_id',
        'observaciones',
        'area_origen',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function cuentaCobro(): BelongsTo
    {
        return $this->belongsTo(CuentaCobro::class);
    }

    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class);
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
