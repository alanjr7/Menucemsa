<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CitaQuirurgica extends Model
{
    use HasFactory;

    protected $table = 'citas_quirurgicas';

    protected $fillable = [
        'ci_paciente',
        'fecha',
        'hora_inicio_estimada',
        'hora_inicio_real',
        'hora_fin_real',
        'ci_cirujano',
        'ci_instrumentista',
        'ci_anestesiologo',
        'nombre_instrumentista',
        'nombre_anestesiologo',
        'tipo_cirugia',
        'tipo_final',
        'descripcion_cirugia',
        'quirofano_id',
        'estado',
        'timestamp_inicio',
        'timestamp_fin',
        'costo_base',
        'costo_final',
        'costo_minuto_extra',
        'observaciones',
        'motivo_cancelacion',
        'user_registro_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio_estimada' => 'datetime',
        'timestamp_inicio' => 'datetime',
        'timestamp_fin' => 'datetime',
        'costo_base' => 'decimal:2',
        'costo_final' => 'decimal:2',
        'costo_minuto_extra' => 'decimal:2',
    ];

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
    }

    public function cirujano()
    {
        return $this->belongsTo(Medico::class, 'ci_cirujano', 'ci');
    }

    public function instrumentista()
    {
        return $this->belongsTo(Medico::class, 'ci_instrumentista', 'ci');
    }

    public function anestesiologo()
    {
        return $this->belongsTo(Medico::class, 'ci_anestesiologo', 'ci');
    }

    public function quirofano()
    {
        return $this->belongsTo(Quirofano::class, 'quirofano_id');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'user_registro_id');
    }

    // Métodos de negocio
    public function getDuracionEstimadaAttribute()
    {
        $tipos = [
            'menor' => 60,
            'mediana' => 90,
            'mayor' => 80,
            'ambulatoria' => 45,
        ];

        // First check hardcoded map, then fallback to DB
        if (isset($tipos[$this->tipo_cirugia])) {
            return $tipos[$this->tipo_cirugia];
        }

        $tipoCirugia = TipoCirugia::where('nombre', $this->tipo_cirugia)->first();
        return $tipoCirugia ? (int) $tipoCirugia->duracion_minutos : 60;
    }

    public function getDuracionRealAttribute()
    {
        if (!$this->timestamp_inicio || !$this->timestamp_fin) {
            return null;
        }
        
        return $this->timestamp_inicio->diffInMinutes($this->timestamp_fin);
    }

    public function getHoraFinEstimadaAttribute()
    {
        $hora = $this->hora_inicio_estimada;
        // Handle both string and Carbon formats
        if ($hora instanceof \Carbon\Carbon) {
            $inicio = $hora->copy();
        } else {
            $parts = explode(':', (string) $hora);
            $inicio = Carbon::createFromTime((int) $parts[0], (int) ($parts[1] ?? 0));
        }

        return $inicio->addMinutes($this->duracion_estimada);
    }

    public function iniciarCirugia()
    {
        $this->timestamp_inicio = now();
        $this->hora_inicio_real = now()->format('H:i:s');
        $this->estado = 'en_curso';
        $this->save();
    }

    public function finalizarCirugia()
    {
        $this->timestamp_fin = now();
        $this->hora_fin_real = now()->format('H:i:s');
        $this->estado = 'finalizada';
        
        // Calcular duración real y tipo final
        $duracionReal = $this->duracion_real;
        $this->tipo_final = $this->determinarTipoFinal($duracionReal);
        
        // Calcular costo final
        $this->calcularCostoFinal();
        
        $this->save();
    }

    private function determinarTipoFinal($duracionReal)
    {
        if ($duracionReal <= 45) return 'ambulatoria';
        if ($duracionReal <= 60) return 'menor';
        if ($duracionReal <= 90) return 'mediana';
        return 'mayor';
    }

    private function calcularCostoFinal()
    {
        $tipoCirugia = TipoCirugia::where('nombre', $this->tipo_final)->first();
        if (!$tipoCirugia) return;

        $duracionReal = $this->duracion_real;
        $duracionEstimada = $tipoCirugia->duracion_minutos;
        
        // Usar el costo base ingresado por el admin (no el del tipo de cirugía)
        $costoBase = $this->costo_base ?? 0;
        
        // Calcular costo extra por minutos adicionales
        $costoExtra = 0;
        if ($duracionReal > $duracionEstimada) {
            $minutosExtras = $duracionReal - $duracionEstimada;
            $costoExtra = $minutosExtras * $tipoCirugia->costo_minuto_extra;
        }
        
        // Buscar y sumar el costo de medicamentos usados
        $costoMedicamentos = 0;
        $cuentaCobro = \App\Models\CuentaCobro::where('referencia_type', self::class)
            ->where('referencia_id', $this->id)
            ->first();
        
        if ($cuentaCobro) {
            $costoMedicamentos = $cuentaCobro->detalles()
                ->where('tipo_item', 'medicamento')
                ->sum('subtotal');
        }
        
        // Calcular costo final: base + extra + medicamentos
        $this->costo_final = $costoBase + $costoExtra + $costoMedicamentos;
        $this->costo_minuto_extra = $tipoCirugia->costo_minuto_extra;
    }

    public function validarDisponibilidadQuirofano()
    {
        try {
            // Helper to parse time strings (handles both H:i and H:i:s formats)
            $parseTime = function ($timeStr): Carbon {
                $timeStr = (string) $timeStr;
                if ($timeStr instanceof \Carbon\Carbon) {
                    return $timeStr->copy();
                }
                $parts = explode(':', $timeStr);
                return Carbon::createFromTime((int) $parts[0], (int) ($parts[1] ?? 0));
            };

            // Si es una cita nueva, no tiene ID aún
            $query = self::where('quirofano_id', $this->quirofano_id)
                ->where('fecha', $this->fecha)
                ->where('estado', '!=', 'cancelada');

            // Si ya tiene ID (edición), excluir la cita actual
            if ($this->id) {
                $query->where('id', '!=', $this->id);
            }

            // Obtener todas las citas existentes para ese día y quirófano
            $citasExistentes = $query->get();

            // Calcular hora fin estimada de la nueva cita
            $horaInicio = $parseTime($this->hora_inicio_estimada);
            $horaFin = $horaInicio->copy()->addMinutes($this->duracion_estimada);

            // Verificar solapamiento con cada cita existente
            foreach ($citasExistentes as $cita) {
                $citaInicio = $parseTime($cita->hora_inicio_estimada);
                $citaFin = $citaInicio->copy()->addMinutes($cita->duracion_estimada);

                // Hay solapamiento si:
                // - La nueva cita empieza antes de que termine la existente
                // - Y la nueva cita termina después de que empieza la existente
                if ($horaInicio < $citaFin && $horaFin > $citaInicio) {
                    return true; // Hay conflicto
                }
            }

            return false; // No hay conflictos
        } catch (\Exception $e) {
            \Log::error('Error en validarDisponibilidadQuirofano: ' . $e->getMessage());
            return true; // En caso de error, asumir conflicto por seguridad
        }
    }

    public function getEstaEnCursoAttribute()
    {
        return $this->estado === 'en_curso';
    }

    public function getEstaFinalizadaAttribute()
    {
        return $this->estado === 'finalizada';
    }
}
