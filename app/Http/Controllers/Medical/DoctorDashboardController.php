<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DoctorDashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Obtener el médico autenticado
        $user = auth()->user();
        $medico = Medico::where('id_usuario', $user->id)->first();
        
        if (!$medico) {
            abort(403, 'No se encontró información del médico');
        }

        // Obtener pacientes asignados al médico (pagados y pendientes de atención)
        $pacientesAsignados = Consulta::with(['paciente'])
            ->where('ci_medico', $medico->ci)
            ->where('estado_pago', true) // Solo pagados
            ->where('estado', 'pendiente') // Pendientes de atención
            ->orderBy('created_at', 'asc') // Orden de llegada
            ->get();

        // Estadísticas del médico
        $stats = [
            'pendientes' => $pacientesAsignados->count(),
            'atendidos_hoy' => Consulta::where('ci_medico', $medico->ci)
                ->whereDate('created_at', today())
                ->where('estado', '!=', 'pendiente')
                ->count(),
            'total_pacientes' => Consulta::where('ci_medico', $medico->ci)->count()
        ];

        return view('medical.doctor-dashboard', compact('pacientesAsignados', 'medico', 'stats'));
    }

    public function atenderPaciente(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'consulta_nro' => 'required|string'
            ]);

            // Obtener el médico autenticado
            $user = auth()->user();
            $medico = Medico::where('id_usuario', $user->id)->first();
            
            if (!$medico) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del médico'
                ], 403);
            }

            // Obtener la consulta
            $consulta = Consulta::where('nro', $request->consulta_nro)
                ->where('ci_medico', $medico->ci)
                ->first();

            if (!$consulta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consulta no encontrada o no asignada a este médico'
                ], 404);
            }

            // Actualizar estado a "en atención"
            $consulta->update([
                'estado' => 'en_atencion',
                'hora_inicio_atencion' => now()->toTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paciente en atención',
                'redirect_url' => '/medico/dashboard'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al atender paciente: ' . $e->getMessage()
            ], 500);
        }
    }
}
