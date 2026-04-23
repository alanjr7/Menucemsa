<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtiRecipeDetail extends Model
{
    use HasFactory;

    protected $table = 'uti_recipe_details';

    protected $fillable = [
        'uti_recipe_id',
        'medicamento_id',
        'dosis',
        'unidad',
        'frecuencia',
        'via_administracion',
        'indicaciones',
    ];

    protected $casts = [
        'dosis' => 'decimal:2',
    ];

    public function recipe()
    {
        return $this->belongsTo(UtiRecipe::class, 'uti_recipe_id');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamentos::class, 'medicamento_id');
    }
}
