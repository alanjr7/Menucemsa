<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtiBed extends Model
{
    use HasFactory;

    protected $table = 'uti_beds';

    protected $fillable = [
        'bed_number',
        'status',
        'tipo',
        'equipamiento',
        'precio_dia',
        'activa',
    ];

    protected $casts = [
        'precio_dia' => 'decimal:2',
        'activa' => 'boolean',
    ];

    public function admission()
    {
        return $this->hasOne(UtiAdmission::class, 'bed_id')->where('estado', 'activo');
    }

    public function admissions()
    {
        return $this->hasMany(UtiAdmission::class, 'bed_id');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'disponible' => 'green',
            'ocupada' => 'red',
            'mantenimiento' => 'yellow',
            'reservada' => 'blue',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'disponible' => 'Disponible',
            'ocupada' => 'Ocupada',
            'mantenimiento' => 'Mantenimiento',
            'reservada' => 'Reservada',
            default => 'Desconocido',
        };
    }
}
