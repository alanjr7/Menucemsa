<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodigoCatalogo;
use App\Support\CodigoProducto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCirugia extends Model
{
    use HasFactory;
    use GeneraCodigoCatalogo;

    /** Familia del código interno (CIRUGÍAS). */
    public const FAMILIA_CODIGO = CodigoProducto::FAMILIA_CIRUGIA;

    protected $table = 'tipos_cirugia';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'duracion_minutos',
        'costo_base',
        'costo_minuto_extra',
        'activo',
    ];

    protected $casts = [
        'duracion_minutos' => 'integer',
        'costo_base' => 'decimal:2',
        'costo_minuto_extra' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function citasQuirurgicas()
    {
        return $this->hasMany(CitaQuirurgica::class, 'tipo_cirugia', 'nombre');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getDuracionFormateadaAttribute()
    {
        $horas = floor($this->duracion_minutos / 60);
        $minutos = $this->duracion_minutos % 60;
        
        if ($horas > 0) {
            return "{$horas}h {$minutos}min";
        }
        return "{$minutos}min";
    }
}
