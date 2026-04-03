<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AlmacenMedicamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'area',
        'precio',
        'fecha_vencimiento',
        'lote',
        'cantidad',
        'stock_minimo',
        'unidad_medida',
        'tipo',
        'activo',
        'observaciones'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'cantidad' => 'integer',
        'stock_minimo' => 'integer',
        'activo' => 'boolean'
    ];

    // Scopes
    public function scopePorArea($query, $area)
    {
        return $query->where('area', $area);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeMedicamentos($query)
    {
        return $query->where('tipo', 'medicamento');
    }

    public function scopeInsumos($query)
    {
        return $query->where('tipo', 'insumo');
    }

    public function scopePorVencer($query, $dias = 30)
    {
        return $query->where('fecha_vencimiento', '<=', Carbon::now()->addDays($dias))
                    ->where('fecha_vencimiento', '>=', Carbon::now());
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha_vencimiento', '<', Carbon::now());
    }

    public function scopeBajoStock($query)
    {
        return $query->where('cantidad', '<=', \DB::raw('stock_minimo'));
    }

    // Accessors
    public function getAreaLabelAttribute()
    {
        $areas = [
            'emergencia' => 'Emergencia',
            'cirugia' => 'Cirugía',
            'hospitalizacion' => 'Hospitalización',
            'uti' => 'UTI',
            'usi' => 'USI',
            'neonato' => 'Neonato'
        ];
        
        return $areas[$this->area] ?? $this->area;
    }

    public function getTipoLabelAttribute()
    {
        return ucfirst($this->tipo);
    }

    public function getEstadoStockAttribute()
    {
        if ($this->cantidad == 0) {
            return 'agotado';
        } elseif ($this->cantidad <= $this->stock_minimo) {
            return 'bajo';
        } else {
            return 'normal';
        }
    }

    public function getEstadoVencimientoAttribute()
    {
        if (!$this->fecha_vencimiento) {
            return 'sin_fecha';
        }
        
        if ($this->fecha_vencimiento < Carbon::now()) {
            return 'vencido';
        } elseif ($this->fecha_vencimiento <= Carbon::now()->addDays(30)) {
            return 'por_vencer';
        } else {
            return 'vigente';
        }
    }

    public function getDiasParaVencerAttribute()
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }
        
        return Carbon::now()->diffInDays($this->fecha_vencimiento, false);
    }

    // Methods
    public function estaBajoStock()
    {
        return $this->cantidad <= $this->stock_minimo;
    }

    public function estaVencido()
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento < Carbon::now();
    }

    public function estaPorVencer($dias = 30)
    {
        return $this->fecha_vencimiento && 
               $this->fecha_vencimiento <= Carbon::now()->addDays($dias) &&
               $this->fecha_vencimiento >= Carbon::now();
    }
}
