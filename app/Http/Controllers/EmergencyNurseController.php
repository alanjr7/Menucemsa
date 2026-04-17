<?php

namespace App\Http\Controllers;

use App\Models\Enfermera;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class EmergencyNurseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:emergencia|admin');
    }

    /**
     * Listar enfermeras de emergencia
     */
    public function index(): View
    {
        $enfermeras = Enfermera::emergencia()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('emergency-staff.enfermeras.index', compact('enfermeras'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): View
    {
        return view('emergency-staff.enfermeras.create');
    }

    /**
     * Guardar nueva enfermera
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::defaults()],
                'ci' => 'required|string|unique:enfermeras,ci',
                'telefono' => 'nullable|string|max:20',
            ]);

            DB::beginTransaction();

            // Crear usuario con rol enfermera-emergencia
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'enfermera-emergencia',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Crear registro de enfermera
            $enfermera = Enfermera::create([
                'user_id' => $user->id,
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'] ?? null,
                'tipo' => 'Enfermera de Emergencia',
                'estado' => 'activo',
                'area' => 'emergencia',
            ]);

            // Registrar en auditoría
            \App\Services\ActivityLogService::log(
                'crear_enfermera',
                'Enfermera de emergencia creada: ' . $user->name,
                $enfermera
            );

            DB::commit();

            return redirect()->route('emergency-staff.enfermeras.index')
                ->with('success', 'Enfermera de emergencia creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear la enfermera: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalle de enfermera
     */
    public function show(Enfermera $enfermera): View
    {
        $enfermera->load('user');
        
        // Obtener actividad reciente
        $actividad = ActivityLog::where('user_id', $enfermera->user_id)
            ->where('model_type', 'App\Models\Emergency')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('emergency-staff.enfermeras.show', compact('enfermera', 'actividad'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Enfermera $enfermera): View
    {
        $enfermera->load('user');
        return view('emergency-staff.enfermeras.edit', compact('enfermera'));
    }

    /**
     * Actualizar enfermera
     */
    public function update(Request $request, Enfermera $enfermera): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $enfermera->user_id,
                'ci' => 'required|string|unique:enfermeras,ci,' . $enfermera->user_id . ',user_id',
                'telefono' => 'nullable|string|max:20',
                'tipo' => 'nullable|string|max:80',
            ]);

            DB::beginTransaction();

            $user = $enfermera->user;
            $oldValues = [
                'name' => $user->name,
                'email' => $user->email,
                'ci' => $enfermera->ci,
                'telefono' => $enfermera->telefono,
            ];

            // Actualizar usuario
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Actualizar enfermera
            $enfermera->update([
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'] ?? null,
                'tipo' => $validated['tipo'] ?? $enfermera->tipo,
            ]);

            $newValues = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'] ?? null,
            ];

            // Registrar en auditoría
            \App\Services\ActivityLogService::log(
                'actualizar_enfermera',
                'Enfermera de emergencia actualizada: ' . $user->name,
                $enfermera,
                $oldValues,
                $newValues
            );

            DB::commit();

            return redirect()->route('emergency-staff.enfermeras.index')
                ->with('success', 'Enfermera actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la enfermera: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar enfermera
     */
    public function destroy(Enfermera $enfermera): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $userName = $enfermera->user?->name ?? 'Desconocida';

            // Registrar en auditoría antes de eliminar
            \App\Services\ActivityLogService::log(
                'eliminar_enfermera',
                'Enfermera de emergencia eliminada: ' . $userName,
                $enfermera
            );

            // Eliminar enfermera (el usuario se mantiene o se elimina según necesidad)
            $enfermera->delete();
            
            // Desactivar el usuario asociado
            if ($enfermera->user) {
                $enfermera->user->update(['is_active' => false]);
            }

            DB::commit();

            return redirect()->route('emergency-staff.enfermeras.index')
                ->with('success', 'Enfermera eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la enfermera: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleStatus(Enfermera $enfermera): JsonResponse
    {
        try {
            $nuevoEstado = $enfermera->estado === 'activo' ? 'inactivo' : 'activo';
            $enfermera->update(['estado' => $nuevoEstado]);

            // Actualizar estado del usuario
            if ($enfermera->user) {
                $enfermera->user->update(['is_active' => $nuevoEstado === 'activo']);
            }

            // Registrar en auditoría
            \App\Services\ActivityLogService::log(
                'cambiar_estado_enfermera',
                'Estado de enfermera cambiado: ' . ($enfermera->user?->name ?? 'Desconocida') . ' → ' . $nuevoEstado,
                $enfermera
            );

            return response()->json([
                'success' => true,
                'estado' => $nuevoEstado,
                'message' => 'Estado actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver actividad de una enfermera
     */
    public function actividad(Enfermera $enfermera): View
    {
        $enfermera->load('user');
        
        $actividad = ActivityLog::where('user_id', $enfermera->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('emergency-staff.enfermeras.actividad', compact('enfermera', 'actividad'));
    }

    /**
     * Panel de auditoría general
     */
    public function auditoria(Request $request): View
    {
        $enfermeras = Enfermera::emergencia()->with('user')->get();
        
        $query = ActivityLog::whereIn('user_id', function($q) {
                $q->select('user_id')
                  ->from('enfermeras')
                  ->where('area', 'emergencia');
            })
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('enfermera_id')) {
            $query->where('user_id', $request->enfermera_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $actividades = $query->paginate(30)->withQueryString();
        
        // Obtener tipos de acciones únicos para el filtro
        $tiposAcciones = ActivityLog::whereIn('user_id', function($q) {
                $q->select('user_id')
                  ->from('enfermeras')
                  ->where('area', 'emergencia');
            })
            ->distinct()
            ->pluck('action');

        return view('emergency-staff.auditoria', compact(
            'actividades', 
            'enfermeras', 
            'tiposAcciones'
        ));
    }

    /**
     * API: Obtener datos de auditoría (para AJAX)
     */
    public function apiAuditoria(Request $request): JsonResponse
    {
        $query = ActivityLog::whereIn('user_id', function($q) {
                $q->select('user_id')
                  ->from('enfermeras')
                  ->where('area', 'emergencia');
            })
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('enfermera_id')) {
            $query->where('user_id', $request->enfermera_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $actividades = $query->limit(100)->get()->map(function($log) {
            return [
                'id' => $log->id,
                'fecha' => $log->created_at->format('d/m/Y H:i'),
                'usuario' => $log->user?->name ?? 'Desconocido',
                'accion' => $log->action,
                'descripcion' => $log->description,
                'model_type' => $log->model_type,
                'model_id' => $log->model_id,
            ];
        });

        return response()->json([
            'success' => true,
            'actividades' => $actividades,
            'total' => $actividades->count(),
        ]);
    }
}
