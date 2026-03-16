<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Especialidad;
use App\Models\Receta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Debug information
        if (!$user) {
            return response()->json(['error' => 'No authenticated user'], 401);
        }
        
        // Check if user has the correct role
        if (!in_array($user->role, ['dirmedico', 'admin', 'doctor'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Acceso denegado. Esta sección es solo para personal médico.');
        }
        
        // Obtener el médico asociado al usuario actual
        $medico = Medico::where('id_usuario', $user->id)->first();
        
        if (!$medico) {
            // Return view with error message instead of aborting
            return view('medical.consulta-externa', [
                'error' => 'No se encontró información del médico asociado a este usuario. Usuario ID: ' . $user->id . ', Role: ' . $user->role,
                'medico' => null,
                'consultasPendientes' => collect([]),
                'consultasEnAtencion' => collect([]),
                'consultasCompletadas' => collect([])
            ]);
        }

        // Para administradores, mostrar vista de control total
        if ($user->role === 'admin') {
            return $this->vistaControlTotal();
        }
        
        // Para rol 'doctor', mostrar el dashboard del médico
        if ($user->role === 'doctor') {
            return $this->vistaDoctorDashboard($medico);
        }

        // Obtener consultas del día que están pagadas y pendientes de atención
        $consultasHoy = Consulta::with(['paciente', 'especialidad', 'caja'])
            ->where('ci_medico', $medico->ci)
            ->where('fecha', date('Y-m-d'))
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('cajas')
                      ->whereColumn('cajas.id', 'consultas.id_caja')
                      ->whereNotNull('cajas.nro_factura');
            })
            ->orderBy('hora')
            ->get();

        // Separar consultas por estado
        $consultasPendientes = $consultasHoy->where('estado', 'pendiente');
        $consultasEnAtencion = $consultasHoy->where('estado', 'en_atencion');
        $consultasCompletadas = $consultasHoy->where('estado', 'completada');

        return view('doctor.consulta-externa', compact(
            'medico',
            'consultasPendientes',
            'consultasEnAtencion',
            'consultasCompletadas'
        ));
    }

    private function vistaDoctorDashboard($medico)
    {
        // Obtener consultas del día que están pagadas y pendientes de atención
        $consultasHoy = Consulta::with(['paciente', 'especialidad', 'caja'])
            ->where('ci_medico', $medico->ci)
            ->where('fecha', date('Y-m-d'))
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('cajas')
                      ->whereColumn('cajas.id', 'consultas.id_caja')
                      ->whereNotNull('cajas.nro_factura');
            })
            ->orderBy('hora')
            ->get();

        // Separar consultas por estado
        $consultasPendientes = $consultasHoy->where('estado', 'pendiente');
        $consultasEnAtencion = $consultasHoy->where('estado', 'en_atencion');
        $consultasCompletadas = $consultasHoy->where('estado', 'completada');

        return view('doctor.dashboard', compact(
            'medico',
            'consultasPendientes',
            'consultasEnAtencion',
            'consultasCompletadas'
        ));
    }

    private function vistaControlTotal()
    {
        // Obtener todos los médicos del sistema
        $medicos = Medico::with(['usuario', 'especialidad'])
            ->orderBy('codigo_especialidad')
            ->orderByRaw('(SELECT name FROM users WHERE id = medicos.id_usuario)')
            ->get();

        // Obtener todas las consultas de hoy con información completa
        $consultasHoy = Consulta::with(['paciente', 'medico.usuario', 'especialidad', 'caja'])
            ->where('fecha', date('Y-m-d'))
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('cajas')
                      ->whereColumn('cajas.id', 'consultas.id_caja')
                      ->whereNotNull('cajas.nro_factura');
            })
            ->orderBy('hora')
            ->get();

        // Agrupar consultas por médico
        $consultasPorMedico = [];
        foreach ($medicos as $medico) {
            $consultasMedico = $consultasHoy->where('ci_medico', $medico->ci);
            
            $consultasPorMedico[$medico->ci] = [
                'medico' => $medico,
                'consultasPendientes' => $consultasMedico->where('estado', 'pendiente'),
                'consultasEnAtencion' => $consultasMedico->where('estado', 'en_atencion'),
                'consultasCompletadas' => $consultasMedico->where('estado', 'completada'),
                'totalConsultas' => $consultasMedico->count(),
                'pacientesUnicos' => $consultasMedico->pluck('paciente.ci')->unique()->count()
            ];
        }

        // Estadísticas generales
        $stats = [
            'totalMedicos' => $medicos->count(),
            'totalConsultasHoy' => $consultasHoy->count(),
            'consultasPendientes' => $consultasHoy->where('estado', 'pendiente')->count(),
            'consultasEnAtencion' => $consultasHoy->where('estado', 'en_atencion')->count(),
            'consultasCompletadas' => $consultasHoy->where('estado', 'completada')->count(),
            'pacientesUnicosHoy' => $consultasHoy->pluck('paciente.ci')->unique()->count(),
            'medicosActivos' => $consultasHoy->pluck('ci_medico')->unique()->count()
        ];

        return view('medical.consulta-externa-control', compact(
            'medicos',
            'consultasPorMedico',
            'stats'
        ));
    }

    public function iniciarConsulta($consultaId)
    {
        try {
            \Log::info('Attempting to start consultation', [
                'consultaId' => $consultaId,
                'user_id' => Auth::id()
            ]);

            $consulta = Consulta::findOrFail($consultaId);
            
            // Verificar que la consulta pertenezca al médico actual
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $medico = Medico::where('id_usuario', $user->id)->first();
            
            if (!$medico) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del médico'
                ], 403);
            }
            
            if ($consulta->ci_medico !== $medico->ci) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para iniciar esta consulta'
                ], 403);
            }

            // Verificar que la consulta esté en estado "pendiente"
            if ($consulta->estado !== 'pendiente') {
                \Log::warning('Invalid consultation state for start', [
                    'consulta_id' => $consulta->nro,
                    'current_state' => $consulta->estado,
                    'required_state' => 'pendiente'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'La consulta debe estar pendiente para poder iniciarse'
                ], 400);
            }

            // Actualizar estado a "en atención"
            $consulta->estado = 'en_atencion';
            $consulta->save();

            \Log::info('Consulta started successfully', [
                'consulta_id' => $consulta->nro,
                'new_state' => 'en_atencion'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Consulta iniciada correctamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error starting consultation', [
                'consultaId' => $consultaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar la consulta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completarConsulta($consultaId, Request $request)
    {
        try {
            // Debug information
            \Log::info('Attempting to complete consultation', [
                'consultaId' => $consultaId,
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            $consulta = Consulta::findOrFail($consultaId);
            
            // Verificar que la consulta pertenezca al médico actual
            $user = Auth::user();
            
            if (!$user) {
                \Log::error('User not authenticated');
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $medico = Medico::where('id_usuario', $user->id)->first();
            
            if (!$medico) {
                \Log::error('No medico found for user', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del médico'
                ], 403);
            }
            
            if ($consulta->ci_medico !== $medico->ci) {
                \Log::error('CI mismatch in completarConsulta', [
                    'consulta_medico_ci' => $consulta->ci_medico,
                    'user_medico_ci' => $medico->ci
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para completar esta consulta'
                ], 403);
            }

            // Verificar que la consulta esté en estado "en_atencion"
            if ($consulta->estado !== 'en_atencion') {
                \Log::error('Invalid consultation state for completion', [
                    'consulta_id' => $consulta->nro,
                    'current_state' => $consulta->estado,
                    'required_state' => 'en_atencion'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'La consulta debe estar en atención para poder completarse'
                ], 400);
            }

            // Actualizar estado a "completada"
            $consulta->estado = 'completada';
            $consulta->observaciones = $request->observaciones ?? $consulta->observaciones;
            $consulta->save();

            \Log::info('Consulta completed successfully', [
                'consulta_id' => $consulta->nro,
                'new_state' => 'completada'
            ]);

            // Crear receta si se proporcionaron medicamentos
            if ($request->has('medicamentos') && !empty($request->medicamentos)) {
                $this->crearReceta($consulta, $request);
            }

            return response()->json([
                'success' => true,
                'message' => 'Consulta completada correctamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error completing consultation', [
                'consultaId' => $consultaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al completar la consulta: ' . $e->getMessage()
            ], 500);
        }
    }

    private function crearReceta($consulta, $request)
    {
        $user = Auth::user();
        $medico = Medico::where('id_usuario', $user->id)->first();
        
        $receta = Receta::create([
            'nro' => 'REC-' . date('Y') . '-' . str_pad(Receta::count() + 1, 6, '0', STR_PAD_LEFT),
            'nro_consulta' => $consulta->nro,
            'fecha' => now(),
            'indicaciones' => $request->indicaciones ?? '',
            'id_usuario_medico' => $medico->id_usuario ?? null,
        ]);

        // Aquí se podrían agregar los detalles de los medicamentos si existe la tabla detalle_receta
        // Por ahora solo creamos la receta básica
    }

    public function verHistorialMedico($ci_medico = null)
    {
        $user = Auth::user();
        
        // Obtener el médico asociado al usuario actual
        $medicoUsuario = Medico::where('id_usuario', $user->id)->first();
        
        // Solo administradores pueden ver historial de otros médicos
        // Los doctores solo pueden ver su propio historial
        if ($user->role !== 'admin') {
            if (!$medicoUsuario || ($ci_medico && $ci_medico != $medicoUsuario->ci)) {
                abort(403, 'Acceso denegado');
            }
            // Si es doctor y no se especifica ci_medico, usar su propio CI
            if (!$ci_medico) {
                $ci_medico = $medicoUsuario->ci;
            }
        }
        
        // Si no se especifica médico (y es admin), mostrar lista para seleccionar
        if (!$ci_medico && $user->role === 'admin') {
            $medicos = Medico::with(['usuario', 'especialidad'])
                ->orderByRaw('(SELECT name FROM users WHERE id = medicos.id_usuario)')
                ->get();
                
            return view('medical.historial-medicos', compact('medicos'));
        }
        
        // Obtener el médico específico
        $medico = Medico::with(['usuario', 'especialidad'])
            ->where('ci', $ci_medico)
            ->firstOrFail();
        
        // Obtener historial completo de consultas del médico
        $consultas = Consulta::with(['paciente', 'especialidad', 'caja'])
            ->where('ci_medico', $medico->ci)
            ->whereHas('caja', function($query) {
                $query->whereNotNull('nro_factura'); // Solo consultas pagadas
            })
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(20);
        
        // Estadísticas del médico
        $stats = [
            'totalConsultas' => $consultas->total(),
            'consultasEsteMes' => Consulta::where('ci_medico', $medico->ci)
                ->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->whereHas('caja', function($query) {
                    $query->whereNotNull('nro_factura');
                })
                ->count(),
            'pacientesUnicos' => $consultas->pluck('paciente.ci')->unique()->count(),
            'consultasCompletadas' => Consulta::where('ci_medico', $medico->ci)
                ->where('estado', 'completada')
                ->whereHas('caja', function($query) {
                    $query->whereNotNull('nro_factura');
                })
                ->count(),
        ];
        
        return view('medical.historial-medico', compact(
            'medico',
            'consultas',
            'stats'
        ));
    }
    
    public function verPacientesMedico($ci_medico = null)
    {
        $user = Auth::user();
        
        // Obtener el médico asociado al usuario actual
        $medicoUsuario = Medico::where('id_usuario', $user->id)->first();
        
        // Solo administradores pueden ver pacientes de otros médicos
        // Los doctores solo pueden ver sus propios pacientes
        if ($user->role !== 'admin') {
            if (!$medicoUsuario || ($ci_medico && $ci_medico != $medicoUsuario->ci)) {
                abort(403, 'Acceso denegado');
            }
            // Si es doctor y no se especifica ci_medico, usar su propio CI
            if (!$ci_medico) {
                $ci_medico = $medicoUsuario->ci;
            }
        }
        
        // Si no se especifica médico (y es admin), mostrar lista para seleccionar
        if (!$ci_medico && $user->role === 'admin') {
            $medicos = Medico::with(['usuario', 'especialidad'])
                ->orderByRaw('(SELECT name FROM users WHERE id = medicos.id_usuario)')
                ->get();
                
            return view('medical.pacientes-medicos', compact('medicos'));
        }
        
        // Obtener el médico específico
        $medico = Medico::with(['usuario', 'especialidad'])
            ->where('ci', $ci_medico)
            ->firstOrFail();
        
        // Obtener todos los pacientes únicos del médico
        $pacientes = Paciente::whereHas('consultas', function($query) use ($medico) {
                $query->where('ci_medico', $medico->ci)
                    ->whereHas('caja', function($query) {
                        $query->whereNotNull('nro_factura');
                    });
            })
            ->with(['consultas' => function($query) use ($medico) {
                $query->where('ci_medico', $medico->ci)
                    ->whereHas('caja', function($query) {
                        $query->whereNotNull('nro_factura');
                    })
                    ->orderBy('fecha', 'desc')
                    ->orderBy('hora', 'desc');
            }])
            ->orderBy('nombre')
            ->paginate(15);
        
        // Estadísticas de pacientes
        $stats = [
            'totalPacientes' => $pacientes->total(),
            'pacientesNuevosEsteMes' => Paciente::whereHas('consultas', function($query) use ($medico) {
                    $query->where('ci_medico', $medico->ci)
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->whereHas('caja', function($query) {
                            $query->whereNotNull('nro_factura');
                        });
                })->count(),
            'pacientesConSeguro' => $pacientes->whereNotNull('id_seguro')->count(),
            'promedioConsultasPorPaciente' => $pacientes->avg(function($paciente) {
                return $paciente->consultas->count();
            }),
        ];
        
        return view('medical.pacientes-medico', compact(
            'medico',
            'pacientes',
            'stats'
        ));
    }

    public function getPaciente($ci)
    {
        $paciente = Paciente::where('ci', $ci)->first();
        
        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'paciente' => $paciente
        ]);
    }

    public function verConsulta($consultaId)
    {
        $consulta = Consulta::with([
            'paciente',
            'medico.usuario',
            'especialidad',
            'caja',
            'recetas'
        ])->findOrFail($consultaId);

        $user = Auth::user();
        
        // Debug information
        if (!$user) {
            abort(403, 'Usuario no autenticado');
        }
        
        // Debug: Log user information
        \Log::info('User attempting to view consultation', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_name' => $user->name,
            'consulta_id' => $consultaId,
            'consulta_medico_ci' => $consulta->ci_medico
        ]);
        
        // Admin puede ver todas las consultas
        if ($user->role === 'admin') {
            return view('doctor.consulta', compact('consulta'));
        }
        
        // Para médicos y dirmedico, verificar que la consulta pertenezca al médico actual
        $medico = Medico::where('id_usuario', $user->id)->first();
        
        // Debug: Check if medico was found
        if (!$medico) {
            \Log::error('No medico found for user', ['user_id' => $user->id]);
            abort(403, 'No se encontró información del médico para este usuario.');
        }
        
        // Debug: Check CI match
        if ($consulta->ci_medico !== $medico->ci) {
            \Log::error('CI mismatch', [
                'consulta_medico_ci' => $consulta->ci_medico,
                'user_medico_ci' => $medico->ci
            ]);
            abort(403, 'No tiene permiso para acceder a esta consulta.');
        }

        return view('doctor.consulta', compact('consulta'));
    }
}
