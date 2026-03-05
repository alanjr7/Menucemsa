<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Especialidad;
use App\Models\Receta;
use Illuminate\Support\Facades\Auth;

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
        if (!in_array($user->role, ['dirmedico', 'admin'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Acceso denegado. Esta sección es solo para personal médico.');
        }
        
        // Obtener el médico asociado al usuario actual
        $medico = Medico::where('id_usuario', $user->id)->first();
        
        if (!$medico) {
            // For admin users, show a different message
            if ($user->role === 'admin') {
                return redirect()->route('dashboard')
                    ->with('error', 'Los administradores no tienen acceso a consultas médicas. Por favor use una cuenta de médico (doctor@menucemsa.com / password).');
            }
            
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
            ->where('fecha', today())
            ->whereHas('caja', function($query) {
                $query->whereNotNull('nro_factura'); // Solo consultas pagadas
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
