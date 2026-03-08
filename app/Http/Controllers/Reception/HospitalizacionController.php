<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Hospitalizacion;
use App\Models\Triage;
use App\Models\Registro;
use App\Models\Seguro;
use App\Models\Medico;
use App\Models\Especialidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HospitalizacionController extends Controller
{
    public function index()
    {
        return view('reception.hospitalizacion');
    }

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

    public function registrarHospitalizacion(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Crear o encontrar paciente
            $paciente = $this->crearOActualizarPaciente($request);

            // 2. Verificar si ya tiene una hospitalización activa
            $hospitalizacionActiva = Hospitalizacion::where('ci_paciente', $paciente->ci)
                ->whereNull('fecha_alta')
                ->first();

            if ($hospitalizacionActiva) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este paciente ya tiene una hospitalización activa. Código: ' . $hospitalizacionActiva->id,
                ], 422);
            }

            // 3. Crear triage (generalmente amarillo para hospitalización)
            $triage = $this->crearTriagePorTipo('amarillo');
            $paciente->update(['id_triage' => $triage->id]);

            // 4. Crear registro de hospitalización
            $hospitalizacion = $this->crearHospitalizacion($request, $paciente, $triage);

            // 5. Procesar acciones adicionales
            $acciones = $this->procesarAccionesHospitalizacion($request, $paciente, $hospitalizacion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hospitalización registrada exitosamente',
                'hospitalizacion' => $hospitalizacion->load(['paciente', 'triage']),
                'acciones' => $acciones
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la hospitalización: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHospitalizacionesActivas()
    {
        try {
            $hospitalizaciones = Hospitalizacion::with(['paciente', 'triage', 'medico.usuario', 'medico.especialidad'])
                ->whereNull('fecha_alta')
                ->orderBy('fecha_ingreso', 'desc')
                ->get();

            // Formatear datos para la vista
            $hospitalizacionesFormateadas = $hospitalizaciones->map(function($hospitalizacion) {
                return [
                    'codigo' => $hospitalizacion->id,
                    'paciente' => [
                        'nombre' => $hospitalizacion->paciente->nombre,
                        'ci' => $hospitalizacion->paciente->ci
                    ],
                    'tipo' => strtolower($hospitalizacion->tipo),
                    'servicio' => $this->getServicioNombre($hospitalizacion->servicio),
                    'motivo' => $hospitalizacion->motivo,
                    'fecha_ingreso' => $hospitalizacion->fecha_ingreso->format('d/m/Y'),
                    'medico' => [
                        'usuario' => [
                            'name' => $hospitalizacion->medico->usuario->name ?? 'No asignado'
                        ]
                    ],
                    'habitacion' => $hospitalizacion->habitacion ?? 'Por asignar'
                ];
            });

            return response()->json([
                'success' => true,
                'hospitalizaciones' => $hospitalizacionesFormateadas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar hospitalizaciones activas: ' . $e->getMessage()
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
            ]);

            // Crear nuevo paciente
            $paciente = Paciente::create([
                'ci' => $ci,
                'nombre' => trim($request->nombres . ' ' . $request->apellidos),
                'sexo' => $request->sexo,
                'direccion' => $request->direccion ?? 'Sin especificar',
                'telefono' => $request->telefono ?? 0,
                'correo' => $request->correo ?? 'sin@email.com',
                'codigo_seguro' => $this->obtenerOCrearSeguro($request->seguro),
                'id_triage' => null, // Se asignará después
                'codigo_registro' => $this->obtenerOCrearRegistro(),
            ]);
        } else {
            // Actualizar datos si es necesario
            $paciente->update([
                'telefono' => $request->telefono ?? $paciente->telefono,
                'correo' => $request->correo ?? $paciente->correo,
                'direccion' => $request->direccion ?? $paciente->direccion,
                'codigo_seguro' => $this->obtenerOCrearSeguro($request->seguro),
            ]);
        }
        
        return $paciente;
    }

    private function crearHospitalizacion($request, $paciente, $triage)
    {
        $idHosp = 'HOSP-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        
        return Hospitalizacion::create([
            'id' => $idHosp,
            'fecha_ingreso' => now(),
            'motivo' => $request->motivo,
            'diagnostico' => $request->diagnostico,
            'tipo' => strtoupper($request->tipo_hospitalizacion),
            'servicio' => $request->servicio,
            'ci_medico' => $request->medico_tratante,
            'ci_paciente' => $paciente->ci,
            'contacto_nombre' => $request->contacto_nombre,
            'contacto_telefono' => $request->contacto_telefono,
            'contacto_parentesco' => $request->contacto_parentesco,
            'contacto_relacion' => $request->contacto_relacion,
            'observaciones' => $request->observaciones,
            'id_triage' => $triage->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function procesarAccionesHospitalizacion($request, $paciente, $hospitalizacion)
    {
        $acciones = [];

        // Acción principal: registro de hospitalización
        $acciones['registro_hospitalizacion'] = true;

        // Requiere cirugía
        if ($request->boolean('requiere_cirugia')) {
            $this->obtenerOCrearRegistroMotivo('Cirugía programada - ' . $paciente->ci);
            $acciones['cirugia_programada'] = true;
        }

        // Requiere UCI
        if ($request->boolean('requiere_uci')) {
            $this->obtenerOCrearRegistroMotivo('Traslado a UCI - ' . $paciente->ci);
            $acciones['traslado_uci'] = true;
        }

        // Paciente de alto riesgo
        if ($request->boolean('paciente_riesgo')) {
            $this->obtenerOCrearRegistroMotivo('Paciente alto riesgo - ' . $paciente->ci);
            $acciones['paciente_riesgo'] = true;
        }

        // Alergias severas
        if ($request->boolean('alergias_severas')) {
            $this->obtenerOCrearRegistroMotivo('Alerta de alergias severas - ' . $paciente->ci);
            $acciones['alerta_alergias'] = true;
        }

        // Asignar habitación (simulación)
        $habitacion = $this->asignarHabitacion($hospitalizacion);
        if ($habitacion) {
            $hospitalizacion->update(['habitacion' => $habitacion]);
            $acciones['habitacion_asignada'] = $habitacion;
        }

        return $acciones;
    }

    private function asignarHabitacion($hospitalizacion)
    {
        // Simulación de asignación de habitación
        $tiposHabitacion = [
            'cirugia' => 'C-',
            'emergencia' => 'E-',
            'observacion' => 'O-',
            'parto' => 'P-',
            'tratamiento' => 'T-',
            'rehabilitacion' => 'R-',
            'uci' => 'UCI-'
        ];

        $prefijo = $tiposHabitacion[strtolower($hospitalizacion->tipo)] ?? 'H-';
        $numero = random_int(1, 20);
        
        return $prefijo . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }

    private function crearTriagePorTipo(string $tipo)
    {
        $currentUser = Auth::user();

        $map = [
            'rojo' => ['color' => 'red', 'descripcion' => 'Emergencia', 'prioridad' => 'alta'],
            'amarillo' => ['color' => 'yellow', 'descripcion' => 'Hospitalización', 'prioridad' => 'media'],
            'verde' => ['color' => 'green', 'descripcion' => 'Observación', 'prioridad' => 'baja'],
        ];

        $cfg = $map[$tipo] ?? $map['amarillo'];
        return Triage::create([
            'id' => 'TRIAGE-' . strtoupper($tipo) . '-' . now()->format('YmdHis') . '-' . random_int(100, 999),
            'color' => $cfg['color'],
            'descripcion' => $cfg['descripcion'],
            'prioridad' => $cfg['prioridad'],
            'id_usuario' => $currentUser->id,
        ]);
    }

    private function obtenerOCrearSeguro($seguroNombre)
    {
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => ucfirst($seguroNombre)],
            [
                'codigo' => Seguro::max('codigo') + 1,
                'tipo' => 'HOSPITALIZACION',
                'cobertura' => 'Cobertura de Hospitalización',
                'telefono' => null,
                'formulario' => 'HOSPITALIZACION',
                'estado' => 'ACTIVO'
            ]
        );
        
        return $seguro->codigo;
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
                'motivo' => 'Registro de Hospitalización',
                'id_usuario' => $currentUser->id
            ]
        );
        
        return $registro->codigo;
    }

    private function obtenerOCrearRegistroMotivo(string $motivo)
    {
        return Registro::firstOrCreate(
            ['codigo' => 'REG-' . now()->format('YmdHis') . '-' . random_int(100, 999)],
            [
                'fecha' => now()->toDateString(),
                'hora' => now()->toTimeString(),
                'motivo' => $motivo,
                'id_usuario' => Auth::id(),
            ]
        );
    }

    private function getServicioNombre($servicio)
    {
        $servicios = [
            'medicina_interna' => 'Medicina Interna',
            'cirugia_general' => 'Cirugía General',
            'ginecologia' => 'Ginecología',
            'pediatria' => 'Pediatría',
            'cardiologia' => 'Cardiología',
            'neurologia' => 'Neurología',
            'oncologia' => 'Oncología',
            'uci' => 'UCI',
            'uci_neonatal' => 'UCI Neonatal',
        ];

        return $servicios[$servicio] ?? $servicio;
    }

    public function darAlta(Request $request, $id)
    {
        try {
            $hospitalizacion = Hospitalizacion::findOrFail($id);
            
            if ($hospitalizacion->fecha_alta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta hospitalización ya fue dada de alta'
                ], 422);
            }

            $hospitalizacion->update([
                'fecha_alta' => now(),
                'motivo_alta' => $request->motivo_alta ?? 'Alta médica',
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paciente dado de alta exitosamente',
                'hospitalizacion' => $hospitalizacion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al dar de alta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function actualizarDatos(Request $request, $id)
    {
        try {
            $hospitalizacion = Hospitalizacion::findOrFail($id);
            
            $hospitalizacion->update([
                'motivo' => $request->motivo ?? $hospitalizacion->motivo,
                'diagnostico' => $request->diagnostico ?? $hospitalizacion->diagnostico,
                'observaciones' => $request->observaciones ?? $hospitalizacion->observaciones,
                'habitacion' => $request->habitacion ?? $hospitalizacion->habitacion,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Datos de hospitalización actualizados exitosamente',
                'hospitalizacion' => $hospitalizacion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
