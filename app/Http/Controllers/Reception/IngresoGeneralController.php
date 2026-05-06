<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Hospitalizacion;
use App\Models\Emergency;
use App\Models\Consulta;
use App\Models\Triage;
use App\Models\Registro;
use App\Models\Seguro;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\Caja;
use App\Models\User;
use App\Services\CuentaCobroService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IngresoGeneralController extends Controller
{
    public function index()
    {
        $seguros = Seguro::where('estado', 'activo')
            ->orderBy('nombre_empresa')
            ->get();

        $especialidades = Especialidad::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('reception.ingreso-general', compact('seguros', 'especialidades'));
    }

    public function buscarEspecialidades(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 1) {
            return response()->json(['especialidades' => []]);
        }

        $especialidades = Especialidad::where('estado', 'activo')
            ->where('nombre', 'LIKE', "%{$q}%")
            ->orderBy('nombre')
            ->get();

        return response()->json(['especialidades' => $especialidades]);
    }

    public function crearEspecialidad(Request $request)
    {
        try {
            $request->validate(['nombre' => 'required|string|max:100']);

            $especialidad = Especialidad::create([
                'nombre' => $request->nombre,
                'descripcion' => 'Creada desde ingreso general',
                'estado' => 'activo',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Especialidad creada exitosamente',
                'especialidad' => $especialidad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function buscarMedicos(Request $request)
    {
        $q = $request->get('q', '');
        $especialidad = $request->get('especialidad');

        if (strlen($q) < 1) {
            return response()->json(['medicos' => []]);
        }

        $query = Medico::with(['user', 'especialidad'])
            ->where('estado', 'activo');

        if ($especialidad) {
            $query->where('codigo_especialidad', $especialidad);
        }

        $medicos = $query->get()
            ->filter(function ($medico) use ($q) {
                $nombre = $medico->user?->name ?? '';
                return stripos($nombre, $q) !== false || stripos((string)$medico->ci, $q) !== false;
            })
            ->values();

        $medicosFormateados = $medicos->map(function ($medico) {
            return [
                'ci' => $medico->ci,
                'nombre' => $medico->user?->name ?? "Médico {$medico->ci}",
                'especialidad' => $medico->especialidad?->nombre ?? 'Sin especialidad',
            ];
        })->toArray();

        return response()->json(['medicos' => $medicosFormateados]);
    }

    public function crearMedico(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'codigo_especialidad' => 'required|exists:especialidades,codigo',
            ]);

            // Generar CI temporal numérico para compatibilidad con columna integer
            // time() retorna ~1.8B, dentro del rango de INTEGER (2.1B max)
            $ciTemporal = (int) (time() + random_int(1000, 9999));

            \DB::beginTransaction();

            // Crear usuario para el médico
            $user = User::create([
                'name' => $request->nombre,
                'email' => 'medico.' . \Str::slug($request->nombre) . '.' . time() . '@hospital.local',
                'password' => \Hash::make('temporal123456'),
                'role' => 'doctor',
                'is_active' => true,
            ]);

            // Crear registro de médico con CI temporal
            $medico = Medico::create([
                'user_id' => $user->id,
                'ci' => $ciTemporal,
                'codigo_especialidad' => $request->codigo_especialidad,
                'estado' => 'activo',
                'telefono' => null,
            ]);

            // Recargar con relación para la respuesta
            $medico->load('especialidad');

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Médico creado exitosamente con ID temporal',
                'medico' => [
                    'ci' => $medico->ci,
                    'nombre' => $user->name,
                    'especialidad' => $medico->especialidad?->nombre ?? 'Sin especialidad',
                ]
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getEspecialidadesLista()
    {
        $especialidades = Especialidad::where('estado', 'activo')
            ->orderBy('nombre')
            ->get(['codigo', 'nombre']);

        return response()->json(['especialidades' => $especialidades]);
    }

    public function getMedicosPorEspecialidad($codigo)
    {
        $medicos = Medico::with(['user'])
            ->where('codigo_especialidad', $codigo)
            ->where('estado', 'activo')
            ->get()
            ->map(function ($medico) {
                return [
                    'ci' => $medico->ci,
                    'nombre' => $medico->user?->name ?? "Médico {$medico->ci}",
                ];
            });

        return response()->json(['medicos' => $medicos]);
    }

    public function buscarPaciente(Request $request)
    {
        $ci = $request->get('ci');

        if (empty($ci) || strlen($ci) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Ingrese al menos 3 caracteres para buscar'
            ]);
        }

        $paciente = Paciente::with(['seguro', 'triage', 'registro'])
                            ->where('ci', $ci)
                            ->first();

        if ($paciente) {
            return response()->json([
                'success' => true,
                'paciente' => $paciente,
                'message' => 'Paciente encontrado'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Paciente no encontrado. Debe registrar sus datos.',
            'show_form' => true
        ]);
    }

    public function buscarGarante(Request $request)
    {
        $ci = $request->get('ci');

        if (empty($ci)) {
            return response()->json(['success' => false, 'message' => 'Ingrese un CI']);
        }

        $garante = Paciente::where('ci', $ci)->first();

        if ($garante) {
            return response()->json([
                'success' => true,
                'garante' => $garante
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Garante no encontrado'
        ]);
    }

    public function procesarIngreso(Request $request)
    {
        $tipoIngreso = $request->input('tipo_ingreso');

        if (!in_array($tipoIngreso, ['consulta_externa', 'enfermeria', 'emergencia', 'internacion'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de ingreso no válido'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $usarTempId = $request->boolean('usar_temp_id');

            if ($tipoIngreso === 'emergencia' && $usarTempId) {
                $resultado = $this->procesarEmergenciaTemporal($request);
            } else {
                $paciente = $this->crearOActualizarPaciente($request);
                $resultado = match($tipoIngreso) {
                    'consulta_externa' => $this->procesarConsultaExterna($request, $paciente),
                    'enfermeria'       => $this->procesarEnfermeria($request, $paciente),
                    'emergencia'       => $this->procesarEmergencia($request, $paciente),
                    'internacion'      => $this->procesarInternacion($request, $paciente),
                };
            }

            DB::commit();

            return response()->json($resultado);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el ingreso: ' . $e->getMessage()
            ], 500);
        }
    }

    private function crearOActualizarPaciente(Request $request): Paciente
    {
        $usarTempId = $request->boolean('usar_temp_id');
        $ci = $usarTempId
            ? ($request->temp_id ?? 'TEMP-' . now()->format('Ymd') . '-' . random_int(100, 999))
            : $request->ci;

        $paciente = Paciente::find($ci);

        if (!$paciente) {
            $request->validate([
                'nombres' => 'required|string|max:80',
                'apellido_paterno' => 'required|string|max:80',
                'apellido_materno' => 'required|string|max:80',
                'sexo' => 'required|string|in:M,F,Masculino,Femenino',
            ]);

            $seguroId = $request->seguro_id ?: $this->obtenerSeguroParticular();

            // Construir nombre completo: Nombres + Apellido Paterno + Apellido Materno
            $nombreCompleto = trim(
                $request->nombres . ' ' .
                $request->apellido_paterno . ' ' .
                $request->apellido_materno
            );

            $paciente = Paciente::create([
                'ci' => $ci,
                'nombre' => $nombreCompleto,
                'sexo' => in_array($request->sexo, ['Femenino', 'F']) ? 'F' : 'M',
                'fecha_nacimiento' => $request->fecha_nacimiento ?? null,
                'lugar_expedicion' => $request->lugar_expedicion ?? null,
                'nacionalidad' => $request->nacionalidad ?? 'Boliviana',
                'estado_civil' => $request->estado_civil ?? null,
                'direccion' => $request->direccion ?? 'Sin especificar',
                'telefono' => $request->telefono ?? 0,
                'correo' => $request->correo ?? 'sin@email.com',
                'profesion' => $request->profesion ?? null,
                'empresa_trabajo' => $request->empresa_trabajo ?? null,
                'seguro_id' => $seguroId,
                'registro_codigo' => $this->crearRegistro('Registro desde Ingreso General'),
            ]);
        } else {
            $paciente->update([
                'fecha_nacimiento' => $request->fecha_nacimiento ?? $paciente->fecha_nacimiento,
                'lugar_expedicion' => $request->lugar_expedicion ?? $paciente->lugar_expedicion,
                'nacionalidad' => $request->nacionalidad ?? $paciente->nacionalidad,
                'estado_civil' => $request->estado_civil ?? $paciente->estado_civil,
                'direccion' => $request->direccion ?? $paciente->direccion,
                'telefono' => $request->telefono ?? $paciente->telefono,
                'correo' => $request->correo ?? $paciente->correo,
                'profesion' => $request->profesion ?? $paciente->profesion,
                'empresa_trabajo' => $request->empresa_trabajo ?? $paciente->empresa_trabajo,
            ]);
        }

        return $paciente;
    }

    private function procesarConsultaExterna(Request $request, Paciente $paciente): array
    {
        $registroExistente = Caja::where('tipo', 'CONSULTA_EXTERNA')
            ->whereDate('fecha', today())
            ->whereNull('nro_factura')
            ->whereHas('consulta', function($query) use ($paciente) {
                $query->where('ci_paciente', $paciente->ci);
            })
            ->first();

        if ($registroExistente) {
            return [
                'success' => false,
                'message' => 'Este paciente ya tiene una consulta registrada y pendiente de pago para hoy.',
                'redirect_url' => route('reception.confirmacion-registro', ['id' => $registroExistente->id])
            ];
        }

        $costoConsulta = $this->obtenerPrecioConsultaExterna();

        // Agregar campos separados al paciente para generación de código
        $paciente->nombres = $request->nombres;
        $paciente->apellido_paterno = $request->apellido_paterno;
        $paciente->apellido_materno = $request->apellido_materno;

        Caja::$patientContext = $paciente;

        $caja = Caja::create([
            'fecha' => now(),
            'total_dia' => $costoConsulta,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => null,
            'monto_pagado' => $costoConsulta,
        ]);

        Caja::$patientContext = null;

        // Obtener médico y especialidad seleccionados
        $medicoId = $request->medico_ci ? (int)$request->medico_ci : null;
        $especialidadCodigo = $request->especialidad_codigo ?: $this->obtenerEspecialidadPorDefecto();

        $consulta = Consulta::create([
            'codigo' => 'CONS-' . date('Y') . '-' . str_pad(Consulta::count() + 1, 6, '0', STR_PAD_LEFT),
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'motivo' => $request->motivo ?? 'Consulta general',
            'observaciones' => $request->observaciones ?? '',
            'codigo_especialidad' => $especialidadCodigo,
            'ci_paciente' => $paciente->ci,
            'ci_medico' => $medicoId,
            'estado_pago' => false,
            'caja_id' => $caja->id,
            'estado' => 'pendiente',
            'tipo' => 'consulta_externa',
        ]);

        $tieneSeguro = !empty($request->seguro_id);
        $cuenta = \App\Models\CuentaCobro::create([
            'paciente_ci' => $paciente->ci,
            'tipo_atencion' => 'consulta',
            'referencia_id' => $consulta->codigo,
            'referencia_type' => Consulta::class,
            'total_calculado' => $costoConsulta,
            'total_pagado' => 0,
            'estado' => 'pendiente',
            'seguro_estado' => $tieneSeguro ? 'pendiente_autorizacion' : null,
            'seguro_id' => $tieneSeguro ? $request->seguro_id : null,
        ]);

        \App\Models\CuentaCobroDetalle::create([
            'cuenta_cobro_id' => $cuenta->id,
            'tipo_item' => 'servicio',
            'descripcion' => 'Consulta Externa - ' . $consulta->codigo,
            'cantidad' => 1,
            'precio_unitario' => $costoConsulta,
            'subtotal' => $costoConsulta,
        ]);

        return [
            'success' => true,
            'message' => 'Consulta externa registrada exitosamente.',
            'tipo' => 'consulta_externa',
            'redirect_url' => route('reception.confirmacion-registro', ['id' => $caja->id])
        ];
    }

    private function procesarEmergenciaTemporal(Request $request): array
    {
        $tempId = $request->input('temp_id');
        if (empty($tempId)) {
            $tempId = 'TEMP-' . now()->format('Ymd') . '-' . random_int(100, 999);
        }

        $nroEmergencia = 'EMER-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        $triage = $this->crearTriage('rojo', 'Emergencia - Ingreso General (Temp)', 'alta');

        $emergencia = Emergency::create([
            'patient_id'         => $tempId,
            'user_id'            => Auth::id(),
            'code'               => $nroEmergencia,
            'status'             => 'recibido',
            'tipo_ingreso'       => 'general',
            'symptoms'           => $request->input('descripcion') ?? 'Ingreso por emergencia',
            'initial_assessment' => $request->input('tipo_emergencia') ?? 'Emergencia general',
            'is_temp_id'         => true,
            'temp_id'            => $tempId,
            'ubicacion_actual'   => 'emergencia',
        ]);

        CuentaCobroService::crearCuentaEmergencia(
            $tempId,
            (string) $emergencia->id,
            [],
            true,
            $request->filled('seguro_id') ? (int) $request->input('seguro_id') : null
        );

        NotificationService::notifyRole('emergencia', 'emergencia', 'Nueva Emergencia (ID Temporal)', "Paciente temporal {$tempId} registrado", route('emergency-staff.dashboard'), ['emergency_id' => $emergencia->id]);
        NotificationService::notifyRole('enfermera-emergencia', 'emergencia', 'Nueva Emergencia (ID Temporal)', "Paciente temporal {$tempId} registrado", route('emergency-staff.dashboard'), ['emergency_id' => $emergencia->id]);

        return [
            'success'        => true,
            'message'        => 'Emergencia registrada con ID temporal.',
            'tipo'           => 'emergencia',
            'emergency_code' => $nroEmergencia,
            'temp_id'        => $tempId,
            'redirect_url'   => route('reception.emergencia.comprobante', ['id' => $emergencia->id]),
        ];
    }

    private function procesarEmergencia(Request $request, Paciente $paciente): array
    {
        $triage = $this->crearTriage('rojo', 'Emergencia - Ingreso General', 'alta');
        $paciente->update(['triage_id' => $triage->id]);

        $nroEmergencia = 'EMER-' . now()->format('YmdHis') . '-' . random_int(100, 999);

        $emergencia = Emergency::create([
            'patient_id'         => $paciente->ci,
            'user_id'            => Auth::id(),
            'code'               => $nroEmergencia,
            'status'             => 'recibido',
            'tipo_ingreso'       => 'general',
            'symptoms'           => $request->descripcion ?? 'Ingreso por emergencia',
            'initial_assessment' => $request->tipo_emergencia ?? 'Emergencia general',
            'is_temp_id'         => false,
            'temp_id'            => null,
            'ubicacion_actual'   => 'emergencia',
        ]);

        $cuentaCobro = CuentaCobroService::crearCuentaEmergencia(
            (string) $paciente->ci,
            (string) $emergencia->id,
            [],
            true,
            $request->filled('seguro_id') ? (int) $request->input('seguro_id') : null
        );

        NotificationService::notifyRole('emergencia', 'emergencia', 'Nueva Emergencia', "Paciente {$paciente->nombre} registrado", route('emergency-staff.dashboard'), ['emergency_id' => $emergencia->id]);
        NotificationService::notifyRole('enfermera-emergencia', 'emergencia', 'Nueva Emergencia', "Paciente {$paciente->nombre} registrado", route('emergency-staff.dashboard'), ['emergency_id' => $emergencia->id]);

        return [
            'success'        => true,
            'message'        => 'Emergencia registrada exitosamente.',
            'tipo'           => 'emergencia',
            'emergency_code' => $nroEmergencia,
            'redirect_url'   => route('reception.emergencia.comprobante', ['id' => $emergencia->id])
        ];
    }

    private function procesarInternacion(Request $request, Paciente $paciente): array
    {
        $hospitalizacionActiva = Hospitalizacion::where('ci_paciente', $paciente->ci)
            ->whereNull('fecha_alta')
            ->first();

        if ($hospitalizacionActiva) {
            return [
                'success' => false,
                'message' => 'Este paciente ya tiene una hospitalización activa. Código: ' . $hospitalizacionActiva->id,
            ];
        }

        $garante = $this->procesarGarante($request);

        $triage = $this->crearTriage('amarillo', 'Hospitalización - Ingreso General', 'media');
        $paciente->update(['triage_id' => $triage->id]);

        $idHospitalizacion = 'HOSP-' . now()->format('YmdHis') . '-' . random_int(100, 999);

        $medicoId = $request->medico_ci ? (int)$request->medico_ci : null;

        $hospitalizacion = Hospitalizacion::create([
            'id' => $idHospitalizacion,
            'ci_paciente' => $paciente->ci,
            'motivo' => $request->motivo ?? 'Por determinar',
            'diagnostico' => $request->diagnostico ?? 'Por determinar',
            'ci_medico' => $medicoId,
            'fecha_ingreso' => now(),
            'estado' => 'activo',
        ]);

        $cuentaCobro = CuentaCobroService::obtenerOCrearCuentaMaestra(
            $paciente->ci,
            'internacion',
            $request->seguro_id
        );

        $precioAdmision = $this->obtenerPrecioInternacion();

        CuentaCobroService::agregarCargo(
            $cuentaCobro->id,
            'servicio',
            'Admisión de Internación',
            1,
            $precioAdmision
        );

        return [
            'success' => true,
            'message' => 'Hospitalización registrada exitosamente.',
            'tipo' => 'internacion',
            'hospitalizacion_id' => $idHospitalizacion,
            'redirect_url' => route('reception.hospitalizacion.comprobante', ['id' => $idHospitalizacion])
        ];
    }

    private function procesarGarante(Request $request): ?Paciente
    {
        if ($request->filled('garante_ci')) {
            $garante = Paciente::find($request->garante_ci);

            if ($garante) {
                // Actualizar garante existente con nuevos datos si se proporcionan
                $garante->update([
                    'fecha_nacimiento' => $request->garante_fecha_nacimiento ?? $garante->fecha_nacimiento,
                    'lugar_expedicion' => $request->garante_lugar_expedicion ?? $garante->lugar_expedicion,
                    'nacionalidad' => $request->garante_nacionalidad ?? $garante->nacionalidad,
                    'estado_civil' => $request->garante_estado_civil ?? $garante->estado_civil,
                    'telefono' => $request->garante_telefono ?? $garante->telefono,
                    'correo' => $request->garante_correo ?? $garante->correo,
                    'profesion' => $request->garante_profesion ?? $garante->profesion,
                    'empresa_trabajo' => $request->garante_empresa_trabajo ?? $garante->empresa_trabajo,
                    'direccion' => $request->garante_direccion ?? $garante->direccion,
                ]);
                return $garante;
            }

            if ($request->filled(['garante_nombres', 'garante_apellido_paterno', 'garante_apellido_materno'])) {
                // Construir nombre completo del garante: Nombres + Apellido Paterno + Apellido Materno
                $nombreCompletoGarante = trim(
                    $request->garante_nombres . ' ' .
                    $request->garante_apellido_paterno . ' ' .
                    $request->garante_apellido_materno
                );

                return Paciente::create([
                    'ci' => $request->garante_ci,
                    'nombre' => $nombreCompletoGarante,
                    'sexo' => in_array($request->garante_sexo, ['Femenino', 'F']) ? 'F' : ($request->garante_sexo ?? 'M'),
                    'fecha_nacimiento' => $request->garante_fecha_nacimiento ?? null,
                    'lugar_expedicion' => $request->garante_lugar_expedicion ?? null,
                    'nacionalidad' => $request->garante_nacionalidad ?? 'Boliviana',
                    'estado_civil' => $request->garante_estado_civil ?? null,
                    'direccion' => $request->garante_direccion ?? 'Sin especificar',
                    'telefono' => $request->garante_telefono ?? 0,
                    'correo' => $request->garante_correo ?? 'sin@email.com',
                    'profesion' => $request->garante_profesion ?? null,
                    'empresa_trabajo' => $request->garante_empresa_trabajo ?? null,
                    'seguro_id' => $this->obtenerSeguroParticular(),
                    'registro_codigo' => $this->crearRegistro('Registro de Garante'),
                ]);
            }
        }

        return null;
    }

    private function crearTriage(string $color, string $descripcion, string $prioridad): Triage
    {
        return Triage::create([
            'id' => 'TRIAGE-' . strtoupper($color) . '-' . now()->format('YmdHis') . '-' . random_int(100, 999),
            'color' => $color,
            'descripcion' => $descripcion,
            'prioridad' => $prioridad,
            'user_id' => Auth::id(),
        ]);
    }

    private function crearRegistro(string $motivo): string
    {
        $codigo = 'REG-' . date('Y') . '-' . str_pad(Registro::count() + 1, 6, '0', STR_PAD_LEFT);

        Registro::create([
            'codigo' => $codigo,
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'motivo' => $motivo,
            'user_id' => Auth::id(),
        ]);

        return $codigo;
    }

    private function obtenerSeguroParticular(): int
    {
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => 'Particular'],
            [
                'tipo' => 'Particular',
                'cobertura' => 'Sin cobertura',
                'telefono' => null,
                'formulario' => 'ESTANDAR',
                'estado' => 'activo'
            ]
        );

        return $seguro->id;
    }

    private function obtenerMedicoId($medicoSeleccionado): ?int
    {
        if (is_numeric($medicoSeleccionado)) {
            return (int) $medicoSeleccionado;
        }

        $medico = Medico::where('estado', 'activo')->first();
        return $medico?->ci;
    }

    private function procesarEnfermeria(Request $request, Paciente $paciente): array
    {
        $registroExistente = Caja::where('tipo', 'ENFERMERIA')
            ->whereDate('fecha', today())
            ->whereNull('nro_factura')
            ->whereHas('consulta', function($query) use ($paciente) {
                $query->where('ci_paciente', $paciente->ci);
            })
            ->first();

        if ($registroExistente) {
            return [
                'success' => false,
                'message' => 'Este paciente ya tiene un registro de enfermería pendiente de pago para hoy.',
                'redirect_url' => route('reception.confirmacion-registro', ['id' => $registroExistente->id])
            ];
        }

        $costoEnfermeria = $this->obtenerPrecioEnfermeria();

        $paciente->nombres = $request->nombres;
        $paciente->apellido_paterno = $request->apellido_paterno;
        $paciente->apellido_materno = $request->apellido_materno;

        Caja::$patientContext = $paciente;

        $caja = Caja::create([
            'fecha' => now(),
            'total_dia' => $costoEnfermeria,
            'tipo' => 'ENFERMERIA',
            'nro_factura' => null,
            'monto_pagado' => $costoEnfermeria,
        ]);

        Caja::$patientContext = null;

        $consulta = Consulta::create([
            'codigo' => 'ENF-' . date('Y') . '-' . str_pad(Consulta::count() + 1, 6, '0', STR_PAD_LEFT),
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'motivo' => $request->motivo ?? 'Atención de enfermería',
            'observaciones' => $request->observaciones ?? '',
            'codigo_especialidad' => $this->obtenerEspecialidadEnfermeria(),
            'ci_paciente' => $paciente->ci,
            'ci_medico' => null,
            'estado_pago' => false,
            'caja_id' => $caja->id,
            'estado' => 'pendiente',
            'tipo' => 'enfermeria',
        ]);

        $tieneSeguro = !empty($request->seguro_id);
        $cuenta = \App\Models\CuentaCobro::create([
            'paciente_ci' => $paciente->ci,
            'tipo_atencion' => 'enfermeria',
            'referencia_id' => $consulta->codigo,
            'referencia_type' => Consulta::class,
            'total_calculado' => $costoEnfermeria,
            'total_pagado' => 0,
            'estado' => 'pendiente',
            'seguro_estado' => $tieneSeguro ? 'pendiente_autorizacion' : null,
            'seguro_id' => $tieneSeguro ? $request->seguro_id : null,
        ]);

        \App\Models\CuentaCobroDetalle::create([
            'cuenta_cobro_id' => $cuenta->id,
            'tipo_item' => 'servicio',
            'descripcion' => 'Enfermería - ' . $consulta->codigo,
            'cantidad' => 1,
            'precio_unitario' => $costoEnfermeria,
            'subtotal' => $costoEnfermeria,
        ]);

        return [
            'success' => true,
            'message' => 'Ingreso a Enfermería registrado exitosamente.',
            'tipo' => 'enfermeria',
            'redirect_url' => route('reception.confirmacion-registro', ['id' => $caja->id])
        ];
    }

    private function obtenerPrecioEnfermeria(): float
    {
        $precio = \App\Models\IngresoPrecio::getPrecio('enfermeria');

        if ($precio !== null) {
            return (float) $precio;
        }

        $servicio = \App\Models\Servicio::getServicioPorTipo('ENFERMERIA');
        return $servicio ? (float) $servicio->precio : 30.00;
    }

    private function obtenerEspecialidadEnfermeria(): string
    {
        $especialidad = Especialidad::where('nombre', 'Enfermería')
            ->orWhere('nombre', 'like', '%Enfermer%')
            ->where('estado', 'activo')
            ->first();

        if ($especialidad) {
            return $especialidad->codigo;
        }

        $nuevaEspecialidad = Especialidad::firstOrCreate(
            ['codigo' => 'ENFERMERIA'],
            [
                'nombre' => 'Enfermería',
                'descripcion' => 'Atención de enfermería general',
                'estado' => 'activo',
            ]
        );

        return $nuevaEspecialidad->codigo;
    }

    private function obtenerPrecioConsultaExterna(): float
    {
        $precio = \App\Models\IngresoPrecio::getPrecio('consulta_externa');

        if ($precio !== null) {
            return (float) $precio;
        }

        $servicio = \App\Models\Servicio::getServicioPorTipo('CONSULTA_EXTERNA');
        return $servicio ? (float) $servicio->precio : 50.00;
    }

    private function obtenerPrecioInternacion(): float
    {
        $precio = \App\Models\IngresoPrecio::getPrecio('internacion');

        if ($precio !== null) {
            return (float) $precio;
        }

        return 150.00;
    }

    private function obtenerEspecialidadPorDefecto(): string
    {
        $especialidad = Especialidad::where('estado', 'activo')->first();

        if ($especialidad) {
            return $especialidad->codigo;
        }

        // Crear especialidad por defecto si no existe ninguna
        $nuevaEspecialidad = Especialidad::create([
            'codigo' => 'ESP-GENERAL',
            'nombre' => 'Medicina General',
            'descripcion' => 'Especialidad por defecto para consultas generales',
            'estado' => 'activo',
        ]);

        return $nuevaEspecialidad->codigo;
    }
}
