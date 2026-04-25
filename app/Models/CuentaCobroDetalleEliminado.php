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
        'tipo_item',
        'tarifa_id',
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
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'eliminado_en' => 'datetime',
    ];

    public function cuentaCobro(): BelongsTo
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    public function usuarioEliminacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_eliminacion_id');
    }

    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class);
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
