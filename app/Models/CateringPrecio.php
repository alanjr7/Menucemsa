<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CateringPrecio extends Model
{
    use HasFactory;

    protected $table = 'catering_precios';

    protected $fillable = [
        'tipo_comida',
        'precio',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_comida', $tipo);
    }

    public static function getPrecio(string $tipo): float
    {
        $registro = self::porTipo($tipo)->first();

        if ($registro) {
            return (float) $registro->precio;
        }

        return (float) config("hospitalizacion.catering.precios.{$tipo}", 0);
    }

    public static function getPreciosArray(): array
    {
        $registros = self::all()->keyBy('tipo_comida');
        $tipos = ['desayuno', 'almuerzo', 'merienda', 'cena'];
        $resultado = [];

        foreach ($tipos as $tipo) {
            if (isset($registros[$tipo])) {
                $resultado[$tipo] = (float) $registros[$tipo]->precio;
            } else {
                $resultado[$tipo] = (float) config("hospitalizacion.catering.precios.{$tipo}", 0);
            }
        }

        return $resultado;
    }

    public static function actualizarPrecio(string $tipo, float $precio): self
    {
        return self::updateOrCreate(
            ['tipo_comida' => $tipo],
            ['precio' => $precio]
        );
    }
}
