<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;

    protected $table = 'registros';
    protected $primaryKey = 'codigo';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'codigo',
        'fecha',
        'hora',
        'motivo',
        'user_id',
    ];

    protected $casts = [
        'codigo' => 'string',
        'fecha' => 'date',
    ];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'registro_codigo', 'codigo');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Genera código en formato REG-{yy}-{mmdd}-{InicialP}{InicialM}{InicialN}
     * Misma lógica que Caja::generarCodigoPaciente() pero verifica unicidad en registros.
     *
     * $datos esperados: fecha_nacimiento, sexo, apellido_paterno, apellido_materno, nombres
     * O como fallback: nombre (nombre completo ya construido).
     */
    public static function generarCodigo(array $datos): string
    {
        $fechaNac = \Carbon\Carbon::parse($datos['fecha_nacimiento'] ?? now());
        $yy = $fechaNac->format('y');

        $mes = (int) $fechaNac->format('m');
        if (($datos['sexo'] ?? 'M') === 'F') {
            $mes += 50;
        }
        $mm = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $dd  = $fechaNac->format('d');

        $apellidoP = trim($datos['apellido_paterno'] ?? '');
        $apellidoM = trim($datos['apellido_materno'] ?? '');
        $nombres   = trim($datos['nombres'] ?? '');

        if ($apellidoP && $apellidoM && $nombres) {
            $iP = strtoupper(substr($apellidoP, 0, 1));
            $iM = strtoupper(substr($apellidoM, 0, 1));
            $iN = strtoupper(substr($nombres,   0, 1));
        } else {
            $partes = explode(' ', strtoupper(trim($datos['nombre'] ?? 'X')));
            $n = count($partes);
            if ($n >= 3) {
                $iN = substr($partes[0], 0, 1);
                $iP = substr($partes[$n - 2], 0, 1);
                $iM = substr($partes[$n - 1], 0, 1);
            } elseif ($n === 2) {
                $iN = substr($partes[0], 0, 1);
                $iP = substr($partes[1], 0, 1);
                $iM = 'X';
            } else {
                $iN = substr($partes[0] ?? 'X', 0, 1) ?: 'X';
                $iP = 'X';
                $iM = 'X';
            }
        }

        $base   = 'REG-' . $yy . '-' . $mm . $dd . '-' . $iP . $iM . $iN;
        $codigo = $base;
        $sufijo = 1;

        while (static::where('codigo', $codigo)->exists()) {
            $codigo = $base . '-' . $sufijo;
            $sufijo++;
        }

        return $codigo;
    }
}
