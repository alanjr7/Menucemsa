<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'ci_paciente',
        'ci_medico',
        'codigo_especialidad',
        'fecha',
        'hora',
        'motivo',
        'estado',
        'observaciones',
        'confirmado',
        'fecha_confirmacion',
        'llamado',
        'fecha_llamada',
        'notas_llamada',
        'user_registro_id',
        'user_confirmacion_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime:H:i:s',
        'fecha_confirmacion' => 'datetime',
        'fecha_llamada' => 'datetime',
        'confirmado' => 'boolean',
        'llamado' => 'boolean',
    ];

    // Estados posibles
    const ESTADOS = [
        'programado' => 'Programado',
        'confirmado' => 'Confirmado',
        'en_atencion' => 'En Atención',
        'atendido' => 'Atendido',
        'cancelado' => 'Cancelado',
        'no_asistio' => 'No Asistió'
    ];

    // Relaciones
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'ci_medico', 'ci');
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'codigo_especialidad', 'codigo');
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_registro_id');
    }

    public function usuarioConfirmacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_confirmacion_id');
    }

    // Scopes para consultas comunes
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? Carbon::today();
        return $query->whereDate('fecha', $fecha);
    }

    public function scopePorConfirmar($query)
    {
        return $query->where('confirmado', false)
                    ->where('fecha', '>=', Carbon::today())
                    ->where('estado', '!=', 'cancelado');
    }

    public function scopeConfirmados($query)
    {
        return $query->where('confirmado', true);
    }

    public function scopeEnEspera($query)
    {
        return $query->where('estado', 'programado')
                    ->where('confirmado', true)
                    ->whereDate('fecha', Carbon::today());
    }

    public function scopeEnAtencion($query)
    {
        return $query->where('estado', 'en_atencion');
    }

    public function scopePendientesLlamada($query)
    {
        return $query->where('llamado', false)
                    ->where('fecha', '>=', Carbon::today())
                    ->where('fecha', '<=', Carbon::today()->addDays(2))
                    ->where('estado', '!=', 'cancelado');
    }

    // Métodos de utilidad
    public function getEstadoLabelAttribute()
    {
        return self::ESTADOS[$this->estado] ?? 'Desconocido';
    }

    public function getHoraFormateadaAttribute()
    {
        return Carbon::parse($this->hora)->format('H:i');
    }

    public function getFechaHoraFormateadaAttribute()
    {
        return Carbon::parse($this->fecha . ' ' . $this->hora)->format('d/m/Y H:i');
    }

    public function confirmar($usuarioId = null)
    {
        $this->confirmado = true;
        $this->fecha_confirmacion = now();
        $this->estado = 'confirmado';
        if ($usuarioId) {
            $this->id_usuario_confirmacion = $usuarioId;
        }
        return $this->save();
    }

    public function registrarLlamada($notas = '', $usuarioId = null)
    {
        $this->llamado = true;
        $this->fecha_llamada = now();
        $this->notas_llamada = $notas;
        if ($usuarioId) {
            $this->id_usuario_confirmacion = $usuarioId;
        }
        return $this->save();
    }

    public function iniciarAtencion()
    {
        $this->estado = 'en_atencion';
        return $this->save();
    }

    public function completarAtencion()
    {
        $this->estado = 'atendido';
        return $this->save();
    }

    public function cancelar($motivo = '')
    {
        $this->estado = 'cancelado';
        $this->observaciones = $motivo;
        return $this->save();
    }

    public function noAsistio()
    {
        $this->estado = 'no_asistio';
        return $this->save();
    }
}
