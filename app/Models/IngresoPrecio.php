<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodigoCatalogo;
use App\Support\CodigoProducto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngresoPrecio extends Model
{
    use HasFactory;
    use GeneraCodigoCatalogo;

    /** Familia del código interno (ADMISIONES). */
    public const FAMILIA_CODIGO = CodigoProducto::FAMILIA_ADMISION;

    protected $fillable = [
        'codigo',
        'tipo_ingreso',
        'precio',
        'activo',
        'user_id',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    const TIPOS_INGRESO = [
        'consulta_externa' => 'Consulta Externa',
        'enfermeria' => 'Enfermería',
        'emergencia' => 'Emergencia',
        'internacion' => 'Internación',
    ];

    public static function getPrecio(string $tipoIngreso): ?float
    {
        $precio = self::where('tipo_ingreso', $tipoIngreso)
            ->where('activo', true)
            ->first();

        return $precio?->precio;
    }

    public function getTipoIngresoLabelAttribute(): string
    {
        return self::TIPOS_INGRESO[$this->tipo_ingreso] ?? $this->tipo_ingreso;
    }

    /**
     * Código interno familia 2 para un cargo de admisión cuya descripción sigue
     * el patrón canónico "Admisión de {etiqueta}" (ver TIPOS_INGRESO). Devuelve
     * null si la descripción no es una admisión conocida o si ese tipo aún no
     * tiene un precio activo configurado → el resolutor cae al diccionario
     * familia 9. Es el puente que hace que TODA admisión (creada en cualquiera
     * de los 5 sitios) tome su código catalogado sin tocar esos call sites.
     */
    public static function codigoPorDescripcion(string $descripcion): ?string
    {
        $desc    = trim($descripcion);
        $prefijo = 'Admisión de ';

        if (mb_stripos($desc, $prefijo) !== 0) {
            return null;
        }

        $etiqueta = mb_substr($desc, mb_strlen($prefijo));
        $tipo     = array_search($etiqueta, self::TIPOS_INGRESO, true);

        if ($tipo === false) {
            return null;
        }

        return self::where('tipo_ingreso', $tipo)->where('activo', true)->value('codigo');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
