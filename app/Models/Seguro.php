<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Money;

class Seguro extends Model
{
    use HasFactory;

    protected $table = 'seguros';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nombre_empresa',
        'tipo',
        'nit',
        'telefono',
        'formulario',
        'estado',
        'tipo_cobertura',
        'cobertura_porcentaje',
        'tope_monto',
        'copago_porcentaje',
    ];

    protected $casts = [
        'telefono' => 'string',
        'cobertura_porcentaje' => 'decimal:2',
        'tope_monto' => 'decimal:2',
        'copago_porcentaje' => 'decimal:2',
    ];

    protected $appends = [
        'descripcion_cobertura',
    ];

    public function getDescripcionCoberturaAttribute(): string
    {
        return match($this->tipo_cobertura) {
            'porcentaje' => "Autorizado con cobertura del {$this->cobertura_porcentaje}%. Copago paciente {$this->copago_porcentaje}%.",
            'solo_consulta' => 'Autorizado solo consulta, no cubre laboratorio.',
            'tope_monto' => "Autorizado con tope de Bs. " . number_format($this->tope_monto, 2),
            default => 'Cobertura no definida',
        };
    }

    /**
     * @param  float       $montoTotal      Monto a cubrir.
     * @param  float|null  $topeDisponible  Saldo del tope aún no consumido por el paciente
     *                                       en el período (para tope_monto). Si es null usa
     *                                       el tope completo (caso sin agregación / cálculo puro).
     */
    public function calcularCobertura(float $montoTotal, ?float $topeDisponible = null): array
    {
        $montoCubierto = '0';
        $montoPaciente = Money::format($montoTotal);

        switch ($this->tipo_cobertura) {
            case 'porcentaje':
                // (monto * porcentaje) / 100 — multiplicar antes de dividir
                // para no redondear la tasa y perder precisión.
                $montoCubierto = Money::div(Money::mul($montoTotal, $this->cobertura_porcentaje), 100);
                $montoPaciente = Money::sub($montoTotal, $montoCubierto);
                break;

            case 'solo_consulta':
                $montoCubierto = Money::format($montoTotal);
                $montoPaciente = '0';
                break;

            case 'tope_monto':
                // El tope es un límite AGREGADO del período: se cubre hasta el saldo del
                // tope que el paciente aún no consumió. Sin dato de consumo se usa el tope
                // completo. Nunca negativo.
                $disponible = $topeDisponible !== null ? max(0, $topeDisponible) : (float) $this->tope_monto;
                $montoCubierto = Money::min($montoTotal, $disponible);
                $montoPaciente = Money::sub($montoTotal, $montoCubierto);
                break;
        }

        return [
            'monto_total' => Money::format($montoTotal),
            'monto_cubierto' => Money::round($montoCubierto),
            'monto_paciente' => Money::round($montoPaciente),
        ];
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'seguro_id');
    }
}
