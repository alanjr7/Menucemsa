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
use App\Services\EpisodioService;
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
                'nombre'      => $request->nombre,
                'descripcion' => 'Creada desde ingreso general',
                'estado'      => 'activo',
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'Especialidad creada exitosamente',
                'especialidad' => $especialidad,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al crear especialidad: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la especialidad. Intente nuevamente.',
            ], 400);
        }
    }

    public function buscarMedicos(Request $request)
    {
        $q            = $request->get('q', '');
        $especialidad = $request->get('especialidad');

        if (strlen($q) < 1) {
            return response()->json(['medicos' => []]);
        }

        $query = Medico::with(['user', 'especialidad'])->where('estado', 'activo');

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
                'ci'           => $medico->ci,
                'nombre'       => $medico->user?->name ?? "Médico {$medico->ci}",
                'especialidad' => $medico->especialidad?->nombre ?? 'Sin especialidad',
            ];
        })->toArray();

        return response()->json(['medicos' => $medicosFormateados]);
    }

    public function crearMedico(Request $request)
    {
        try {
            $request->validate([
                'nombre'              => 'required|string|max:255',
                'codigo_especialidad' => 'required|exists:especialidades,codigo',
            ]);

            $ciTemporal = (int) (time() + random_int(1000, 9999));

            \DB::beginTransaction();

            $user = User::create([
                'name'      => $request->nombre,
                'email'     => 'medico.' . \Str::slug($request->nombre) . '.' . time() . '@hospital.local',
                'password'  => \Hash::make(\Str::random(20)),
                'role'      => 'doctor',
                'is_active' => true,
            ]);

            $medico = Medico::create([
                'user_id'             => $user->id,
                'ci'                  => $ciTemporal,
                'codigo_especialidad' => $request->codigo_especialidad,
                'estado'              => 'activo',
                'telefono'            => null,
            ]);

            $medico->load('especialidad');

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Médico creado exitosamente con ID temporal',
                'medico'  => [
                    'ci'           => $medico->ci,
                    'nombre'       => $user->name,
                    'especialidad' => $medico->especialidad?->nombre ?? 'Sin especialidad',
                ],
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al crear médico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el médico. Intente nuevamente.',
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
                    'ci'     => $medico->ci,
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
                'message' => 'Ingrese al menos 3 caracteres para buscar',
            ]);
        }

        $paciente = Paciente::with(['seguro', 'triage', 'registro'])
            ->where('ci', $ci)
            ->first();

        if ($paciente) {
            return response()->json([
                'success'  => true,
                'paciente' => $paciente,
                'message'  => 'Paciente encontrado',
            ]);
        }

        return response()->json([
            'success'   => false,
            'message'   => 'Paciente no encontrado. Debe registrar sus datos.',
            'show_form' => true,
        ]);
    }

    public function buscarGarante(Request $request)
    {
        $ci = $request->get('ci');

        if (empty($ci) || strlen($ci) < 3) {
            return response()->json(['success' => false, 'message' => 'El CI debe tener al menos 3 caracteres']);
        }

        $garante = Paciente::where('ci', $ci)->first();

        if ($garante) {
            return response()->json(['success' => true, 'garante' => $garante]);
        }

        return response()->json(['success' => false, 'message' => 'Garante no encontrado']);
    }

    public function procesarIngreso(Request $request)
    {
        $tipoIngreso = $request->input('tipo_ingreso');

        if (!in_array($tipoIngreso, ['consulta_externa', 'enfermeria', 'emergencia', 'internacion'])) {
            return response()->json(['success' => false, 'message' => 'Tipo de ingreso no válido'], 400);
        }

        try {
            DB::beginTransaction();

            $usarTempId = $request->boolean('usar_temp_id');

            if ($tipoIngreso === 'emergencia' && $usarTempId) {
                $resultado = $this->procesarEmergenciaTemporal($request);
            } else {
                $paciente  = $this->crearOActualizarPaciente($request);
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
            DB::rollBack();
            \Log::error('Error al procesar ingreso: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el ingreso. Intente nuevamente.',
            ], 500);
        }
    }

    private function crearOActualizarPaciente(Request $request): Paciente
    {
        $ci       = $request->ci;
        $paciente = Paciente::where('ci', $ci)->first();

        if (!$paciente) {
            $request->validate([
                'nombres'           => 'required|string|max:80',
                'apellido_paterno'  => 'required|string|max:80',
                'apellido_materno'  => 'required|string|max:80',
                'sexo'              => 'required|string|in:M,F,Masculino,Femenino',
            ]);

            $seguroId = $request->seguro_id ?: $this->obtenerSeguroParticular();

            $nombreCompleto = trim(
                $request->nombres . ' ' .
                $request->apellido_paterno . ' ' .
                $request->apellido_materno
            );

            $paciente = Paciente::create([
                'ci'               => (int) $ci,
                'nombre'           => $nombreCompleto,
                'sexo'             => in_array($request->sexo, ['Femenino', 'F']) ? 'F' : 'M',
                'fecha_nacimiento' => $request->fecha_nacimiento ?? null,
                'lugar_expedicion' => $request->lugar_expedicion ?? null,
                'nacionalidad'     => $request->nacionalidad ?? 'Boliviana',
                'estado_civil'     => $request->estado_civil ?? null,
                'direccion'        => $request->direccion ?? 'Sin especificar',
                'telefono'         => $request->telefono ?? 0,
                'correo'           => $request->correo ?? 'sin@email.com',
                'profesion'        => $request->profesion ?? null,
                'empresa_trabajo'  => $request->empresa_trabajo ?? null,
                'seguro_id'        => $seguroId,
                'registro_codigo'  => $this->crearRegistro('Registro desde Ingreso General', [
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'sexo'             => in_array($request->sexo, ['Femenino', 'F']) ? 'F' : 'M',
                    'apellido_paterno' => $request->apellido_paterno,
                    'apellido_materno' => $request->apellido_materno,
                    'nombres'          => $request->nombres,
                ]),
                'is_temp' => false,
            ]);
        } else {
            $paciente->update([
                'fecha_nacimiento' => $request->fecha_nacimiento ?? $paciente->fecha_nacimiento,
                'lugar_expedicion' => $request->lugar_expedicion ?? $paciente->lugar_expedicion,
                'nacionalidad'     => $request->nacionalidad ?? $paciente->nacionalidad,
                'estado_civil'     => $request->estado_civil ?? $paciente->estado_civil,
                'direccion'        => $request->direccion ?? $paciente->direccion,
                'telefono'         => $request->telefono ?? $paciente->telefono,
                'correo'           => $request->correo ?? $paciente->correo,
                'profesion'        => $request->profesion ?? $paciente->profesion,
                'empresa_trabajo'  => $request->empresa_trabajo ?? $paciente->empresa_trabajo,
            ]);
        }

        return $paciente;
    }

    private function procesarConsultaExterna(Request $request, Paciente $paciente): array
    {
        $registroExistente = Caja::where('tipo', 'CONSULTA_EXTERNA')
            ->whereDate('fecha', today())
            ->whereNull('nro_factura')
            ->whereHas('consulta', fn($q) => $q->where('paciente_id', $paciente->id))
            ->first();

        if ($registroExistente) {
            return [
                'success'      => false,
                'message'      => 'Este paciente ya tiene una consulta registrada y pendiente de pago para hoy.',
                'redirect_url' => route('reception.confirmacion-registro', ['id' => $registroExistente->id]),
            ];
        }

        $costoConsulta = $this->obtenerPrecioConsultaExterna();

        $paciente->nombres           = $request->nombres;
        $paciente->apellido_paterno  = $request->apellido_paterno;
        $paciente->apellido_materno  = $request->apellido_materno;

        Caja::$patientContext = $paciente;
        try {
            $caja = Caja::create([
                'fecha'        => now(),
                'total_dia'    => $costoConsulta,
                'tipo'         => 'CONSULTA_EXTERNA',
                'nro_factura'  => null,
                'monto_pagado' => $costoConsulta,
            ]);
        } finally {
            Caja::$patientContext = null;
        }

        $medicoId           = $request->medico_ci ? (int)$request->medico_ci : null;
        $especialidadCodigo = $request->especialidad_codigo ?: $this->obtenerEspecialidadPorDefecto();

        $consulta = Consulta::create([
            'codigo'              => 'CONS-' . now()->format('YmdHis') . '-' . random_int(1000, 9999),
            'fecha'               => now()->toDateString(),
            'hora'                => now()->toTimeString(),
            'motivo'              => $request->motivo ?? 'Consulta general',
            'observaciones'       => $request->observaciones ?? '',
            'codigo_especialidad' => $especialidadCodigo,
            'paciente_id'         => $paciente->id,
            'ci_medico'           => $medicoId,
            'estado_pago'         => false,
            'caja_id'             => $caja->id,
            'estado'              => 'pendiente',
            'tipo'                => 'consulta_externa',
        ]);

        $seguroId = $request->filled('seguro_id') ? (int) $request->seguro_id : null;
        $cuenta   = CuentaCobroService::obtenerOCrearCuentaMaestra($paciente->id, 'consulta_externa', $seguroId);

        CuentaCobroService::agregarCargoConDeduplicacion(
            $cuenta->id,
            'servicio',
            'Consulta Externa - ' . $consulta->codigo,
            $costoConsulta,
            1,
            'consulta_externa',
            Consulta::class,
            $consulta->codigo
        );

        $episodio = EpisodioService::abrirEpisodio($paciente->id, 'consulta', Auth::id());
        $cuenta->update(['episodio_id' => $episodio->id]);

        return [
            'success'      => true,
            'message'      => 'Consulta externa registrada exitosamente.',
            'tipo'         => 'consulta_externa',
            'redirect_url' => route('reception.confirmacion-registro', ['id' => $caja->id]),
        ];
    }

    private function procesarEmergenciaTemporal(Request $request): array
    {
        $prefix   = 'TEMP-' . now()->format('Ymd');
        $last     = Paciente::where('temp_code', 'like', $prefix . '-%')->orderBy('temp_code', 'desc')->value('temp_code');
        $seq      = $last ? ((int) substr($last, -3)) + 1 : 1;
        $tempCode = $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);

        $paciente = Paciente::create([
            'nombre'    => trim($request->nombres . ' ' . $request->apellidos),
            'sexo'      => in_array($request->sexo, ['Femenino', 'F']) ? 'F' : 'M',
            'temp_code' => $tempCode,
            'is_temp'   => true,
        ]);

        $nroEmergencia = 'EMER-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        $triage        = $this->crearTriage('rojo', 'Emergencia - Ingreso General (Temp)', 'alta');

        $emergencia = Emergency::create([
            'paciente_id'        => $paciente->id,
            'user_id'            => Auth::id(),
            'code'               => $nroEmergencia,
            'status'             => 'recibido',
            'tipo_ingreso'       => 'general',
            'symptoms'           => $request->input('descripcion') ?? 'Ingreso por emergencia',
            'initial_assessment' => $request->input('tipo_emergencia') ?? 'Emergencia general',
            'ubicacion_actual'   => 'emergencia',
        ]);

        CuentaCobroService::crearCuentaEmergencia(
            $paciente->id,
            (string) $emergencia->id,
            [],
            true,
            $request->filled('seguro_id') ? (int) $request->input('seguro_id') : null
        );

        NotificationService::notifyRole('emergencia', 'emergencia', 'Nueva Emergencia (ID Temporal)', "Paciente temporal {$tempCode} registrado", route('emergency-staff.dashboard'), ['emergency_id' => $emergencia->id]);
        NotificationService::notifyRole('enfermera-emergencia', 'emergencia', 'Nueva Emergencia (ID Temporal)', "Paciente temporal {$tempCode} registrado", route('emergency-staff.dashboard'), ['emergency_id' => $emergencia->id]);

        return [
            'success'        => true,
            'message'        => 'Emergencia registrada con ID temporal.',
            'tipo'           => 'emergencia',
            'emergency_code' => $nroEmergencia,
            'temp_id'        => $tempCode,
            'redirect_url'   => route('reception.emergencia.comprobante', ['id' => $emergencia->id]),
        ];
    }

    private function procesarEmergencia(Request $request, Paciente $paciente): array
    {
        $triage = $this->crearTriage('rojo', 'Emergencia - Ingreso General', 'alta');
        $paciente->update(['triage_id' => $triage->id]);

        $nroEmergencia = 'EMER-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        $episodio      = EpisodioService::abrirEpisodio($paciente->id, 'emergencia', Auth::id());

        $emergencia = Emergency::create([
            'paciente_id'        => $paciente->id,
            'user_id'            => Auth::id(),
            'code'               => $nroEmergencia,
            'status'             => 'recibido',
            'tipo_ingreso'       => 'general',
            'symptoms'           => $request->descripcion ?? 'Ingreso por emergencia',
            'initial_assessment' => $request->tipo_emergencia ?? 'Emergencia general',
            'ubicacion_actual'   => 'emergencia',
            'episodio_id'        => $episodio->id,
        ]);

        $cuentaCobro = CuentaCobroService::crearCuentaEmergencia(
            $paciente->id,
            (string) $emergencia->id,
            [],
            true,
            $request->filled('seguro_id') ? (int) $request->input('seguro_id') : null
        );

        $cuentaCobro->update(['episodio_id' => $episodio->id]);

        NotificationService::notifyRole('emergencia', 'emergencia', 'Nueva Emergencia', "Paciente {$paciente->nombre} registrado", route('emergency-staff.dashboard'), ['emergency_id' => $emergencia->id]);
        NotificationService::notifyRole('enfermera-emergencia', 'emergencia', 'Nueva Emergencia', "Paciente {$paciente->nombre} registrado", route('emergency-staff.dashboard'), ['emergency_id' => $emergencia->id]);

        return [
            'success'        => true,
            'message'        => 'Emergencia registrada exitosamente.',
            'tipo'           => 'emergencia',
            'emergency_code' => $nroEmergencia,
            'redirect_url'   => route('reception.emergencia.comprobante', ['id' => $emergencia->id]),
        ];
    }

    private function procesarInternacion(Request $request, Paciente $paciente): array
    {
        $hospitalizacionActiva = Hospitalizacion::where('paciente_id', $paciente->id)
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

        $updateData = ['triage_id' => $triage->id];
        if ($garante) {
            $updateData['garante_id'] = $garante->id;
        }
        $paciente->update($updateData);

        $idHospitalizacion = 'HOSP-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        $medicoId          = $request->medico_ci ? (int)$request->medico_ci : null;
        $episodio          = EpisodioService::abrirEpisodio($paciente->id, 'internacion', Auth::id());

        $hospitalizacion = Hospitalizacion::create([
            'id'                => $idHospitalizacion,
            'paciente_id'       => $paciente->id,
            'motivo'            => $request->motivo ?? 'Por determinar',
            'diagnostico'       => $request->diagnostico ?? 'Por determinar',
            'ci_medico'         => $medicoId,
            'fecha_ingreso'     => now(),
            'estado'            => 'activo',
            'contacto_nombre'   => $garante ? $garante->nombre : null,
            'contacto_telefono' => $garante ? $garante->telefono : null,
            'episodio_id'       => $episodio->id,
        ]);

        $cuentaCobro = CuentaCobroService::obtenerOCrearCuentaMaestra(
            $paciente->id,
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

        $cuentaCobro->update(['episodio_id' => $episodio->id]);

        return [
            'success'           => true,
            'message'           => 'Hospitalización registrada exitosamente.',
            'tipo'              => 'internacion',
            'hospitalizacion_id'=> $idHospitalizacion,
            'redirect_url'      => route('reception.hospitalizacion.comprobante', ['id' => $idHospitalizacion]),
        ];
    }

    private function procesarGarante(Request $request): ?Paciente
    {
        if (!$request->filled('garante_ci')) {
            return null;
        }

        $garante = Paciente::where('ci', $request->garante_ci)->first();

        if ($garante) {
            $garante->update([
                'fecha_nacimiento' => $request->garante_fecha_nacimiento ?? $garante->fecha_nacimiento,
                'lugar_expedicion' => $request->garante_lugar_expedicion ?? $garante->lugar_expedicion,
                'nacionalidad'     => $request->garante_nacionalidad ?? $garante->nacionalidad,
                'estado_civil'     => $request->garante_estado_civil ?? $garante->estado_civil,
                'telefono'         => $request->garante_telefono ?? $garante->telefono,
                'correo'           => $request->garante_correo ?? $garante->correo,
                'profesion'        => $request->garante_profesion ?? $garante->profesion,
                'empresa_trabajo'  => $request->garante_empresa_trabajo ?? $garante->empresa_trabajo,
                'direccion'        => $request->garante_direccion ?? $garante->direccion,
            ]);
            return $garante;
        }

        if ($request->filled(['garante_nombres', 'garante_apellido_paterno', 'garante_apellido_materno'])) {
            $nombreCompletoGarante = trim(
                $request->garante_nombres . ' ' .
                $request->garante_apellido_paterno . ' ' .
                $request->garante_apellido_materno
            );

            return Paciente::create([
                'ci'               => (int) $request->garante_ci,
                'nombre'           => $nombreCompletoGarante,
                'sexo'             => in_array($request->garante_sexo, ['Femenino', 'F']) ? 'F' : ($request->garante_sexo ?? 'M'),
                'fecha_nacimiento' => $request->garante_fecha_nacimiento ?? null,
                'lugar_expedicion' => $request->garante_lugar_expedicion ?? null,
                'nacionalidad'     => $request->garante_nacionalidad ?? 'Boliviana',
                'estado_civil'     => $request->garante_estado_civil ?? null,
                'direccion'        => $request->garante_direccion ?? 'Sin especificar',
                'telefono'         => $request->garante_telefono ?? 0,
                'correo'           => $request->garante_correo ?? 'sin@email.com',
                'profesion'        => $request->garante_profesion ?? null,
                'empresa_trabajo'  => $request->garante_empresa_trabajo ?? null,
                'seguro_id'        => $this->obtenerSeguroParticular(),
                'registro_codigo'  => $this->crearRegistro('Registro de Garante', [
                    'fecha_nacimiento' => $request->garante_fecha_nacimiento,
                    'sexo'             => in_array($request->garante_sexo, ['Femenino', 'F']) ? 'F' : 'M',
                    'apellido_paterno' => $request->garante_apellido_paterno,
                    'apellido_materno' => $request->garante_apellido_materno,
                    'nombres'          => $request->garante_nombres,
                ]),
                'is_temp' => false,
            ]);
        }

        return null;
    }

    private function crearTriage(string $color, string $descripcion, string $prioridad): Triage
    {
        return Triage::create([
            'id'          => 'TRIAGE-' . strtoupper($color) . '-' . now()->format('YmdHis') . '-' . random_int(100, 999),
            'color'       => $color,
            'descripcion' => $descripcion,
            'prioridad'   => $prioridad,
            'user_id'     => Auth::id(),
        ]);
    }

    private function crearRegistro(string $motivo, array $datosPaciente = []): string
    {
        $codigo = Registro::generarCodigo($datosPaciente);

        Registro::create([
            'codigo'  => $codigo,
            'fecha'   => now()->toDateString(),
            'hora'    => now()->toTimeString(),
            'motivo'  => $motivo,
            'user_id' => Auth::id(),
        ]);

        return $codigo;
    }

    private function obtenerSeguroParticular(): int
    {
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => 'Particular'],
            [
                'tipo'       => 'Particular',
                'cobertura'  => 'Sin cobertura',
                'telefono'   => null,
                'formulario' => 'ESTANDAR',
                'estado'     => 'activo',
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
            ->whereHas('consulta', fn($q) => $q->where('paciente_id', $paciente->id))
            ->first();

        if ($registroExistente) {
            return [
                'success'      => false,
                'message'      => 'Este paciente ya tiene un registro de enfermería pendiente de pago para hoy.',
                'redirect_url' => route('reception.confirmacion-registro', ['id' => $registroExistente->id]),
            ];
        }

        $costoEnfermeria = $this->obtenerPrecioEnfermeria();

        $paciente->nombres          = $request->nombres;
        $paciente->apellido_paterno = $request->apellido_paterno;
        $paciente->apellido_materno = $request->apellido_materno;

        Caja::$patientContext = $paciente;
        try {
            $caja = Caja::create([
                'fecha'        => now(),
                'total_dia'    => $costoEnfermeria,
                'tipo'         => 'ENFERMERIA',
                'nro_factura'  => null,
                'monto_pagado' => $costoEnfermeria,
            ]);
        } finally {
            Caja::$patientContext = null;
        }

        $consulta = Consulta::create([
            'codigo'              => 'ENF-' . now()->format('YmdHis') . '-' . random_int(1000, 9999),
            'fecha'               => now()->toDateString(),
            'hora'                => now()->toTimeString(),
            'motivo'              => $request->motivo ?? 'Atención de enfermería',
            'observaciones'       => $request->observaciones ?? '',
            'codigo_especialidad' => $this->obtenerEspecialidadEnfermeria(),
            'paciente_id'         => $paciente->id,
            'ci_medico'           => null,
            'estado_pago'         => false,
            'caja_id'             => $caja->id,
            'estado'              => 'pendiente',
            'tipo'                => 'enfermeria',
        ]);

        $seguroId = $request->filled('seguro_id') ? (int) $request->seguro_id : null;
        $cuenta   = CuentaCobroService::obtenerOCrearCuentaMaestra($paciente->id, 'enfermeria', $seguroId);

        CuentaCobroService::agregarCargoConDeduplicacion(
            $cuenta->id,
            'servicio',
            'Enfermería - ' . $consulta->codigo,
            $costoEnfermeria,
            1,
            'enfermeria',
            Consulta::class,
            $consulta->codigo
        );

        $episodio = EpisodioService::abrirEpisodio($paciente->id, 'consulta', Auth::id());
        $cuenta->update(['episodio_id' => $episodio->id]);

        return [
            'success'      => true,
            'message'      => 'Ingreso a Enfermería registrado exitosamente.',
            'tipo'         => 'enfermeria',
            'redirect_url' => route('reception.confirmacion-registro', ['id' => $caja->id]),
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
            ['nombre' => 'Enfermería', 'descripcion' => 'Atención de enfermería general', 'estado' => 'activo']
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

        $nuevaEspecialidad = Especialidad::firstOrCreate(
            ['codigo' => 'ESP-GENERAL'],
            ['nombre' => 'Medicina General', 'descripcion' => 'Especialidad por defecto para consultas generales', 'estado' => 'activo']
        );

        return $nuevaEspecialidad->codigo;
    }
}
