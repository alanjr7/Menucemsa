<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\Caja;
use App\Models\Seguro;
use App\Models\Triage;
use App\Models\Registro;
use App\Models\User;
use App\Models\Cita;
use App\Models\Hospitalizacion;
use App\Models\HistorialMedico;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Emergency;
use App\Models\Cirugia;
use Illuminate\View\View;

class ReceptionController extends Controller
{
    use \App\Traits\AuditLoggable;

    public function index()
    {
        return view('reception.reception');
    }

    // NUEVAS FUNCIONES PARA CONSULTA EXTERNA
    public function buscarPaciente(Request $request)
    {
        $ci = $request->get('ci');

        if (strlen($ci) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'El CI debe tener al menos 3 dígitos'
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
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado. Por favor registre sus datos.',
                'show_form' => true
            ]);
        }
    }

    public function registrarConsultaExterna(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Crear o encontrar paciente
            $paciente = $this->crearOActualizarPaciente($request);

            // 2. Verificar si ya existe un registro pendiente para este paciente hoy
            $registroExistente = Caja::where('tipo', 'CONSULTA_EXTERNA')
                ->whereDate('fecha', today())
                ->whereNull('nro_factura') // Pendiente de pago
                ->whereHas('consulta', function($query) use ($paciente) {
                    $query->where('ci_paciente', $paciente->ci);
                })
                ->first();

            if ($registroExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este paciente ya tiene una consulta registrada y pendiente de pago para hoy. Por favor, diríjase a caja para completar el pago.',
                    'redirect_url' => route('reception.confirmacion-registro', ['id' => $registroExistente->id])
                ], 422);
            }

            // 3. Crear registro de consulta en caja (estado pendiente)
            $caja = $this->crearRegistroCajaPendiente($request, $paciente);

            // 4. Crear consulta
            $consulta = $this->crearConsulta($request, $paciente, $caja);

            DB::commit();

            // Registrar en bitácora
            $this->logActivity(
                'registrar_consulta',
                'Recepción registró consulta externa para: ' . $paciente->nombre . ' (CI: ' . $paciente->ci . ')',
                $consulta
            );

            // Notificar a caja sobre nuevo pago pendiente
            NotificationService::notifyRole('caja', 'pago', 'Pago Pendiente', "Paciente {$paciente->nombre} - Consulta externa por cobrar", route('caja.operativa.index'), ['caja_id' => $caja->id]);

            return response()->json([
                'success' => true,
                'message' => 'Consulta externa registrada exitosamente. El paciente debe pasar a caja para realizar el pago.',
                'consulta' => $consulta->load(['paciente', 'medico.user', 'especialidad']),
                'redirect_url' => route('reception.confirmacion-registro', ['id' => $caja->id])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la consulta: ' . $e->getMessage()
            ], 500);
        }
    }

    private function crearOActualizarPaciente($request)
    {
        $ci = $request->ci;

        // Buscar paciente existente
        $paciente = Paciente::find($ci);

        if (!$paciente) {
            // Validar que todos los campos requeridos estén presentes
            $request->validate([
                'nombres' => 'required|string|max:80',
                'apellidos' => 'required|string|max:80',
                'sexo' => 'required|string|in:M,F',
                'fecha_nacimiento' => 'required|date',
            ]);

            // Crear nuevo paciente con todos los campos requeridos
            $nombreCompleto = trim($request->nombres . ' ' . $request->apellidos);
            $paciente = Paciente::create([
                'ci' => $ci,
                'nombre' => $nombreCompleto,
                'sexo' => $request->sexo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'lugar_expedicion' => $request->lugar_expedicion ?? null,
                'nacionalidad' => $request->nacionalidad ?? 'Boliviana',
                'estado_civil' => $request->estado_civil ?? null,
                'direccion' => $request->direccion ?? null,
                'telefono' => $request->telefono ?? null,
                'correo' => $request->correo ?? null,
                'profesion' => $request->profesion ?? null,
                'empresa_trabajo' => $request->empresa_trabajo ?? null,
                'seguro_id' => $request->seguro_id ?: null,
                'triage_id' => $this->obenerOCrearTriage(),
                'registro_codigo' => $this->obtenerOCrearRegistro(),
                'id_garante_referencia' => $request->id_garante_referencia ?? null,
            ]);
        } else {
            // Permitir múltiples consultas sin límite
            // $consultasHoy = Consulta::where('ci_paciente', $ci)
            //     ->whereDate('fecha', today())
            //     ->get();
            
            // if ($consultasHoy->count() >= 3) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Este paciente ya ha alcanzado el límite de consultas para hoy (máximo 3). Por favor, contacte al administrador.'
            //     ], 422);
            // }
            
            // Actualizar datos si es necesario
            $paciente->update([
                'telefono' => $request->telefono ?? $paciente->telefono,
                'correo' => $request->correo ?? $paciente->correo,
                'direccion' => $request->direccion ?? $paciente->direccion,
                'profesion' => $request->profesion ?? $paciente->profesion,
                'empresa_trabajo' => $request->empresa_trabajo ?? $paciente->empresa_trabajo,
                'id_garante_referencia' => $request->id_garante_referencia ?? $paciente->id_garante_referencia,
            ]);
        }
        
        return $paciente;
    }

    private function crearRegistroCajaPendiente($request, $paciente)
    {
        $servicio = \App\Models\Servicio::getServicioPorTipo('CONSULTA_EXTERNA');
        $costoConsulta = $servicio ? $servicio->precio : 50.00;

        Caja::$patientContext = $paciente;

        $caja = Caja::create([
            'fecha' => now(),
            'total_dia' => $costoConsulta,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => null,
            'caja_diaria_id' => null,
            'monto_pagado' => $costoConsulta,
        ]);

        Caja::$patientContext = null;

        return $caja;
    }

    private function crearConsulta($request, $paciente, $caja)
    {
        // Obtener médico basado en la selección
        $medicoId = $this->obtenerMedicoId($request->medico);
        
        return Consulta::create([
            'codigo' => 'CONS-' . date('Y') . '-' . str_pad(Consulta::count() + 1, 6, '0', STR_PAD_LEFT),
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'motivo' => $request->motivo,
            'observaciones' => $request->observaciones ?? '',
            'codigo_especialidad' => $this->obtenerEspecialidadCodigo($request->especialidad),
            'ci_paciente' => $paciente->ci,
            'ci_medico' => $medicoId,
            'estado_pago' => false,
            'caja_id' => $caja->id,
            'estado' => 'pendiente',
        ]);
    }

    private function obtenerOCrearSeguro($seguroNombre)
    {
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => ucfirst($seguroNombre)],
            [
                'tipo' => 'CONSULTA',
                'telefono' => null,
                'formulario' => 'ESTANDAR',
                'estado' => 'activo'
            ]
        );
        
        return $seguro->id;
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

    private function obtenerOCrearRegistro()
    {
        $currentUser = Auth::user();
        $codigo = 'REG-' . date('Y') . '-' . str_pad(Registro::count() + 1, 6, '0', STR_PAD_LEFT);
        
        $registro = Registro::firstOrCreate(
            ['codigo' => $codigo],
            [
                'fecha' => now()->toDateString(),
                'hora' => now()->toTimeString(),
                'motivo' => 'Registro de Consulta Externa',
                'user_id' => $currentUser->id
            ]
        );
        
        return $registro->codigo;
    }

    private function generarNumeroFactura()
    {
        // Obtener el último número de factura y generar el siguiente
        $ultimaFactura = Caja::max('nro_factura') ?? 0;
        return $ultimaFactura + 1;
    }

    private function obtenerMedicoId($medicoSeleccionado)
    {
        // Mapeo de selección del formulario a CI del médico
        $medicos = [
            'dr_ramirez' => 12345678, // CI de ejemplo
            'dra_torres' => 87654321, // CI de ejemplo
            'dr_silva' => 11223344,   // CI de ejemplo
        ];
        
        return $medicos[$medicoSeleccionado] ?? 12345678;
    }

    private function obtenerEspecialidadCodigo($especialidadNombre)
    {
        $especialidad = Especialidad::firstOrCreate(
            ['nombre' => ucfirst(str_replace('_', ' ', $especialidadNombre))],
            [
                'descripcion' => 'Especialidad de ' . str_replace('_', ' ', $especialidadNombre)
            ]
        );
        
        return $especialidad->codigo;
    }

    public function mostrarFormaPago($id)
    {
        $caja = Caja::with(['consulta.paciente', 'consulta.medico.user', 'consulta.especialidad'])
                     ->findOrFail($id);
        
        return view('reception.pago', compact('caja'));
    }

    public function procesarPago(Request $request, $id)
    {
        try {
            $caja = Caja::findOrFail($id);
            
            if ($caja->nro_factura) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pago ya ha sido procesado'
                ]);
            }
            
            // Asignar número de factura al procesar pago
            $caja->nro_factura = $this->generarNumeroFactura();
            $caja->save();
            
            // Actualizar estado de la consulta
            if ($caja->consulta) {
                $caja->consulta->estado_pago = true;
                $caja->consulta->save();
            }

            // Registrar en bitácora
            $pacienteNombre = $caja->consulta && $caja->consulta->paciente ? $caja->consulta->paciente->nombre : 'Paciente';
            $this->logActivity(
                'procesar_pago',
                'Recepción procesó pago de consulta para: ' . $pacienteNombre . ' - Factura #' . $caja->nro_factura,
                $caja
            );

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado exitosamente',
                'factura' => $caja->nro_factura,
                'redirect_url' => route('reception.confirmacion', ['id' => $caja->id])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmacionRegistro($id)
    {
        $caja = Caja::with(['consulta.paciente.seguro', 'consulta.paciente.triage', 'consulta.paciente.garante', 'consulta.medico.user', 'consulta.especialidad'])
                     ->findOrFail($id);

        return view('reception.confirmacion-registro', compact('caja'));
    }

    public function confirmacion($id)
    {
        $caja = Caja::with(['consulta.paciente', 'consulta.medico.user', 'consulta.especialidad'])
                     ->findOrFail($id);

        return view('reception.confirmacion', compact('caja'));
    }



    public function procesarTriageGeneral(Request $request)
    {
        $rules = [
            'ci' => 'required|string|min:3',
            'triage_tipo' => 'required|in:rojo,amarillo,verde',
        ];

        // Si es paciente nuevo, validar campos adicionales
        if ($request->tipo_paciente === 'nuevo') {
            $rules['nombres'] = 'required|string|max:80';
            $rules['apellidos'] = 'required|string|max:80';
            $rules['sexo'] = 'required|string|in:M,F';
            $rules['fecha_nacimiento'] = 'required|date';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $paciente = $this->crearOActualizarPaciente($request);
            $triage = $this->crearTriagePorTipo($request->triage_tipo);
            $paciente->update([
                'telefono' => $request->telefono ?? $paciente->telefono,
                'correo' => $request->correo ?? $paciente->correo,
                'triage_id' => $triage->id,
            ]);

            if ($request->triage_tipo === 'verde') {
                $caja = $this->crearRegistroCajaPendiente($request, $paciente);
                $consulta = $this->crearConsulta($request, $paciente, $caja);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'flujo' => 'consulta_normal',
                    'message' => 'Triage VERDE registrado. Continúe con caja y consulta externa.',
                    'redirect_url' => route('reception.confirmacion-registro', ['id' => $caja->id]),
                    'consulta_id' => $consulta->id,
                ]);
            }

            if ($request->triage_tipo === 'rojo') {
                $nroEmergencia = 'EMER-' . now()->format('YmdHis') . '-' . random_int(100, 999);
                DB::table('emergencias')->insert([
                    'nro' => $nroEmergencia,
                    'descripcion' => $request->motivo ?? 'Emergencia por triage rojo',
                    'estado' => 'INGRESADO',
                    'tipo' => strtoupper($request->input('tipo_emergencia', 'EMERGENCIA')),
                    'id_triage' => $triage->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $acciones = ['enviado_emergencia' => true];

                if ($request->boolean('accidente_automovilistico')) {
                    $this->obtenerOCrearRegistroMotivo('Formulario SOAT - ' . $paciente->ci);
                    $acciones['formulario_soat'] = true;
                }

                if ($request->boolean('requiere_cirugia_inmediata')) {
                    $acciones['traslado_uci'] = true;
                    $quirofano = DB::table('quirofanos')->orderBy('nro')->first();
                    if ($quirofano) {
                        $nroCirugia = 'CIR-' . now()->format('YmdHis') . '-' . random_int(100, 999);
                        DB::table('cirugias')->insert([
                            'nro' => $nroCirugia,
                            'fecha' => now()->toDateString(),
                            'hora' => now()->toTimeString(),
                            'tipo' => 'CIRUGIA_INMEDIATA',
                            'descripcion' => $request->motivo ?? 'Cirugía inmediata por triage rojo',
                            'nro_emergencia' => $nroEmergencia,
                            'nro_quirofano' => $quirofano->nro,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $acciones['cirugia'] = true;
                    }
                } else {
                    $idHosp = 'HOSP-' . now()->format('YmdHis') . '-' . random_int(100, 999);
                    DB::table('hospitalizaciones')->insert([
                        'id' => $idHosp,
                        'fecha_ingreso' => now(),
                        'motivo' => 'Observación posterior a triage rojo',
                        'nro_emergencia' => $nroEmergencia,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $acciones['internacion_uti'] = true;
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'flujo' => 'emergencia',
                    'message' => 'Triage ROJO procesado y derivado a emergencia.',
                    'acciones' => $acciones,
                ]);
            }

            // AMARILLO - Parto
            $idHosp = 'HOSP-' . now()->format('YmdHis') . '-' . random_int(100, 999);
            $nroEmergencia = 'EMER-' . now()->format('YmdHis') . '-' . random_int(100, 999);
            DB::table('emergencias')->insert([
                'nro' => $nroEmergencia,
                'descripcion' => 'Ingreso para parto',
                'estado' => 'INGRESADO',
                'tipo' => 'PARTO',
                'id_triage' => $triage->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('hospitalizaciones')->insert([
                'id' => $idHosp,
                'fecha_ingreso' => now(),
                'motivo' => $request->boolean('requiere_estabilizacion_previa') ? 'Estabilización previa a parto' : 'Ingreso a parto',
                'nro_emergencia' => $nroEmergencia,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $quirofano = DB::table('quirofanos')->orderBy('nro')->first();
            $acciones = [
                'registro_paciente' => true,
                'parto_quirofano_exclusivo' => true,
                'asignacion_neonatologia' => true,
            ];

            if ($quirofano) {
                $nroCirugia = 'CIR-' . now()->format('YmdHis') . '-' . random_int(100, 999);
                DB::table('cirugias')->insert([
                    'nro' => $nroCirugia,
                    'fecha' => now()->toDateString(),
                    'hora' => now()->toTimeString(),
                    'tipo' => 'PARTO',
                    'descripcion' => 'Parto en quirófano exclusivo',
                    'nro_emergencia' => $nroEmergencia,
                    'nro_quirofano' => $quirofano->nro,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('partos')->insert([
                    'nro' => 'PARTO-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                    'tipo' => 'INSTITUCIONAL',
                    'observaciones' => $request->observaciones,
                    'id_hospitalizacion' => $idHosp,
                    'nro_cirugia' => $nroCirugia,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($request->boolean('requiere_observacion_postparto')) {
                $acciones['observacion_postparto_uti'] = true;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'flujo' => 'parto',
                'message' => 'Triage AMARILLO (parto) procesado correctamente.',
                'acciones' => $acciones,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar triage: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function crearTriagePorTipo(string $tipo)
    {
        $currentUser = Auth::user();

        $map = [
            'rojo' => ['color' => 'red', 'descripcion' => 'Emergencia', 'prioridad' => 'alta'],
            'amarillo' => ['color' => 'yellow', 'descripcion' => 'Parto', 'prioridad' => 'media'],
            'verde' => ['color' => 'green', 'descripcion' => 'Consulta Externa - No Urgente', 'prioridad' => 'baja'],
        ];

        $cfg = $map[$tipo];
        return Triage::create([
            'id' => 'TRIAGE-' . strtoupper($tipo) . '-' . now()->format('YmdHis') . '-' . random_int(100, 999),
            'color' => $cfg['color'],
            'descripcion' => $cfg['descripcion'],
            'prioridad' => $cfg['prioridad'],
            'user_id' => $currentUser->id,
        ]);
    }

    private function obtenerOCrearRegistroMotivo(string $motivo)
    {
        return Registro::firstOrCreate(
            ['codigo' => 'REG-' . now()->format('YmdHis') . '-' . random_int(100, 999)],
            [
                'fecha' => now()->toDateString(),
                'hora' => now()->toTimeString(),
                'motivo' => $motivo,
                'user_id' => Auth::id(),
            ]
        );
    }

    // MÉTODOS PARA GESTIÓN DE CITAS Y AGENDA

    public function getAgendaDia(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::today()->toDateString());
        
        $citas = Cita::delDia($fecha)
            ->with(['paciente', 'medico.user', 'especialidad'])
            ->orderBy('hora')
            ->get();

        // Estadísticas del día
        $stats = [
            'total' => $citas->count(),
            'confirmados' => $citas->where('confirmado', true)->count(),
            'en_espera' => $citas->where('estado', 'programado')->where('confirmado', true)->count(),
            'en_atencion' => $citas->where('estado', 'en_atencion')->count(),
            'atendidos' => $citas->where('estado', 'atendido')->count(),
            'cancelados' => $citas->where('estado', 'cancelado')->count(),
        ];

        return response()->json([
            'success' => true,
            'citas' => $citas,
            'stats' => $stats,
            'fecha' => $fecha
        ]);
    }

    public function crearNuevaCita(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validar que no exista una cita en el mismo horario
            $citaExistente = Cita::where('fecha', $request->fecha)
                ->where('hora', $request->hora)
                ->where('ci_medico', $request->ci_medico)
                ->where('estado', '!=', 'cancelado')
                ->first();

            if ($citaExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El médico ya tiene una cita programada en este horario'
                ], 422);
            }

            // Crear la cita
            $cita = Cita::create([
                'ci_paciente' => $request->ci_paciente,
                'ci_medico' => $request->ci_medico,
                'codigo_especialidad' => $request->codigo_especialidad,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'motivo' => $request->motivo,
                'observaciones' => $request->observaciones ?? '',
                'estado' => 'programado',
                'user_registro_id' => Auth::id(),
            ]);

            DB::commit();

            // Registrar en bitácora
            $this->logActivity(
                'crear_cita',
                'Recepción creó cita para: ' . ($cita->paciente ? $cita->paciente->nombre : 'Paciente') . ' - Fecha: ' . $cita->fecha . ' ' . $cita->hora,
                $cita
            );

            return response()->json([
                'success' => true,
                'message' => 'Cita creada exitosamente',
                'cita' => $cita->load(['paciente', 'medico' => function ($query) {
                    $query->with('user');
                }, 'especialidad'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la cita: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmarCita(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            
            if ($cita->confirmado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta cita ya fue confirmada'
                ], 422);
            }

            $cita->confirmar(Auth::id());

            // Registrar en bitácora
            $this->logActivity(
                'confirmar_cita',
                'Recepción confirmó cita #' . $cita->id . ' para: ' . ($cita->paciente ? $cita->paciente->nombre : 'Paciente'),
                $cita
            );

            return response()->json([
                'success' => true,
                'message' => 'Cita confirmada exitosamente',
                'cita' => $cita->load(['paciente', 'medico' => function ($query) {
                    $query->with('user');
                }, 'especialidad'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al confirmar la cita: ' . $e->getMessage()
            ], 500);
        }
    }

    public function registrarLlegadaPaciente(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            
            if (!$cita->confirmado) {
                $cita->confirmar(Auth::id());
            }

            $cita->iniciarAtencion();

            return response()->json([
                'success' => true,
                'message' => 'Llegada del paciente registrada. En atención ahora.',
                'cita' => $cita->load(['paciente', 'medico' => function ($query) {
                    $query->with('user');
                }, 'especialidad'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar llegada: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelarCita(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            
            if ($cita->estado === 'atendido') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar una cita ya atendida'
                ], 422);
            }

            $cita->cancelar($request->motivo ?? 'Cancelado por recepción');

            // Registrar en bitácora
            $this->logActivity(
                'cancelar_cita',
                'Recepción canceló cita #' . $cita->id . ' para: ' . ($cita->paciente ? $cita->paciente->nombre : 'Paciente') . ' - Motivo: ' . ($request->motivo ?? 'No especificado'),
                $cita
            );

            return response()->json([
                'success' => true,
                'message' => 'Cita cancelada exitosamente',
                'cita' => $cita->load(['paciente', 'medico' => function ($query) {
                    $query->with('user');
                }, 'especialidad'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la cita: ' . $e->getMessage()
            ], 500);
        }
    }

    // MÉTODOS PARA GESTIÓN DE LLAMADAS

    public function getPendientesLlamada()
    {
        // Mostrar todas las citas futuras (no canceladas) para gestión de llamadas
        $citas = Cita::where('fecha', '>=', Carbon::today())
            ->where('estado', '!=', 'cancelado')
            ->with(['paciente' => function ($query) {
                $query->select('ci', 'nombre', 'telefono', 'fecha_nacimiento');
            }, 'medico' => function ($query) {
                $query->with('user');
            }, 'especialidad'])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        // Agrupar por tipo
        $hoy = $citas->where('fecha', Carbon::today())->values();
        $manana = $citas->where('fecha', Carbon::tomorrow())->values();
        $futuras = $citas->where('fecha', '>', Carbon::tomorrow())->values();

        return response()->json([
            'success' => true,
            'hoy' => $hoy,
            'manana' => $manana,
            'futuras' => $futuras,
            'total' => $citas->count()
        ]);
    }

    public function registrarLlamadaCita(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);
            
            $cita->registrarLlamada(
                $request->notas ?? '',
                Auth::id()
            );

            // Si la llamada fue exitosa y confirmaron, marcar como confirmado
            if ($request->confirmado) {
                $cita->confirmar(Auth::id());
            }

            return response()->json([
                'success' => true,
                'message' => 'Llamada registrada exitosamente',
                'cita' => $cita->load(['paciente', 'medico' => function ($query) {
                    $query->with('user');
                }, 'especialidad'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar llamada: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEstadisticasDashboard()
    {
        $hoy = Carbon::today();
        
        // Contar emergencias activas del día
        $emergenciasActivas = Emergency::whereDate('created_at', $hoy)
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])
            ->count();
        
        $stats = [
            'citas_programadas' => Cita::delDia()->count(),
            'en_atencion' => Cita::enAtencion()->count() + $emergenciasActivas,
            'en_espera' => Cita::enEspera()->count(),
            'admisiones' => Caja::whereDate('fecha', $hoy)
                ->where('tipo', 'CONSULTA_EXTERNA')
                ->count() + $emergenciasActivas,
            'por_confirmar' => Cita::porConfirmar()->count(),
            'llamadas_pendientes' => Cita::pendientesLlamada()->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function buscarMedicosDisponibles(Request $request)
    {
        $especialidadCodigo = $request->get('especialidad');
        $fecha = $request->get('fecha');
        $hora = $request->get('hora');

        // Incluir todos los códigos con el mismo nombre (normalizado) por si hay "Medicina General" y "Medicina general"
        $codigosEspecialidad = [];
        if ($especialidadCodigo) {
            $nombre = Especialidad::where('codigo', $especialidadCodigo)->value('nombre');
            if ($nombre) {
                $nombreNorm = strtolower(trim($nombre));
                $codigosEspecialidad = Especialidad::all()
                    ->filter(fn ($e) => strtolower(trim($e->nombre)) === $nombreNorm)
                    ->pluck('codigo')
                    ->toArray();
            } else {
                $codigosEspecialidad = [$especialidadCodigo];
            }
        }

        $medicos = Medico::with(['user', 'especialidad'])
            ->when(!empty($codigosEspecialidad), function ($query) use ($codigosEspecialidad) {
                return $query->whereIn('codigo_especialidad', $codigosEspecialidad);
            })
            ->get();

        // Filtrar médicos que no tienen cita en ese horario
        $medicosDisponibles = $medicos->filter(function($medico) use ($fecha, $hora) {
            if (!$fecha || !$hora) return true;
            
            $citaExistente = Cita::where('fecha', $fecha)
                ->where('hora', $hora)
                ->where('ci_medico', $medico->ci)
                ->where('estado', '!=', 'cancelado')
                ->first();
            
            return !$citaExistente;
        });

        return response()->json([
            'success' => true,
            'medicos' => $medicosDisponibles->map(function($medico) {
                return [
                    'ci' => $medico->ci,
                    'nombre' => $medico->user ? $medico->user->name : 'Sin usuario',
                    'especialidad' => $medico->especialidad ? $medico->especialidad->nombre : 'Sin especialidad',
                ];
            })
        ]);
    }

    public function getEspecialidades()
    {
        try {
            // Una sola opción por nombre (ignorar mayúsculas/espacios: "Medicina General" = "Medicina general")
            $especialidades = Especialidad::where('estado', 'activo')->orderBy('nombre')
                ->get()
                ->unique(fn ($esp) => strtolower(trim($esp->nombre)))
                ->values();

            return response()->json($especialidades);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar especialidades: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vista de listado de pacientes para recepción (Historial de Pacientes)
     */
    public function pacientesIndex(Request $request): View
    {
        $query = Paciente::with([
                'seguro',
                'triage',
                'registro.user',
                'consultas' => function($q) {
                    $q->with('caja')->orderBy('fecha', 'desc')->limit(1);
                }
            ])
            ->whereHas('registro');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhereHas('registro', function($rq) use ($search) {
                      $rq->where('codigo', 'LIKE', "%{$search}%");
                  });
            });
        }

        $pacientes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('reception.pacientes.index', compact('pacientes'));
    }

    /**
     * Vista de historial clínico completo de un paciente
     */
    public function pacientesHistorial($ci): View
    {
        $paciente = Paciente::with([
            'seguro',
            'triage',
            'registro.user',
            'consultas' => function($q) {
                $q->with(['medico.user', 'especialidad', 'caja'])
                  ->orderBy('fecha', 'desc');
            },
            'emergencias' => function($q) {
                $q->with(['user'])
                  ->orderBy('created_at', 'desc');
            },
            'hospitalizaciones' => function($q) {
                $q->with(['medico.user', 'habitacion'])
                  ->orderBy('fecha_ingreso', 'desc');
            }
        ])->findOrFail($ci);

        $cirugiasHistorial = Cirugia::whereHas('emergencia', function($q) use ($ci) {
                $q->where('patient_id', $ci);
            })
            ->with(['quirofano', 'emergencia.paciente'])
            ->orderBy('fecha', 'desc')
            ->get();

        return view('reception.pacientes.historial', compact(
            'paciente',
            'cirugiasHistorial'
        ));
    }

    /**
     * Vista de historial clínico optimizada para impresión
     */
    public function pacientesHistorialPrint($ci): View
    {
        $paciente = Paciente::with([
            'seguro',
            'triage',
            'registro.user',
            'consultas' => function($q) {
                $q->with(['medico.user', 'especialidad', 'caja'])
                  ->orderBy('fecha', 'desc');
            },
            'emergencias' => function($q) {
                $q->with(['user'])
                  ->orderBy('created_at', 'desc');
            },
            'hospitalizaciones' => function($q) {
                $q->with(['medico.user', 'habitacion'])
                  ->orderBy('fecha_ingreso', 'desc');
            }
        ])->findOrFail($ci);

        $cirugiasHistorial = Cirugia::whereHas('emergencia', function($q) use ($ci) {
                $q->where('patient_id', $ci);
            })
            ->with(['quirofano', 'emergencia.paciente'])
            ->orderBy('fecha', 'desc')
            ->get();

        $fechaImpresion = Carbon::now();

        return view('reception.pacientes.historial-print', compact(
            'paciente',
            'cirugiasHistorial',
            'fechaImpresion'
        ));
    }

    // MÉTODOS ADICIONALES PARA GESTIÓN COMPLETA DE CITAS

    public function eliminarCita(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);

            if ($cita->estado === 'atendido') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una cita ya atendida'
                ], 422);
            }

            $cita->delete(); // Soft delete

            return response()->json([
                'success' => true,
                'message' => 'Cita eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la cita: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restaurarCita($id)
    {
        try {
            $cita = Cita::withTrashed()->findOrFail($id);
            $cita->restore();

            return response()->json([
                'success' => true,
                'message' => 'Cita restaurada exitosamente',
                'cita' => $cita->load(['paciente', 'medico.user', 'especialidad'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar la cita: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCitasEliminadas()
    {
        try {
            $citas = Cita::onlyTrashed()
                ->with(['paciente', 'medico.user', 'especialidad'])
                ->orderBy('deleted_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'citas' => $citas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener citas eliminadas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function marcarAsistida(Request $request, $id)
    {
        try {
            $cita = Cita::findOrFail($id);

            if ($cita->estado === 'cancelado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede marcar asistida una cita cancelada'
                ], 422);
            }

            if ($cita->estado === 'atendido') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta cita ya fue marcada como atendida'
                ], 422);
            }

            $cita->completarAtencion();

            return response()->json([
                'success' => true,
                'message' => 'Cita marcada como atendida exitosamente',
                'cita' => $cita->load(['paciente', 'medico.user', 'especialidad'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar asistencia: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAgendaSemanal(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio', Carbon::today()->startOfWeek()->toDateString());
            $fechaFin = $request->get('fecha_fin', Carbon::today()->endOfWeek()->toDateString());

            $citas = Cita::whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->where('estado', '!=', 'cancelado')
                ->with(['paciente', 'medico.user', 'especialidad'])
                ->orderBy('fecha')
                ->orderBy('hora')
                ->get();

            // Agrupar por día
            $citasPorDia = $citas->groupBy(function($cita) {
                return Carbon::parse($cita->fecha)->format('Y-m-d');
            });

            return response()->json([
                'success' => true,
                'citas_por_dia' => $citasPorDia,
                'citas' => $citas,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener agenda semanal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCitasPorPaciente($ci)
    {
        try {
            $citas = Cita::where('ci_paciente', $ci)
                ->with(['medico.user', 'especialidad'])
                ->orderBy('fecha', 'desc')
                ->orderBy('hora', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'citas' => $citas,
                'total' => $citas->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener citas del paciente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function buscarGarante(Request $request)
    {
        $ci = $request->get('ci');

        if (strlen($ci) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'El CI debe tener al menos 3 dígitos'
            ]);
        }

        $garante = Paciente::where('ci', $ci)
            ->where(function($q) {
                $q->whereNull('seguro_id')
                  ->whereNull('triage_id')
                  ->whereNull('registro_codigo');
            })
            ->orWhere(function($q) {
                $q->whereNotNull('ci');
            })
            ->first();

        if ($garante) {
            return response()->json([
                'success' => true,
                'garante' => $garante,
                'message' => 'Persona encontrada'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Persona no encontrada. Debe registrarla primero.',
                'show_form' => true
            ]);
        }
    }

    public function buscarGaranteExacto(Request $request)
    {
        $ci = $request->input('ci');

        if (strlen($ci) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'El CI debe tener al menos 3 caracteres'
            ]);
        }

        $garante = Paciente::where('ci', $ci)->first();

        if ($garante) {
            return response()->json([
                'success' => true,
                'garante' => [
                    'ci' => $garante->ci,
                    'nombre' => $garante->nombre,
                    'telefono' => $garante->telefono,
                    'correo' => $garante->correo,
                    'direccion' => $garante->direccion
                ],
                'message' => 'Garante encontrado'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Garante no encontrado'
        ]);
    }

    public function registrarGarante(Request $request)
    {
        try {
            $request->validate([
                'ci' => 'required|string|max:20|unique:pacientes,ci',
                'lugar_expedicion' => 'nullable|string|max:2',
                'nombres' => 'required|string|max:150',
                'apellidos' => 'required|string|max:150',
                'sexo' => 'required|in:M,F',
                'fecha_nacimiento' => 'required|date',
                'nacionalidad' => 'nullable|string|max:100',
                'estado_civil' => 'nullable|string|max:50',
                'telefono' => 'nullable|string|max:20',
                'correo' => 'nullable|email|max:100',
                'profesion' => 'nullable|string|max:150',
                'empresa_trabajo' => 'nullable|string|max:150',
                'direccion' => 'nullable|string',
            ]);

            $nombreCompleto = $request->nombres . ' ' . $request->apellidos;

            $garante = Paciente::create([
                'ci' => $request->ci,
                'lugar_expedicion' => $request->lugar_expedicion,
                'nombre' => $nombreCompleto,
                'sexo' => $request->sexo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'nacionalidad' => $request->nacionalidad,
                'estado_civil' => $request->estado_civil,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'profesion' => $request->profesion,
                'empresa_trabajo' => $request->empresa_trabajo,
                'direccion' => $request->direccion,
            ]);

            return response()->json([
                'success' => true,
                'garante' => [
                    'ci' => $garante->ci,
                    'nombre' => $garante->nombre,
                    'telefono' => $garante->telefono
                ],
                'message' => 'Garante registrado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar garante: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registra un paciente completo para crear cita
     * Incluye registro_codigo y triage_id automáticos
     */
    public function registrarPacienteParaCita(Request $request)
    {
        try {
            $request->validate([
                'ci' => 'required|string|max:20|unique:pacientes,ci',
                'nombres' => 'required|string|max:150',
                'apellidos' => 'required|string|max:150',
                'sexo' => 'required|in:M,F',
                'fecha_nacimiento' => 'required|date',
                'lugar_expedicion' => 'nullable|string|max:2',
                'nacionalidad' => 'nullable|string|max:100',
                'estado_civil' => 'nullable|string|max:50',
                'telefono' => 'nullable|string|max:20',
                'correo' => 'nullable|email|max:100',
                'direccion' => 'nullable|string',
                'profesion' => 'nullable|string|max:150',
                'empresa_trabajo' => 'nullable|string|max:150',
            ]);

            DB::beginTransaction();

            // Crear registro y triage automáticos para el paciente
            $registroCodigo = $this->obtenerOCrearRegistro();
            $triageId = $this->obenerOCrearTriage();

            $nombreCompleto = trim($request->nombres . ' ' . $request->apellidos);

            $paciente = Paciente::create([
                'ci' => $request->ci,
                'nombre' => $nombreCompleto,
                'sexo' => $request->sexo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'lugar_expedicion' => $request->lugar_expedicion,
                'nacionalidad' => $request->nacionalidad ?? 'Boliviana',
                'estado_civil' => $request->estado_civil,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'direccion' => $request->direccion,
                'profesion' => $request->profesion,
                'empresa_trabajo' => $request->empresa_trabajo,
                'registro_codigo' => $registroCodigo,
                'triage_id' => $triageId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'paciente' => [
                    'ci' => $paciente->ci,
                    'nombre' => $paciente->nombre,
                    'telefono' => $paciente->telefono,
                ],
                'message' => 'Paciente registrado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar paciente: ' . $e->getMessage()
            ], 500);
        }
    }
}
