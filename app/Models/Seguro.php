<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'cobertura',
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

    public function calcularCobertura(float $montoTotal): array
    {
        $montoCubierto = 0;
        $montoPaciente = $montoTotal;

        switch ($this->tipo_cobertura) {
            case 'porcentaje':
                $montoCubierto = $montoTotal * ($this->cobertura_porcentaje / 100);
                $montoPaciente = $montoTotal - $montoCubierto;
                break;

            case 'solo_consulta':
                $montoCubierto = $montoTotal;
                $montoPaciente = 0;
                break;

            case 'tope_monto':
                $montoCubierto = min($montoTotal, $this->tope_monto);
                $montoPaciente = $montoTotal - $montoCubierto;
                break;
        }

        return [
            'monto_total' => $montoTotal,
            'monto_cubierto' => round($montoCubierto, 2),
            'monto_paciente' => round($montoPaciente, 2),
        ];
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'seguro_id');
    }
}
