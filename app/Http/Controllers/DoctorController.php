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
        
        // Para administradores, mostrar vista de control total
        if ($user->role === 'admin') {
            return $this->vistaControlTotal();
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

        return view('medical.consulta-externa', compact(
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
        $consulta = Consulta::findOrFail($consultaId);
        
        // Verificar que la consulta pertenezca al médico actual
        $user = Auth::user();
        $medico = Medico::where('id_usuario', $user->id)->first();
        
        if ($consulta->ci_medico !== $medico->ci) {
            abort(403, 'No tiene permiso para acceder a esta consulta.');
        }

        // Actualizar estado a "en atención"
        $consulta->estado = 'en_atencion';
        $consulta->save();

        return response()->json([
            'success' => true,
            'message' => 'Consulta iniciada correctamente'
        ]);
    }

    public function completarConsulta($consultaId, Request $request)
    {
        $consulta = Consulta::findOrFail($consultaId);
        
        // Verificar que la consulta pertenezca al médico actual
        $user = Auth::user();
        $medico = Medico::where('id_usuario', $user->id)->first();
        
        if ($consulta->ci_medico !== $medico->ci) {
            abort(403, 'No tiene permiso para acceder a esta consulta.');
        }

        // Actualizar estado a "completada"
        $consulta->estado = 'completada';
        $consulta->observaciones = $request->observaciones ?? $consulta->observaciones;
        $consulta->save();

        // Crear receta si se proporcionaron medicamentos
        if ($request->has('medicamentos') && !empty($request->medicamentos)) {
            $this->crearReceta($consulta, $request);
        }

        return response()->json([
            'success' => true,
            'message' => 'Consulta completada correctamente'
        ]);
    }

    private function crearReceta($consulta, $request)
    {
        $receta = Receta::create([
            'nro_consulta' => $consulta->nro,
            'fecha' => now(),
            'indicaciones' => $request->indicaciones ?? '',
        ]);

        // Aquí se podrían agregar los detalles de los medicamentos si existe la tabla detalle_receta
        // Por ahora solo creamos la receta básica
    }

    public function verHistorialMedico($ci_medico = null)
    {
        $user = Auth::user();
        
        // Solo administradores pueden ver historial de otros médicos
        if ($user->role !== 'admin') {
            abort(403, 'Acceso denegado');
        }
        
        // Si no se especifica médico, mostrar lista para seleccionar
        if (!$ci_medico) {
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
        
        // Solo administradores pueden ver pacientes de otros médicos
        if ($user->role !== 'admin') {
            abort(403, 'Acceso denegado');
        }
        
        // Si no se especifica médico, mostrar lista para seleccionar
        if (!$ci_medico) {
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

    public function verConsulta($consultaId)
    {
        $consulta = Consulta::with([
            'paciente',
            'medico.usuario',
            'especialidad',
            'caja',
            'recetas'
        ])->findOrFail($consultaId);

        // Verificar que la consulta pertenezca al médico actual
        $user = Auth::user();
        $medico = Medico::where('id_usuario', $user->id)->first();
        
        if ($consulta->ci_medico !== $medico->ci) {
            abort(403, 'No tiene permiso para acceder a esta consulta.');
        }

        return view('doctor.consulta', compact('consulta'));
    }
}
