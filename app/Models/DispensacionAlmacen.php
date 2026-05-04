<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispensacionAlmacen extends Model
{
    use HasFactory;

    protected $table = 'dispensaciones_almacen';

    protected $fillable = [
        'almacen_medicamento_id',
        'cantidad',
        'area_destino',
        'dispensado_por',
        'recibido_por',
        'observaciones',
        'fecha_dispensacion',
        'paciente_ci',
        'entregado_por',
        'fecha_entrega_paciente',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'paciente_ci' => 'integer',
        'fecha_dispensacion' => 'datetime',
        'fecha_entrega_paciente' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function medicamento()
    {
        return $this->belongsTo(AlmacenMedicamento::class, 'almacen_medicamento_id');
    }

    public function dispensadoPor()
    {
        return $this->belongsTo(User::class, 'dispensado_por');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_ci', 'ci');
    }

    public function entregadoPor()
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }

    public function scopePorArea($query, $area)
    {
        return $query->where('area_destino', $area);
    }

    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha_dispensacion', 'desc');
    }

    public function scopePorItem($query, $id)
    {
        return $query->where('almacen_medicamento_id', $id);
    }

    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_dispensacion', [$desde, $hasta]);
    }

    public function scopeUltimosDias($query, $dias = 30)
    {
        return $query->where('fecha_dispensacion', '>=', now()->subDays($dias));
    }

    public function getAreaDestinoLabelAttribute()
    {
        $areas = [
            'emergencia' => 'Emergencia',
            'cirugia' => 'Cirugía',
            'hospitalizacion' => 'Hospitalización',
            'uti' => 'UTI',
            'usi' => 'USI',
            'neonato' => 'Neonato',
            'internacion' => 'Internación'
        ];

        return $areas[$this->area_destino] ?? $this->area_destino;
    }
}
