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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReceptionController extends Controller
{
    public function index()
    {
        return view('reception.reception');
    }

    // MODIFICA ESTA FUNCIÓN:
    public function admision(Request $request)
    {
        // 1. Recibimos el paso de la URL (si no hay, por defecto es 1)
        $paso = $request->get('paso', 1);

        // 2. Le pasamos la variable $paso a la vista admision
        return view('reception.admision', compact('paso'));
    }

    // NUEVAS FUNCIONES PARA CONSULTA EXTERNA
    public function buscarPaciente(Request $request)
    {
        $ci = $request->get('ci');
        
        if (strlen($ci) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'El CI debe tener al menos 8 dígitos'
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

            return response()->json([
                'success' => true,
                'message' => 'Consulta externa registrada exitosamente. El paciente debe pasar a caja para realizar el pago.',
                'consulta' => $consulta->load(['paciente', 'medico.usuario', 'especialidad']),
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
                'sexo' => 'required|string|in:Masculino,Femenino',
                'seguro' => 'required|string',
            ]);

            // Crear nuevo paciente con todos los campos requeridos
            $paciente = Paciente::create([
                'ci' => $ci,
                'nombre' => trim($request->nombres . ' ' . $request->apellidos),
                'sexo' => $request->sexo,
                'direccion' => $request->direccion ?? 'Sin especificar',
                'telefono' => $request->telefono ?? 0, // Valor por defecto para NOT NULL
                'correo' => $request->correo ?? 'sin@email.com', // Valor por defecto para NOT NULL
                'codigo_seguro' => $this->obtenerOCrearSeguro($request->seguro),
                'id_triage' => $this->obenerOCrearTriage(),
                'codigo_registro' => $this->obtenerOCrearRegistro(),
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
            ]);
        }
        
        return $paciente;
    }

    private function crearRegistroCajaPendiente($request, $paciente)
    {
        // Obtener precio del servicio desde la base de datos
        $servicio = \App\Models\Servicio::getServicioPorTipo('CONSULTA_EXTERNA');
        $costoConsulta = $servicio ? $servicio->precio : 50.00; // Fallback a 50.00 si no encuentra
        
        return Caja::create([
            'fecha' => now(),
            'total_dia' => $costoConsulta,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => null, // Ahora es nullable, pendiente de pago
            'id_farmacia' => null, // No aplica para consultas externas
            'nro_pago_internos' => 'CONSULTA-' . date('YmdHis'), // Referencia temporal
            'monto_pagado' => $costoConsulta, // Add this field for the view
        ]);
    }

    private function crearConsulta($request, $paciente, $caja)
    {
        // Obtener médico basado en la selección
        $medicoId = $this->obtenerMedicoId($request->medico);
        
        return Consulta::create([
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'motivo' => $request->motivo,
            'observaciones' => $request->observaciones ?? '',
            'codigo_especialidad' => $this->obtenerEspecialidadCodigo($request->especialidad),
            'ci_paciente' => $paciente->ci,
            'ci_medico' => $medicoId,
            'estado_pago' => false,
            'id_caja' => $caja->id,
            'estado' => 'pendiente',
        ]);
    }

    private function obtenerOCrearSeguro($seguroNombre)
    {
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => ucfirst($seguroNombre)],
            [
                'codigo' => Seguro::max('codigo') + 1,
                'tipo' => 'CONSULTA',
                'cobertura' => 'Consulta Externa',
                'telefono' => null,
                'formulario' => 'ESTANDAR',
                'estado' => 'ACTIVO'
            ]
        );
        
        return $seguro->codigo;
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
                'id_usuario' => $currentUser->id
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
                'id_usuario' => $currentUser->id
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
        $caja = Caja::with(['consulta.paciente', 'consulta.medico.usuario', 'consulta.especialidad'])
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
            
            return response()->json([
                'success' => true,
                'message' => 'Pago procesado exitosamente',
                'factura' => $caja->nro_factura
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
        $caja = Caja::with(['consulta.paciente', 'consulta.medico.usuario', 'consulta.especialidad'])
                     ->findOrFail($id);
        
        return view('reception.confirmacion-registro', compact('caja'));
    }

    // MÉTODOS PARA GESTIÓN DE CITAS Y AGENDA

    public function getAgendaDia(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::today()->toDateString());
        
        $citas = Cita::delDia($fecha)
            ->with(['paciente', 'medico.usuario', 'especialidad'])
            ->orderBy('hora')
            ->get();

        // Estadísticas del día
        $stats = [
            'total' => $citas->count(),
            'confirmados' => $citas->where('confirmado', true)->count(),
            'en_espera' => $citas->enEspera()->count(),
            'en_atencion' => $citas->enAtencion()->count(),
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
                'id_usuario_registro' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cita creada exitosamente',
                'cita' => $cita->load(['paciente', 'medico.usuario', 'especialidad'])
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

            return response()->json([
                'success' => true,
                'message' => 'Cita confirmada exitosamente',
                'cita' => $cita->load(['paciente', 'medico.usuario', 'especialidad'])
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
                'cita' => $cita->load(['paciente', 'medico.usuario', 'especialidad'])
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

            return response()->json([
                'success' => true,
                'message' => 'Cita cancelada exitosamente',
                'cita' => $cita->load(['paciente', 'medico.usuario', 'especialidad'])
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
        $citas = Cita::pendientesLlamada()
            ->with(['paciente', 'medico.usuario', 'especialidad'])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        // Agrupar por tipo
        $recordatorios = $citas->where('fecha', Carbon::today());
        $confirmaciones = $citas->where('fecha', '>', Carbon::today());
        $seguimientos = []; // Podríamos agregar post-alta aquí

        return response()->json([
            'success' => true,
            'recordatorios' => $recordatorios,
            'confirmaciones' => $confirmaciones,
            'seguimientos' => $seguimientos,
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
                'cita' => $cita->load(['paciente', 'medico.usuario', 'especialidad'])
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
        
        $stats = [
            'citas_programadas' => Cita::delDia()->count(),
            'en_atencion' => Cita::enAtencion()->count(),
            'en_espera' => Cita::enEspera()->count(),
            'admisiones' => Caja::whereDate('fecha', $hoy)
                ->where('tipo', 'CONSULTA_EXTERNA')
                ->count(),
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
        $especialidad = $request->get('especialidad');
        $fecha = $request->get('fecha');
        $hora = $request->get('hora');

        $medicos = Medico::with(['usuario', 'especialidades'])
            ->when($especialidad, function($query) use ($especialidad) {
                return $query->whereHas('especialidades', function($q) use ($especialidad) {
                    $q->where('codigo', $especialidad);
                });
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
            'medicos' => $medicosDisponibles
        ]);
    }

    public function getEspecialidades()
    {
        try {
            $especialidades = Especialidad::orderBy('nombre')->get();
            
            return response()->json($especialidades);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar especialidades: ' . $e->getMessage()
            ], 500);
        }
    }
}
