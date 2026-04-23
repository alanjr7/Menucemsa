<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\UtiRecipeDetail;

class UtiRecipe extends Model
{
    use HasFactory;

    protected $table = 'uti_recipes';

    protected $fillable = [
        'uti_admission_id',
        'medico_id',
        'nro_receta',
        'fecha',
        'indicaciones_generales',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function admission()
    {
        return $this->belongsTo(UtiAdmission::class, 'uti_admission_id');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    public function details()
    {
        return $this->hasMany(UtiRecipeDetail::class, 'uti_recipe_id');
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'activa' => 'green',
            'suspendida' => 'red',
            'completada' => 'blue',
            default => 'gray',
        };
    }
}
