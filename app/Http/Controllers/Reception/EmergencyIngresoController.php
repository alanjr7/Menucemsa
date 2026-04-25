<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Emergency;
use App\Models\Triage;
use App\Models\Registro;
use App\Models\Seguro;
use App\Models\CuentaCobro;
use App\Services\CuentaCobroService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmergencyIngresoController extends Controller
{
    /**
     * Registrar ingreso a emergencia desde recepción
     */
    public function registrarIngreso(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validar datos
            $validated = $request->validate([
                'ci' => 'nullable|string',
                'temp_id' => 'nullable|string',
                'usar_temp_id' => 'nullable|boolean',
                'tipo_paciente' => 'required|in:existente,nuevo',
                'nombres' => 'nullable|string',
                'apellidos' => 'nullable|string',
                'sexo' => 'nullable|string',
                'telefono' => 'nullable|string',
                'correo' => 'nullable|email',
                'direccion' => 'nullable|string',
                'tipo_ingreso' => 'required|in:soat,parto,general',
                'destino_inicial' => 'required|in:cirugia,camilla,uti,parto,observacion,hospitalizacion,alta',
                'tipo_emergencia' => 'required|string',
                'descripcion' => 'required|string',
                'presion_arterial' => 'nullable|string',
                'frecuencia_cardiaca' => 'nullable|string',
                'frecuencia_respiratoria' => 'nullable|string',
                'temperatura' => 'nullable|string',
                'alergias' => 'nullable|string',
                'medicamentos' => 'nullable|string',
                'seguro_id' => 'nullable|integer|exists:seguros,id',
            ]);

            $usarTempId = $request->boolean('usar_temp_id');

            // 1. Crear o encontrar paciente
            if ($usarTempId) {
                $paciente = $this->crearPacienteTemporal($request);
            } else {
                $paciente = $this->crearOActualizarPaciente($request);
            }

            // 2. Generar código de emergencia
            $emergencyCode = Emergency::generateCode();

            // 3. Preparar datos de la emergencia
            $emergencyData = [
                'code' => $emergencyCode,
                'patient_id' => $paciente->ci ?? $paciente->id,
                'is_temp_id' => $usarTempId,
                'temp_id' => $usarTempId ? ($request->temp_id ?? 'TEMP-' . time()) : null,
                'tipo_ingreso' => $request->tipo_ingreso,
                'destino_inicial' => $request->destino_inicial,
                'status' => 'recibido',
                'ubicacion_actual' => 'emergencia',
                'symptoms' => $request->descripcion,
                'vital_signs' => json_encode([
                    'presion_arterial' => $request->presion_arterial,
                    'frecuencia_cardiaca' => $request->frecuencia_cardiaca,
                    'frecuencia_respiratoria' => $request->frecuencia_respiratoria,
                    'temperatura' => $request->temperatura,
                ]),
                'initial_assessment' => $request->descripcion,
                'observations' => "Tipo: {$request->tipo_emergencia}\nAlergias: " . ($request->alergias ?? 'Ninguna') . "\nMedicamentos: " . ($request->medicamentos ?? 'Ninguno'),
                'admission_date' => now(),
                'user_id' => Auth::id(),
                'cost' => 0,
                'paid' => false,
                'es_parto' => $request->tipo_ingreso === 'parto',
                'flujo_historial' => [
                    [
                        'fecha' => now()->toDateTimeString(),
                        'desde' => 'recepcion',
                        'hasta' => 'emergencia',
                        'usuario_id' => Auth::id(),
                        'notas' => 'Ingreso registrado desde recepción',
                    ]
                ],
            ];

            // 4. Crear emergencia
            $emergency = Emergency::create($emergencyData);

            // 5. Crear cuenta de cobro con seguro (pre-autorización)
            // Para pacientes temporales, usar el ID de emergencia como identificador numérico
            // ya que paciente_ci en BD es integer y temp_id es string
            $pacienteCi = $usarTempId
                ? (int) $emergency->id  // Usar ID de emergencia como identificador numérico
                : (int) $paciente->ci;

            $cuentaCobro = CuentaCobroService::crearCuentaEmergencia(
                $pacienteCi,
                $emergency->id,
                [], // Sin servicios predefinidos, se agregarán después
                true, // esPostPago = true
                $request->seguro_id // seguro_id seleccionado en el formulario
            );

            // 6. Procesar destino inicial si es necesario
            if (in_array($request->destino_inicial, ['cirugia', 'uti'])) {
                $this->preReservarRecurso($emergency, $request->destino_inicial);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paciente registrado en emergencia exitosamente',
                'emergency_code' => $emergencyCode,
                'emergency_id' => $emergency->id,
                'redirect_url' => route('reception.emergencia.comprobante', $emergency->id),
                'paciente' => [
                    'id' => $paciente->ci ?? $paciente->id,
                    'nombre' => $paciente->nombre ?? ($request->nombres . ' ' . $request->apellidos),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al registrar ingreso a emergencia: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la emergencia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear paciente temporal
     */
    private function crearPacienteTemporal(Request $request)
    {
        $tempId = $request->temp_id ?? 'TEMP-' . now()->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        // Para pacientes temporales, usamos el temp_id como identificador único
        // En una implementación real, podrías querer una tabla separada para pacientes temporales
        return (object)[
            'ci' => $tempId,
            'nombre' => $request->nombres . ' ' . $request->apellidos,
            'is_temp' => true,
        ];
    }

    /**
     * Crear o actualizar paciente
     */
    private function crearOActualizarPaciente(Request $request)
    {
        $ci = $request->ci;
        
        $paciente = Paciente::find($ci);
        
        if (!$paciente) {
            // Crear nuevo paciente
            $request->validate([
                'nombres' => 'required|string|max:80',
                'apellidos' => 'required|string|max:80',
                'sexo' => 'required|string|in:Masculino,Femenino',
            ]);

            $registroCodigo = 'REG-' . date('Y') . '-' . str_pad(Registro::count() + 1, 6, '0', STR_PAD_LEFT);
            
            $registro = Registro::create([
                'codigo' => $registroCodigo,
                'fecha' => now()->toDateString(),
                'hora' => now()->toTimeString(),
                'motivo' => 'Registro de Emergencia',
                'user_id' => Auth::id()
            ]);

            $seguroCodigo = $this->obtenerOCrearSeguro('particular');

            $paciente = Paciente::create([
                'ci' => $ci,
                'nombre' => trim($request->nombres . ' ' . $request->apellidos),
                'sexo' => $request->sexo === 'Femenino' ? 'F' : 'M',
                'direccion' => $request->direccion ?? 'Sin especificar',
                'telefono' => $request->telefono ? (int)$request->telefono : 0,
                'correo' => $request->correo ?? 'sin@email.com',
                'seguro_id' => $seguroCodigo,
                'registro_codigo' => $registroCodigo,
            ]);
        } else {
            // Actualizar datos si es necesario
            $paciente->update([
                'telefono' => $request->telefono ?? $paciente->telefono,
                'correo' => $request->correo ?? $paciente->correo,
                'direccion' => $request->direccion ?? $paciente->direccion,
            ]);
        }
        
        return $paciente;
    }

    /**
     * Pre-reservar recurso (quirófano o UTI)
     */
    private function preReservarRecurso(Emergency $emergency, string $destino): void
    {
        if ($destino === 'cirugia') {
            // Buscar quirófano disponible
            $quirofano = DB::table('quirofanos')
                ->where('estado', 'disponible')
                ->orderBy('nro')
                ->first();
            
            if ($quirofano) {
                $emergency->update([
                    'nro_cirugia' => 'PRE-' . $quirofano->nro,
                ]);
            }
        } elseif ($destino === 'uti') {
            // Buscar cama UTI disponible
            $cama = DB::table('camas_uti')
                ->where('estado', 'disponible')
                ->first();
            
            if ($cama) {
                $emergency->update([
                    'nro_uti' => 'PRE-UTI-' . $cama->id,
                ]);
            }
        }
    }

    /**
     * Obtener o crear seguro
     */
    private function obtenerOCrearSeguro($seguroNombre)
    {
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => ucfirst($seguroNombre)],
            [
                'tipo' => 'EMERGENCIA',
                'cobertura' => 'Sin cobertura',
                'telefono' => null,
                'formulario' => 'EMERGENCIA',
                'estado' => 'activo'
            ]
        );
        
        return $seguro->id;
    }

    /**
     * Obtener emergencias activas para mostrar en recepción
     */
    public function getEmergenciasActivas()
    {
        try {
            $emergencias = Emergency::whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])
                ->where('ubicacion_actual', 'emergencia')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $emergenciasFormateadas = $emergencias->map(function($emergencia) {
                $pacienteNombre = 'ID Temporal';
                if (!$emergencia->is_temp_id) {
                    $paciente = Paciente::find($emergencia->patient_id);
                    $pacienteNombre = $paciente?->nombre ?? 'Desconocido';
                }

                return [
                    'codigo' => $emergencia->code,
                    'paciente' => [
                        'nombre' => $pacienteNombre,
                        'ci' => $emergencia->patient_id
                    ],
                    'tipo_ingreso' => $emergencia->tipo_ingreso,
                    'tipo_ingreso_label' => $emergencia->tipo_ingreso_label,
                    'destino_inicial' => $emergencia->destino_inicial,
                    'hora_ingreso' => $emergencia->admission_date?->format('H:i') ?? $emergencia->created_at->format('H:i'),
                    'status' => $emergencia->status,
                    'status_color' => $emergencia->status_color,
                ];
            });

            return response()->json([
                'success' => true,
                'emergencias' => $emergenciasFormateadas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar emergencias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar formulario para completar datos de paciente temporal
     */
    public function mostrarFormularioCompletarDatos($emergencyId)
    {
        $emergency = Emergency::findOrFail($emergencyId);
        
        // Verificar que sea un paciente temporal
        if (!$emergency->is_temp_id) {
            return redirect()->route('reception.dashboard')
                ->with('error', 'Esta emergencia ya tiene datos completos del paciente');
        }
        
        return view('reception.completar-datos-paciente', compact('emergency'));
    }

    /**
     * Completar datos de paciente temporal
     */
    public function completarDatosPacienteTemporal(Request $request)
    {
        try {
            $validated = $request->validate([
                'emergency_id' => 'required|string|exists:emergencies,id',
                'ci' => 'required|string|unique:pacientes,ci',
                'nombres' => 'required|string|max:80',
                'apellidos' => 'required|string|max:80',
                'sexo' => 'required|string|in:Masculino,Femenino',
                'telefono' => 'nullable|string',
                'correo' => 'nullable|email',
                'direccion' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // 1. Buscar la emergencia
            $emergency = Emergency::findOrFail($validated['emergency_id']);

            // 2. Crear el paciente con datos completos
            $registroCodigo = 'REG-' . date('Y') . '-' . str_pad(Registro::count() + 1, 6, '0', STR_PAD_LEFT);
            
            $registro = Registro::create([
                'codigo' => $registroCodigo,
                'fecha' => now()->toDateString(),
                'hora' => now()->toTimeString(),
                'motivo' => 'Registro completado desde paciente temporal de emergencia',
                'user_id' => Auth::id()
            ]);

            $seguroCodigo = $this->obtenerOCrearSeguro('particular');

            // Mapear sexo a formato corto para BD (M/F)
            $sexoCodigo = $validated['sexo'] === 'Masculino' ? 'M' : 'F';

            $paciente = Paciente::create([
                'ci' => $validated['ci'],
                'nombre' => trim($validated['nombres'] . ' ' . $validated['apellidos']),
                'sexo' => $sexoCodigo,
                'direccion' => $validated['direccion'] ?? 'Sin especificar',
                'telefono' => $validated['telefono'] ? (int)$validated['telefono'] : 0,
                'correo' => $validated['correo'] ?? 'sin@email.com',
                'seguro_id' => $seguroCodigo,
                'registro_codigo' => $registroCodigo,
                'triage_id' => $this->obenerOCrearTriage(),
            ]);

            // 3. Actualizar la emergencia con el nuevo CI y marcar como no temporal
            $tempIdAnterior = $emergency->temp_id;
            $emergency->update([
                'patient_id' => $validated['ci'],
                'is_temp_id' => false,
                'temp_id' => null,
            ]);

            // 4. Actualizar la cuenta_cobros asociada con el nuevo CI real
            CuentaCobro::where('referencia_type', Emergency::class)
                ->where('referencia_id', $emergency->id)
                ->update([
                    'paciente_ci' => $validated['ci'],
                ]);

            // 5. Actualizar la hospitalización asociada (si existe) con el nuevo CI
            if ($emergency->nro_hospitalizacion) {
                \App\Models\Hospitalizacion::where('id', $emergency->nro_hospitalizacion)
                    ->whereNull('ci_paciente')
                    ->update([
                        'ci_paciente' => $validated['ci'],
                    ]);

                // 5b. Actualizar la cuenta de cobro asociada a la hospitalización
                CuentaCobro::where('referencia_type', \App\Models\Hospitalizacion::class)
                    ->where('referencia_id', $emergency->nro_hospitalizacion)
                    ->whereNull('paciente_ci')
                    ->update([
                        'paciente_ci' => $validated['ci'],
                    ]);
            }

            // 6. Registrar en el historial
            $emergency->registrarMovimiento(
                'emergencia',
                'emergencia',
                'Datos del paciente completados. CI asignado: ' . $validated['ci'] . ' (anterior: ' . $tempIdAnterior . ')'
            );

            DB::commit();

            // Si es petición AJAX, retornar JSON; si es web, redirigir
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Datos del paciente completados exitosamente',
                    'paciente' => [
                        'ci' => $paciente->ci,
                        'nombre' => $paciente->nombre,
                    ],
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
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al completar datos del paciente temporal: ' . $e->getMessage(), [
                'emergency_id' => $request->emergency_id ?? 'unknown',
                'trace' => $e->getTraceAsString(),
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

    private function obenerOCrearTriage()
    {
        $currentUser = Auth::user();
        
        $triage = Triage::firstOrCreate(
            ['id' => 'TRIAGE-CONSULTA'],
            [
                'color' => 'green',
                'descripcion' => 'Consulta Externa - No Urgente',
                'prioridad' => 'baja',
                'user_id' => $currentUser->id
            ]
        );
        
        return $triage->id;
    }

    /**
     * Mostrar comprobante de emergencia
     */
    public function comprobante($id)
    {
        $emergencia = Emergency::with(['paciente', 'user'])->findOrFail($id);
        
        // Decodificar signos vitales si existen
        $vitalSigns = json_decode($emergencia->vital_signs, true) ?? [];
        
        return view('reception.emergencia-comprobante', compact('emergencia', 'vitalSigns'));
    }
}
