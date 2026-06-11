<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Emergency;
use App\Models\Evaluacion;
use App\Models\Triage;
use App\Models\Registro;
use App\Models\Seguro;
use App\Services\CuentaCobroService;
use App\Services\EpisodioService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmergencyIngresoController extends Controller
{
    public function registrarIngreso(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'usar_temp_id'    => 'nullable|boolean',
                'tipo_paciente'   => 'required|in:existente,nuevo',
                'ci'              => 'nullable|string',
                'nombres'         => 'nullable|string',
                'apellidos'       => 'nullable|string',
                'sexo'            => 'nullable|string',
                'telefono'        => 'nullable|string',
                'correo'          => 'nullable|email',
                'direccion'       => 'nullable|string',
                'tipo_ingreso'    => 'required|in:soat,parto,general',
                'destino_inicial' => 'required|in:cirugia,camilla,uti,parto,observacion,hospitalizacion,alta',
                'tipo_emergencia' => 'required|string',
                'descripcion'     => 'required|string',
                'seguro_id'       => 'nullable|integer|exists:seguros,id',
            ]);

            $usarTempId = $request->boolean('usar_temp_id');

            $paciente = $usarTempId
                ? $this->crearPacienteTemporal($request)
                : $this->crearOActualizarPaciente($request);

            $emergencyData = [
                'paciente_id'      => $paciente->id,
                'tipo_ingreso'     => $request->tipo_ingreso,
                'destino_inicial'  => $request->destino_inicial,
                'status'           => 'recibido',
                'ubicacion_actual' => 'emergencia',
                'symptoms'         => $request->descripcion,
                'vital_signs'      => null,
                'initial_assessment' => $request->descripcion,
                'observations'     => "Tipo: {$request->tipo_emergencia}",
                'admission_date'   => now(),
                'user_id'          => Auth::id(),
                'cost'             => 0,
                'paid'             => false,
                'es_parto'         => $request->tipo_ingreso === 'parto',
                'flujo_historial'  => [
                    [
                        'fecha'      => now()->toDateTimeString(),
                        'desde'      => 'recepcion',
                        'hasta'      => 'emergencia',
                        'usuario_id' => Auth::id(),
                        'notas'      => 'Ingreso registrado desde recepción',
                    ]
                ],
            ];

            $episodio = null;
            if (!$usarTempId) {
                $episodio = EpisodioService::abrirEpisodio($paciente->id, 'emergencia', Auth::id());
                $emergencyData['episodio_id'] = $episodio->id;
            }

            $emergency = Emergency::crearConCodigo($emergencyData);
            $emergencyCode = $emergency->code;

            $cuentaCobro = CuentaCobroService::crearCuentaEmergencia(
                $paciente->id,
                $emergency->id,
                [],
                true,
                $request->seguro_id
            );

            if ($episodio) {
                $cuentaCobro->update(['episodio_id' => $episodio->id]);
            }

            if (in_array($request->destino_inicial, ['cirugia', 'uti'])) {
                $this->preReservarRecurso($emergency, $request->destino_inicial);
            }

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'Paciente registrado en emergencia exitosamente',
                'emergency_code' => $emergencyCode,
                'emergency_id'   => $emergency->id,
                'redirect_url'   => route('reception.emergencia.comprobante', $emergency->id),
                'paciente'       => [
                    'id'     => $paciente->id,
                    'nombre' => $paciente->nombre,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al registrar ingreso a emergencia: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la emergencia. Intente nuevamente.',
            ], 500);
        }
    }

    private function crearPacienteTemporal(Request $request): Paciente
    {
        return Paciente::crearTemporal([
            'nombre' => trim($request->nombres . ' ' . $request->apellidos),
            'sexo'   => $request->sexo === 'Femenino' ? 'F' : 'M',
        ]);
    }

    private function crearOActualizarPaciente(Request $request): Paciente
    {
        $ci = $request->ci;

        $paciente = Paciente::where('ci', $ci)->first();

        if (!$paciente) {
            $request->validate([
                'nombres'   => 'required|string|max:80',
                'apellidos' => 'required|string|max:80',
                'sexo'      => 'required|string|in:Masculino,Femenino',
            ]);

            $sexoCodigo     = $request->sexo === 'Femenino' ? 'F' : 'M';
            $registroCodigo = Registro::generarCodigo([
                'fecha_nacimiento' => $request->fecha_nacimiento ?? null,
                'sexo'             => $sexoCodigo,
                'nombre'           => trim($request->nombres . ' ' . $request->apellidos),
            ]);

            Registro::create([
                'codigo'  => $registroCodigo,
                'fecha'   => now()->toDateString(),
                'hora'    => now()->toTimeString(),
                'motivo'  => 'Registro de Emergencia',
                'user_id' => Auth::id(),
            ]);

            $paciente = Paciente::create([
                'ci'              => (int) $ci,
                'nombre'          => trim($request->nombres . ' ' . $request->apellidos),
                'sexo'            => $sexoCodigo,
                'direccion'       => $request->direccion ?? 'Sin especificar',
                'telefono'        => $request->telefono ? (int)$request->telefono : 0,
                'correo'          => $request->correo ?? 'sin@email.com',
                'seguro_id'       => $this->obtenerOCrearSeguro('particular'),
                'registro_codigo' => $registroCodigo,
                'is_temp'         => false,
            ]);
        } else {
            $paciente->update([
                'telefono' => $request->telefono ?? $paciente->telefono,
                'correo'   => $request->correo ?? $paciente->correo,
                'direccion'=> $request->direccion ?? $paciente->direccion,
            ]);
        }

        return $paciente;
    }

    private function preReservarRecurso(Emergency $emergency, string $destino): void
    {
        if ($destino === 'cirugia') {
            $quirofano = DB::table('quirofanos')
                ->where('estado', 'disponible')
                ->orderBy('nro')
                ->first();

            if ($quirofano) {
                $emergency->update(['nro_cirugia' => 'PRE-' . $quirofano->nro]);
            }
        }
        // Nota: no hay pre-reserva de cama UTI: el rol UTI opera sobre tablas compartidas
        // (no existe una tabla de camas UTI dedicada).
    }

    private function obtenerOCrearSeguro(string $seguroNombre): int
    {
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => ucfirst($seguroNombre)],
            [
                'tipo'       => 'EMERGENCIA',
                'cobertura'  => 'Sin cobertura',
                'telefono'   => null,
                'formulario' => 'EMERGENCIA',
                'estado'     => 'activo',
            ]
        );

        return $seguro->id;
    }

    public function getEmergenciasActivas()
    {
        try {
            $emergencias = Emergency::with('paciente')
                ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])
                ->where('ubicacion_actual', 'emergencia')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $emergenciasFormateadas = $emergencias->map(function ($emergencia) {
                $paciente = $emergencia->paciente;
                return [
                    'codigo'   => $emergencia->code,
                    'paciente' => [
                        'nombre' => $paciente?->nombre ?? 'Desconocido',
                        'ci'     => $paciente?->ci ?? $paciente?->temp_code,
                    ],
                    'tipo_ingreso'        => $emergencia->tipo_ingreso,
                    'tipo_ingreso_label'  => $emergencia->tipo_ingreso_label,
                    'destino_inicial'     => $emergencia->destino_inicial,
                    'hora_ingreso'        => $emergencia->admission_date?->format('H:i') ?? $emergencia->created_at->format('H:i'),
                    'status'              => $emergencia->status,
                    'status_color'        => $emergencia->status_color,
                ];
            });

            return response()->json([
                'success'     => true,
                'emergencias' => $emergenciasFormateadas,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al cargar emergencias activas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar emergencias activas.',
            ], 500);
        }
    }

    public function mostrarFormularioCompletarDatos($emergencyId)
    {
        $emergency = Emergency::with('paciente')->findOrFail($emergencyId);

        if (!$emergency->paciente?->is_temp) {
            return redirect()->route('reception')
                ->with('error', 'Esta emergencia ya tiene datos completos del paciente');
        }

        $seguros = \App\Models\Seguro::where('estado', 'activo')
            ->orderBy('tipo')
            ->orderBy('nombre_empresa')
            ->get();

        return view('reception.completar-datos-paciente', compact('emergency', 'seguros'));
    }

    public function completarDatosPacienteTemporal(Request $request)
    {
        try {
            $validated = $request->validate([
                'emergency_id'    => 'required|exists:emergencies,id',
                'ci'              => 'required|integer',
                'nombres'         => 'nullable|string|max:80',
                'apellidos'       => 'nullable|string|max:80',
                'sexo'            => 'nullable|string|in:Masculino,Femenino',
                'fecha_nacimiento'=> 'nullable|date',
                'lugar_expedicion'=> 'nullable|string|max:10',
                'nacionalidad'    => 'nullable|string|max:50',
                'estado_civil'    => 'nullable|string|max:20',
                'telefono'        => 'nullable|string',
                'correo'          => 'nullable|email',
                'direccion'       => 'nullable|string',
                'profesion'       => 'nullable|string|max:100',
                'empresa_trabajo' => 'nullable|string|max:100',
                'seguro_id'       => 'nullable|integer|exists:seguros,id',
            ]);

            DB::beginTransaction();

            $emergency = Emergency::with('paciente')->findOrFail($validated['emergency_id']);
            $temp      = $emergency->paciente;

            if (!$temp || !$temp->is_temp) {
                DB::rollBack();
                $msg = 'Esta emergencia ya tiene datos completos del paciente';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return redirect()->route('reception')->with('error', $msg);
            }

            // ¿Ya existe un paciente real con este CI? -> fusionar en vez de promover.
            $existente = Paciente::where('ci', (int) $validated['ci'])
                ->where('id', '!=', $temp->id)
                ->first();

            if ($existente) {
                $paciente = $this->fusionarConPacienteExistente($emergency, $temp, $existente);
            } else {
                // Paciente nuevo: los datos de identidad son obligatorios para registrarlo.
                $request->validate([
                    'nombres'   => 'required|string|max:80',
                    'apellidos' => 'required|string|max:80',
                    'sexo'      => 'required|string|in:Masculino,Femenino',
                ]);

                $seguroId       = $validated['seguro_id'] ?? $this->obtenerOCrearSeguro('particular');
                $sexoCodigo     = $validated['sexo'] === 'Masculino' ? 'M' : 'F';
                $nombreCompleto = mb_strtoupper(trim($validated['nombres'] . ' ' . $validated['apellidos']), 'UTF-8');
                $registroCodigo = Registro::generarCodigo([
                    'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                    'sexo'             => $sexoCodigo,
                    'nombre'           => $nombreCompleto,
                ]);

                Registro::create([
                    'codigo'  => $registroCodigo,
                    'fecha'   => now()->toDateString(),
                    'hora'    => now()->toTimeString(),
                    'motivo'  => 'Registro completado desde paciente temporal de emergencia',
                    'user_id' => Auth::id(),
                ]);

                // Promote temp paciente in-place — all FK relations stay valid
                $temp->update([
                    'ci'               => (int) $validated['ci'],
                    'nombre'           => $nombreCompleto,
                    'sexo'             => $sexoCodigo,
                    'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                    'lugar_expedicion' => $validated['lugar_expedicion'] ?? null,
                    'nacionalidad'     => $validated['nacionalidad'] ?? null,
                    'estado_civil'     => $validated['estado_civil'] ?? null,
                    'direccion'        => $validated['direccion'] ?? 'Sin especificar',
                    'telefono'         => ($validated['telefono'] ?? null) ? (int)$validated['telefono'] : 0,
                    'correo'           => $validated['correo'] ?? 'sin@email.com',
                    'profesion'        => $validated['profesion'] ?? null,
                    'empresa_trabajo'  => $validated['empresa_trabajo'] ?? null,
                    'seguro_id'        => $seguroId,
                    'registro_codigo'  => $registroCodigo,
                    'triage_id'        => $this->obtenerOCrearTriage(),
                    'is_temp'          => false,
                    'temp_code'        => null,
                ]);

                // Open real episodio now that patient has a CI
                $episodio = EpisodioService::abrirEpisodio($temp->id, 'emergencia', Auth::id());
                $emergency->update(['episodio_id' => $episodio->id]);

                // Link evaluaciones and cuenta_cobros to the new episodio
                Evaluacion::where('paciente_id', $temp->id)
                    ->update(['episodio_id' => $episodio->id]);

                \App\Models\CuentaCobro::where('referencia_type', Emergency::class)
                    ->where('referencia_id', $emergency->id)
                    ->update(['episodio_id' => $episodio->id]);

                $paciente = $temp;
            }

            $emergency->registrarMovimiento(
                'emergencia',
                'emergencia',
                $existente
                    ? 'Emergencia vinculada a paciente existente. CI: ' . $paciente->ci
                    : 'Datos del paciente completados. CI asignado: ' . $paciente->ci
            );

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'      => true,
                    'message'      => 'Datos del paciente completados exitosamente',
                    'paciente'     => ['ci' => $paciente->ci, 'nombre' => $paciente->nombre],
                    'emergency_id' => $emergency->id,
                ]);
            }

            return redirect()->route('reception')
                ->with('success', 'Datos del paciente completados exitosamente. CI: ' . $paciente->ci);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . $e->getMessage(),
                    'errors'  => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al completar datos del paciente temporal: ' . $e->getMessage(), [
                'emergency_id' => $request->emergency_id ?? 'unknown',
                'trace'        => $e->getTraceAsString(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al completar los datos: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Error al completar los datos: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * El CI ingresado pertenece a un paciente ya registrado: en lugar de promover
     * el paciente temporal (lo que duplicaría la historia clínica), se re-apunta la
     * emergencia y todos sus registros hijos al paciente real y se elimina el temporal.
     *
     * Se re-apuntan explícitamente las tablas que (a) tienen FK restrictiva y
     * bloquearían el borrado (almacen_entregas_paciente, camilla_usos) o (b) tienen
     * FK en cascada y perderían datos clínicos al borrar el temporal (evaluaciones,
     * historial_medicos). El episodio se abre/reutiliza sobre el paciente existente.
     */
    private function fusionarConPacienteExistente(Emergency $emergency, Paciente $temp, Paciente $existente): Paciente
    {
        $episodio = EpisodioService::abrirEpisodio($existente->id, 'emergencia', Auth::id());

        $emergency->update([
            'paciente_id' => $existente->id,
            'episodio_id' => $episodio->id,
        ]);

        Evaluacion::where('paciente_id', $temp->id)
            ->update(['paciente_id' => $existente->id, 'episodio_id' => $episodio->id]);

        \App\Models\CuentaCobro::where('referencia_type', Emergency::class)
            ->where('referencia_id', $emergency->id)
            ->update(['paciente_id' => $existente->id, 'episodio_id' => $episodio->id]);

        DB::table('historial_medicos')
            ->where('paciente_id', $temp->id)
            ->update(['paciente_id' => $existente->id]);

        DB::table('almacen_entregas_paciente')
            ->where('paciente_id', $temp->id)
            ->update(['paciente_id' => $existente->id]);

        DB::table('camilla_usos')
            ->where('paciente_id', $temp->id)
            ->update(['paciente_id' => $existente->id]);

        // El paciente temporal queda huérfano y ya no debe existir.
        $temp->delete();

        return $existente;
    }

    private function obtenerOCrearTriage(): string
    {
        $triage = Triage::create([
            'id'          => 'TRIAGE-' . now()->format('YmdHis') . '-' . random_int(1000, 9999),
            'color'       => 'green',
            'descripcion' => 'Consulta Externa - No Urgente',
            'prioridad'   => 'baja',
            'user_id'     => Auth::id(),
        ]);

        return $triage->id;
    }

    public function comprobante($id)
    {
        $emergencia = Emergency::with(['paciente', 'user'])->findOrFail($id);
        $vitalSigns = json_decode($emergencia->vital_signs, true) ?? [];

        return view('reception.emergencia-comprobante', compact('emergencia', 'vitalSigns'));
    }
}
