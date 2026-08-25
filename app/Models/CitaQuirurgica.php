<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Support\Money;

class CitaQuirurgica extends Model
{
    use HasFactory;

    protected $table = 'citas_quirurgicas';

    protected $fillable = [
        'paciente_id',
        'episodio_id',
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
        'tipo_anestesia',
        'descripcion_cirugia',
        'quirofano_id',
        'equipamiento_nombre',
        'equipamiento_precio',
        'equipamientos_detalle',
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
        'hora_inicio_real' => 'datetime',
        'hora_fin_real' => 'datetime',
        'timestamp_inicio' => 'datetime',
        'timestamp_fin' => 'datetime',
        'costo_base' => 'decimal:2',
        'costo_final' => 'decimal:2',
        'costo_minuto_extra' => 'decimal:2',
        'equipamiento_precio' => 'decimal:2',
        'equipamientos_detalle' => 'array',
    ];

    /**
     * Fuente única: al crear una cirugía, vincularla al episodio abierto del
     * paciente (si lo hay), igual que evaluaciones/emergencias/hospitalizaciones.
     * Respeta un episodio_id ya asignado explícitamente.
     */
    protected static function booted(): void
    {
        static::creating(function (CitaQuirurgica $cita) {
            if (empty($cita->episodio_id) && ! empty($cita->paciente_id)) {
                $cita->episodio_id = \App\Services\EpisodioService::getEpisodioAbierto($cita->paciente_id)?->id;
            }
        });
    }

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function episodio()
    {
        return $this->belongsTo(Episodio::class, 'episodio_id');
    }

    /**
     * Garantiza que la cirugía quede ligada a un episodio en el momento en que
     * realmente se realiza (ejecutar / iniciar). Si el paciente tiene un episodio
     * abierto, la vincula a ese; si no, abre uno nuevo (tipo_ingreso = 'cirugia').
     * No abre episodios al solo programar (eso lo decide el hook creating).
     */
    public function asegurarEpisodio(?int $userId = null): void
    {
        if (! empty($this->episodio_id) || empty($this->paciente_id)) {
            return;
        }

        $this->episodio_id = \App\Services\EpisodioService::abrirEpisodio(
            $this->paciente_id,
            'cirugia',
            $userId ?? auth()->id()
        )->id;
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

    /**
     * Cargos de la cuenta originados por ESTA cirugía (medicamentos, insumos,
     * equipos y el procedimiento quirúrgico). Fuente única de "qué se le dio /
     * usó en la cirugía": son las mismas filas de cuenta_cobro_detalles que
     * graba la ejecución (QuirofanoController::ejecutar) y de las que se deriva
     * costo_final. El global scope `habilitado` excluye los cargos anulados.
     */
    public function cargos(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(CuentaCobroDetalle::class, 'origen');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'user_registro_id');
    }

    // Métodos de negocio
    public function getDuracionEstimadaAttribute()
    {
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
        $this->asegurarEpisodio();
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

        $this->save();

        // El total se deriva de los cargos reales de la cuenta (fuente única).
        $this->recalcularCostoFinal();
    }

    private function determinarTipoFinal($duracionReal)
    {
        if ($duracionReal <= 45) return 'ambulatoria';
        if ($duracionReal <= 60) return 'menor';
        if ($duracionReal <= 90) return 'mediana';
        return 'mayor';
    }

    /**
     * Cobro de una cirugía por regla de 3 sobre la duración, con PISO en el costo base.
     * Fuente ÚNICA de la fórmula (antes duplicada en QuirofanoController y JS).
     *
     * cobro = max(costo_base, costo_base * duracion_real / duracion_base)
     * El cobro nunca baja del costo_base aunque la cirugía termine antes; el "extra"
     * es sólo el sobrecargo por excederse de la duración de referencia del tipo.
     *
     * @return array{base:string, extra:string, cirugia:string}
     */
    public static function calcularCobroCirugia($costoBase, $duracionReal, $duracionBase): array
    {
        $base  = Money::format($costoBase);
        $dBase = ((int) $duracionBase) > 0 ? (string) $duracionBase : (string) $duracionReal;

        $reglaTres = Money::div(Money::mul($base, (string) $duracionReal), $dBase);
        $cirugia   = Money::cmp($reglaTres, $base) > 0 ? Money::format($reglaTres) : $base;
        $extra     = Money::sub($cirugia, $base);

        return ['base' => $base, 'extra' => $extra, 'cirugia' => $cirugia];
    }

    /**
     * Fuente ÚNICA del total denormalizado de la cirugía.
     *
     * `costo_final` es un cache de la suma real de los cargos de ESTA cita en la
     * cuenta del paciente (procedimiento + medicamentos + insumos + equipos). El
     * global scope `habilitado` de CuentaCobroDetalle ya excluye los cargos
     * anulados, así que el cache coincide siempre con lo facturable.
     *
     * Reemplaza las fórmulas que estaban duplicadas y divergentes en el controlador
     * (ejecutar/actualizarDetalles) y aquí (la vieja calcularCostoFinal, que solo
     * sumaba cirugía + medicamentos y omitía insumos/equipos). Se mantiene al día
     * solo, vía el evento de dominio en CuentaCobroDetalle, ante cualquier cambio
     * de cargos (incluidos los caminos genéricos: anular cargos y ajustes de paciente).
     */
    public function recalcularCostoFinal(): string
    {
        if (empty($this->id)) {
            return Money::format($this->costo_final ?? '0');
        }

        $total = (string) CuentaCobroDetalle::where('origen_type', self::class)
            ->where('origen_id', (string) $this->id)
            ->sum('subtotal');

        $nuevo = Money::format($total);

        // Sólo persiste si cambió (evita writes/queries innecesarios en cascada).
        if (Money::cmp($nuevo, (string) ($this->costo_final ?? '0')) !== 0) {
            $this->costo_final = $nuevo;
            $this->saveQuietly();
        }

        return $nuevo;
    }

    public function validarDisponibilidadQuirofano()
    {
        try {
            // Helper to parse time strings (handles both H:i and H:i:s formats, and Carbon objects)
            $parseTime = function ($timeStr): Carbon {
                if ($timeStr instanceof \Carbon\Carbon) {
                    return $timeStr->copy();
                }
                $timeStr = (string) $timeStr;
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
