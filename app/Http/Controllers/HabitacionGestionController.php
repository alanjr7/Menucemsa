<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\Cama;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HabitacionGestionController extends Controller
{
    private const TIPOS_CAMA = [
        'General' => 'General',
        'UCI' => 'UCI',
        'Pediatría' => 'Pediatría',
        'Maternidad' => 'Maternidad',
        'Quirúrgica' => 'Quirúrgica',
        'Aislada' => 'Aislada',
    ];

    public function __construct()
    {
        $this->middleware('role:internacion|admin|dirmedico|administrador');
    }

    public function index(): View
    {
        $stats = [
            'total_habitaciones' => Habitacion::count(),
            'habitaciones_disponibles' => Habitacion::where('estado', 'disponible')->count(),
            'habitaciones_ocupadas' => Habitacion::where('estado', 'ocupada')->count(),
            'habitaciones_mantenimiento' => Habitacion::where('estado', 'mantenimiento')->count(),
            'total_camas' => Cama::count(),
            'camas_disponibles' => Cama::where('disponibilidad', 'disponible')->count(),
            'camas_ocupadas' => Cama::where('disponibilidad', 'ocupada')->count(),
        ];

        $habitaciones = Habitacion::with(['camas' => function($q) {
                $q->orderBy('nro')->with(['hospitalizacionActiva.paciente']);
            }])
            ->withCount(['camas as camas_disponibles' => fn($q) => $q->where('disponibilidad', 'disponible')])
            ->withCount('camas')
            ->orderBy('id')
            ->get();

        $pacientesSinHabitacion = \App\Models\Hospitalizacion::whereNull('fecha_alta')
            ->whereNull('habitacion_id')
            ->with('paciente')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('ci_paciente')
            ->values();

        return view('internacion-staff.habitaciones.index', compact('stats', 'habitaciones', 'pacientesSinHabitacion'));
    }

    public function create(): View
    {
        return view('internacion-staff.habitaciones.create', ['tiposCama' => self::TIPOS_CAMA]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => 'required|string|max:20|unique:habitaciones,id',
            'detalle' => 'nullable|string|max:120',
            'capacidad' => 'required|integer|min:1|max:10',
            'camas' => 'required|array|min:1',
            'camas.*.nro' => 'required|integer|min:1',
            'camas.*.tipo' => 'required|string|max:80',
            'camas.*.precio_por_dia' => 'required|numeric|min:0',
        ]);

        $this->crearHabitacionConCamas($validated);

        return redirect()->route('internacion-staff.habitaciones.index')
            ->with('success', 'Habitación y camas creadas exitosamente.');
    }

    private function crearHabitacionConCamas(array $data): void
    {
        $habitacion = Habitacion::create([
            'id' => $data['id'],
            'estado' => 'disponible',
            'detalle' => $data['detalle'],
            'capacidad' => $data['capacidad'],
        ]);

        foreach ($data['camas'] as $camaData) {
            Cama::create([
                'nro' => $camaData['nro'],
                'habitacion_id' => $habitacion->id,
                'disponibilidad' => 'disponible',
                'tipo' => $camaData['tipo'],
                'precio_por_dia' => $camaData['precio_por_dia'] ?? 150.00,
            ]);
        }
    }

    public function edit(Habitacion $habitacion): View
    {
        $habitacion->load('camas');

        return view('internacion-staff.habitaciones.edit', [
            'habitacion' => $habitacion,
            'tiposCama' => self::TIPOS_CAMA,
        ]);
    }

    public function update(Request $request, Habitacion $habitacion): RedirectResponse
    {
        $validated = $request->validate([
            'detalle' => 'nullable|string|max:120',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
            'capacidad' => 'required|integer|min:1|max:10',
            'camas' => 'nullable|array',
            'camas.*.id' => 'required|exists:camas,id',
            'camas.*.precio_por_dia' => 'required|numeric|min:0',
        ]);

        $habitacion->update([
            'detalle' => $validated['detalle'],
            'estado' => $validated['estado'],
            'capacidad' => $validated['capacidad'],
        ]);

        $this->actualizarPreciosCamas($habitacion, $validated['camas'] ?? []);

        return redirect()->route('internacion-staff.habitaciones.show', $habitacion)
            ->with('success', 'Habitación actualizada exitosamente.');
    }

    private function actualizarPreciosCamas(Habitacion $habitacion, array $camasData): void
    {
        foreach ($camasData as $camaData) {
            $cama = Cama::find($camaData['id']);
            if ($cama && $cama->habitacion_id === $habitacion->id && $cama->disponibilidad !== 'ocupada') {
                $cama->update(['precio_por_dia' => $camaData['precio_por_dia']]);
            }
        }
    }

    public function destroy(Habitacion $habitacion)
    {
        if (request()->wantsJson()) {
            if ($habitacion->estado === 'mantenimiento') {
                return $this->activarHabitacionAjax($habitacion);
            }
            return $this->marcarMantenimientoAjax($habitacion);
        }

        if ($habitacion->estado === 'mantenimiento') {
            return $this->activarHabitacion($habitacion);
        }

        return $this->marcarMantenimiento($habitacion);
    }

    private function activarHabitacion(Habitacion $habitacion): RedirectResponse
    {
        $tieneCamasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->exists();
        $nuevoEstado = $tieneCamasOcupadas ? 'ocupada' : 'disponible';
        $mensaje = $tieneCamasOcupadas
            ? 'Habitación activada y marcada como ocupada.'
            : 'Habitación activada y marcada como disponible.';

        $habitacion->update(['estado' => $nuevoEstado]);

        return redirect()->route('internacion-staff.habitaciones.index')->with('success', $mensaje);
    }

    private function marcarMantenimiento(Habitacion $habitacion): RedirectResponse
    {
        $camasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->count();

        if ($camasOcupadas > 0) {
            return redirect()->back()
                ->with('error', 'No se puede poner en mantenimiento una habitación con camas ocupadas.');
        }

        $habitacion->update(['estado' => 'mantenimiento']);

        return redirect()->route('internacion-staff.habitaciones.index')
            ->with('success', 'Habitación marcada en mantenimiento.');
    }

    private function activarHabitacionAjax(Habitacion $habitacion): array
    {
        $tieneCamasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->exists();
        $nuevoEstado = $tieneCamasOcupadas ? 'ocupada' : 'disponible';

        $habitacion->update(['estado' => $nuevoEstado]);

        return [
            'success' => true,
            'message' => $tieneCamasOcupadas
                ? 'Habitación activada y marcada como ocupada.'
                : 'Habitación activada y marcada como disponible.'
        ];
    }

    private function marcarMantenimientoAjax(Habitacion $habitacion): array
    {
        $camasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->count();

        if ($camasOcupadas > 0) {
            return [
                'success' => false,
                'error' => 'No se puede poner en mantenimiento una habitación con camas ocupadas.'
            ];
        }

        $habitacion->update(['estado' => 'mantenimiento']);

        return [
            'success' => true,
            'message' => 'Habitación marcada en mantenimiento.'
        ];
    }
}
