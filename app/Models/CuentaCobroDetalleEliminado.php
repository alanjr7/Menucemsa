<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaCobroDetalleEliminado extends Model
{
    use HasFactory;

    protected $table = 'cuenta_cobro_detalle_eliminados';

    protected $fillable = [
        'cuenta_cobro_id',
        'cuenta_cobro_detalle_id',
        'tipo_item',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'origen_type',
        'origen_id',
        'area_origen',
        'observaciones',
        'usuario_eliminacion_id',
        'motivo_eliminacion',
        'eliminado_en',
        'revertido_en',
        'revertido_por',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'eliminado_en' => 'datetime',
        'revertido_en' => 'datetime',
    ];

    public function cuentaCobro(): BelongsTo
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    /**
     * Línea viva que originó esta anulación (puede ser null en filas legacy).
     * Sin el global scope `habilitado`: una anulación total deja la línea
     * deshabilitada y son justamente esas las candidatas a revertir.
     */
    public function detalle(): BelongsTo
    {
        return $this->belongsTo(CuentaCobroDetalle::class, 'cuenta_cobro_detalle_id')
            ->withoutGlobalScope('habilitado');
    }

    public function usuarioEliminacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_eliminacion_id');
    }

    public function revertidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revertido_por');
    }

    /** Una anulación está vigente mientras no haya sido revertida. */
    public function estaRevertida(): bool
    {
        return $this->revertido_en !== null;
    }

    /** Anulaciones vigentes (no revertidas). */
    public function scopeVigentes($query)
    {
        return $query->whereNull('revertido_en');
    }

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
