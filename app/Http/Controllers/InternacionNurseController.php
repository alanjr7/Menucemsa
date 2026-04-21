<?php

namespace App\Http\Controllers;

use App\Models\Enfermera;
use App\Models\EnfermeraPermission;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class InternacionNurseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:internacion|admin');
    }

    /**
     * Listar enfermeras de internación
     */
    public function index(): View
    {
        $enfermeras = Enfermera::internacion()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('internacion-staff.enfermeras.index', compact('enfermeras'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): View
    {
        return view('internacion-staff.enfermeras.create');
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
                'password' => ['required', 'confirmed', 'min:8'],
                'ci' => 'required|integer|unique:enfermeras,ci',
                'telefono' => 'nullable|string|max:20',
                'turno' => 'required|in:mañana,tarde,noche',
            ]);

            DB::beginTransaction();

            // Crear usuario con rol enfermera-internacion
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'enfermera-internacion',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Crear registro de enfermera
            $enfermera = Enfermera::create([
                'user_id' => $user->id,
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'] ?? null,
                'tipo' => 'Enfermera de Internación',
                'estado' => 'activo',
                'area' => 'internacion',
                'turno' => $validated['turno'],
            ]);

            // Asignar permisos por defecto para internación
            $grantedBy = auth()->check() ? auth()->id() : null;
            $enfermera->assignDefaultPermissions($grantedBy);

            // Registrar en auditoría
            \App\Services\ActivityLogService::log(
                'crear_enfermera_internacion',
                'Enfermera de internación creada: ' . $user->name,
                $enfermera
            );

            DB::commit();

            return redirect()->route('internacion-staff.enfermeras.index')
                ->with('success', 'Enfermera de internación creada exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            \Log::error('Error al crear enfermera internacion: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
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
            ->whereIn('model_type', ['App\Models\Hospitalizacion', 'App\Models\HospMedicamentoAdministrado', 'App\Models\HospCatering', 'App\Models\HospDrenaje'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('internacion-staff.enfermeras.show', compact('enfermera', 'actividad'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Enfermera $enfermera): View
    {
        $enfermera->load('user');
        return view('internacion-staff.enfermeras.edit', compact('enfermera'));
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
                'ci' => 'required|integer|unique:enfermeras,ci,' . $enfermera->user_id . ',user_id',
                'telefono' => 'nullable|string|max:20',
                'tipo' => 'nullable|string|max:80',
                'turno' => 'required|in:mañana,tarde,noche',
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
                'turno' => $validated['turno'],
            ]);

            $newValues = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'] ?? null,
            ];

            // Registrar en auditoría
            \App\Services\ActivityLogService::log(
                'actualizar_enfermera_internacion',
                'Enfermera de internación actualizada: ' . $user->name,
                $enfermera,
                $oldValues,
                $newValues
            );

            DB::commit();

            return redirect()->route('internacion-staff.enfermeras.index')
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
                'eliminar_enfermera_internacion',
                'Enfermera de internación eliminada: ' . $userName,
                $enfermera
            );

            // Eliminar enfermera
            $enfermera->delete();
            
            // Desactivar el usuario asociado
            if ($enfermera->user) {
                $enfermera->user->update(['is_active' => false]);
            }

            DB::commit();

            return redirect()->route('internacion-staff.enfermeras.index')
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
                'cambiar_estado_enfermera_internacion',
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

        return view('internacion-staff.enfermeras.actividad', compact('enfermera', 'actividad'));
    }

    /**
     * Mostrar vista de gestión de permisos
     */
    public function permissions(Enfermera $enfermera): View
    {
        $enfermera->load('user');

        // Obtener permisos actuales de la enfermera
        $currentPermissions = $enfermera->getPermissionKeys();

        // Obtener todos los permisos disponibles
        $availablePermissions = EnfermeraPermission::AVAILABLE_PERMISSIONS;

        return view('internacion-staff.enfermeras.permissions', compact(
            'enfermera',
            'currentPermissions',
            'availablePermissions'
        ));
    }

    /**
     * Actualizar permisos de la enfermera
     */
    public function updatePermissions(Request $request, Enfermera $enfermera): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|in:' . implode(',', array_keys(EnfermeraPermission::AVAILABLE_PERMISSIONS)),
            ]);

            DB::beginTransaction();

            // Guardar permisos anteriores para auditoría
            $permisosAnteriores = $enfermera->getPermissionKeys();

            // Eliminar permisos actuales
            $enfermera->permissions()->delete();

            // Crear nuevos permisos
            $permissions = $validated['permissions'] ?? [];
            foreach ($permissions as $permissionKey) {
                $enfermera->permissions()->create([
                    'permission_key' => $permissionKey,
                    'granted_by' => auth()->id(),
                ]);
            }

            // Registrar en auditoría
            \App\Services\ActivityLogService::log(
                'actualizar_permisos_enfermera_internacion',
                'Permisos actualizados para: ' . ($enfermera->user?->name ?? 'Enfermera'),
                $enfermera,
                ['permisos_anteriores' => $permisosAnteriores],
                ['permisos_nuevos' => $permissions]
            );

            DB::commit();

            return redirect()->route('internacion-staff.enfermeras.index')
                ->with('success', 'Permisos actualizados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar permisos: ' . $e->getMessage())
                ->withInput();
        }
    }
}
