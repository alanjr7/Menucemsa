<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion',
        'tipo_documento',
        'numero_documento',
        'complemento',
    ];

    protected $casts = [
        'tipo_documento' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Accesor para formatear la fecha
    public function getFechaAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y') : 'N/A';
    }

    // Etiqueta legible del tipo de documento (catálogo SIN)
    public function getTipoDocumentoLabelAttribute(): string
    {
        return \App\Support\TipoDocumento::labelFor($this->tipo_documento);
    }
}
