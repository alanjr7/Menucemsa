<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\Cama;
use App\Models\Hospitalizacion;
use App\Models\Paciente;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InternacionHabitacionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:internacion|admin|dirmedico');
    }

    /**
     * Listar todas las habitaciones con estadísticas
     */
    public function index()
    {
        $habitaciones = Habitacion::with(['camas' => function($query) {
                $query->orderBy('nro');
            }])
            ->withCount(['camas as camas_disponibles' => function($query) {
                $query->where('disponibilidad', 'disponible');
            }, 'camas as camas_ocupadas' => function($query) {
                $query->where('disponibilidad', 'ocupada');
            }])
            ->orderBy('id')
            ->get();

        // Stats
        $stats = [
            'total_habitaciones' => Habitacion::count(),
            'habitaciones_disponibles' => Habitacion::where('estado', 'disponible')->count(),
            'habitaciones_ocupadas' => Habitacion::where('estado', 'ocupada')->count(),
            'habitaciones_mantenimiento' => Habitacion::where('estado', 'mantenimiento')->count(),
            'total_camas' => Cama::count(),
            'camas_disponibles' => Cama::where('disponibilidad', 'disponible')->count(),
            'camas_ocupadas' => Cama::where('disponibilidad', 'ocupada')->count(),
        ];

        return view('internacion-staff.habitaciones.index', compact('habitaciones', 'stats'));
    }

    /**
     * Formulario para crear nueva habitación
     */
    public function create()
    {
        $tiposCama = [
            'General' => 'General',
            'UCI' => 'UCI',
            'Pediatría' => 'Pediatría',
            'Maternidad' => 'Maternidad',
            'Quirúrgica' => 'Quirúrgica',
            'Aislada' => 'Aislada',
        ];

        return view('internacion-staff.habitaciones.create', compact('tiposCama'));
    }

    /**
     * Guardar nueva habitación con sus camas
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:20|unique:habitaciones,id',
            'detalle' => 'nullable|string|max:120',
            'capacidad' => 'required|integer|min:1|max:10',
            'camas' => 'required|array|min:1',
            'camas.*.nro' => 'required|integer|min:1',
            'camas.*.tipo' => 'required|string|max:80',
            'camas.*.precio_por_dia' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Crear habitación
            $habitacion = Habitacion::create([
                'id' => $request->id,
                'estado' => 'disponible',
                'detalle' => $request->detalle,
                'capacidad' => $request->capacidad,
            ]);

            // Crear camas
            foreach ($request->camas as $camaData) {
                Cama::create([
                    'nro' => $camaData['nro'],
                    'habitacion_id' => $habitacion->id,
                    'disponibilidad' => 'disponible',
                    'tipo' => $camaData['tipo'],
                    'precio_por_dia' => $camaData['precio_por_dia'] ?? 150.00,
                ]);
            }

            DB::commit();

            return redirect()->route('internacion-staff.habitaciones.index')
                ->with('success', 'Habitación y camas creadas exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear la habitación: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalle de habitación con camas y pacientes
     */
    public function show(Habitacion $habitacion)
    {
        $habitacion->load(['camas' => function($query) {
            $query->orderBy('nro')->with(['hospitalizacionActiva.paciente']);
        }]);

        $pacientesSinHabitacion = Hospitalizacion::whereNull('fecha_alta')
            ->whereNull('habitacion_id')
            ->with('paciente')
            ->get();

        return view('internacion-staff.habitaciones.show', compact('habitacion', 'pacientesSinHabitacion'));
    }

    /**
     * Formulario para editar habitación
     */
    public function edit(Habitacion $habitacion)
    {
        $habitacion->load('camas');
        $tiposCama = [
            'General' => 'General',
            'UCI' => 'UCI',
            'Pediatría' => 'Pediatría',
            'Maternidad' => 'Maternidad',
            'Quirúrgica' => 'Quirúrgica',
            'Aislada' => 'Aislada',
        ];

        return view('internacion-staff.habitaciones.edit', compact('habitacion', 'tiposCama'));
    }

    /**
     * Actualizar habitación
     */
    public function update(Request $request, Habitacion $habitacion)
    {
        $request->validate([
            'detalle' => 'nullable|string|max:120',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
            'capacidad' => 'required|integer|min:1|max:10',
            'camas' => 'nullable|array',
            'camas.*.id' => 'required|exists:camas,id',
            'camas.*.precio_por_dia' => 'required|numeric|min:0',
        ]);

        $habitacion->update([
            'detalle' => $request->detalle,
            'estado' => $request->estado,
            'capacidad' => $request->capacidad,
        ]);

        // Actualizar precios de camas
        if ($request->has('camas')) {
            foreach ($request->camas as $camaData) {
                $cama = Cama::find($camaData['id']);
                if ($cama && $cama->habitacion_id === $habitacion->id) {
                    // Solo actualizar si la cama no está ocupada
                    if ($cama->disponibilidad !== 'ocupada') {
                        $cama->update([
                            'precio_por_dia' => $camaData['precio_por_dia'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('internacion-staff.habitaciones.show', $habitacion)
            ->with('success', 'Habitación y precios de camas actualizados exitosamente.');
    }

    /**
     * Toggle mantenimiento de habitación (marcar/desmarcar)
     */
    public function destroy(Habitacion $habitacion)
    {
        // Si está en mantenimiento, activarla (toggle)
        if ($habitacion->estado === 'mantenimiento') {
            $camasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->count();

            if ($camasOcupadas > 0) {
                $habitacion->update(['estado' => 'ocupada']);
                return redirect()->route('internacion-staff.habitaciones.index')
                    ->with('success', 'Habitación activada y marcada como ocupada (tiene camas en uso).');
            } else {
                $habitacion->update(['estado' => 'disponible']);
                return redirect()->route('internacion-staff.habitaciones.index')
                    ->with('success', 'Habitación activada y marcada como disponible.');
            }
        }

        // Si está activa (disponible u ocupada), marcar como mantenimiento
        // Verificar si tiene camas ocupadas
        $camasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->count();

        if ($camasOcupadas > 0) {
            return redirect()->back()
                ->with('error', 'No se puede poner en mantenimiento una habitación con camas ocupadas.');
        }

        $habitacion->update(['estado' => 'mantenimiento']);

        return redirect()->route('internacion-staff.habitaciones.index')
            ->with('success', 'Habitación marcada en mantenimiento. Presione nuevamente para activarla.');
    }

    /**
     * Asignar paciente a una cama
     */
    public function asignarPaciente(Request $request, Habitacion $habitacion)
    {
        $request->validate([
            'cama_id' => 'required|exists:camas,id',
            'hospitalizacion_id' => 'required|exists:hospitalizaciones,id',
        ]);

        try {
            DB::beginTransaction();

            $cama = Cama::findOrFail($request->cama_id);

            // Verificar que la cama esté disponible
            if (!$cama->estaDisponible()) {
                return redirect()->back()
                    ->with('error', 'La cama seleccionada no está disponible.');
            }

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($request->hospitalizacion_id);

            // 1. Obtener o crear cuenta de cobro del paciente
            $pacienteCi = $hospitalizacion->ci_paciente;

            // Para pacientes temporales (ci_paciente es null), buscar por referencia_id
            $searchCriteria = $pacienteCi
                ? ['paciente_ci' => $pacienteCi, 'estado' => 'pendiente']
                : ['referencia_id' => $hospitalizacion->id, 'referencia_type' => Hospitalizacion::class, 'estado' => 'pendiente'];

            $cuentaCobro = CuentaCobro::firstOrCreate(
                $searchCriteria,
                [
                    'paciente_ci' => $pacienteCi,
                    'tipo_atencion' => 'internacion',
                    'referencia_id' => $hospitalizacion->id,
                    'referencia_type' => Hospitalizacion::class,
                    'total_calculado' => 0,
                    'total_pagado' => 0,
                ]
            );

            // 2. Actualizar hospitalización con habitación, cama y precio
            $hospitalizacion->update([
                'habitacion_id' => $habitacion->id,
                'cama_id' => $cama->id,
                'precio_cama_dia' => $cama->precio_por_dia,
            ]);

            // 3. Crear detalle inicial en cuenta de cobro (se actualizará al liberar)
            $detalle = CuentaCobroDetalle::create([
                'cuenta_cobro_id' => $cuentaCobro->id,
                'tipo_item' => 'estadia',
                'descripcion' => "Estancia Internación - Habitación {$habitacion->id}, Cama {$cama->nro} (En progreso)",
                'cantidad' => 1, // Se actualizará al liberar
                'precio_unitario' => $cama->precio_por_dia,
                'subtotal' => $cama->precio_por_dia, // Temporal, se recalculará
                'origen_type' => Hospitalizacion::class,
                'origen_id' => $hospitalizacion->id,
            ]);

            // 4. Guardar referencia al detalle en hospitalización
            $hospitalizacion->update([
                'cuenta_cobro_detalle_id' => $detalle->id,
            ]);

            // 5. Actualizar cama a ocupada
            $cama->update(['disponibilidad' => 'ocupada']);

            // 6. Si habitación estaba disponible, cambiar a ocupada
            if ($habitacion->estado === 'disponible') {
                $habitacion->update(['estado' => 'ocupada']);
            }

            DB::commit();

            return redirect()->route('internacion-staff.habitaciones.show', $habitacion)
                ->with('success', 'Paciente asignado a la cama. Estancia registrada en cuenta de cobro automáticamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al asignar paciente: ' . $e->getMessage());
        }
    }

    /**
     * Liberar cama (dar de alta paciente o mover)
     */
    public function liberarCama(Request $request, Cama $cama)
    {
        try {
            DB::beginTransaction();

            // Buscar hospitalización activa en esta cama
            $hospitalizacion = Hospitalizacion::where('cama_id', $cama->id)
                ->whereNull('fecha_alta')
                ->first();

            if ($hospitalizacion) {
                // 1. Calcular días y costo final de estancia
                $dias = $hospitalizacion->getDiasEstancia();
                $costoTotal = $hospitalizacion->getCostoEstancia();

                // 2. Actualizar detalle de cuenta de cobro si existe
                if ($hospitalizacion->cuenta_cobro_detalle_id) {
                    $detalle = CuentaCobroDetalle::find($hospitalizacion->cuenta_cobro_detalle_id);
                    if ($detalle) {
                        $detalle->update([
                            'cantidad' => $dias,
                            'subtotal' => $costoTotal,
                            'descripcion' => "Estancia Internación - {$dias} días (Hab. {$hospitalizacion->habitacion_id}, Cama {$cama->nro})",
                        ]);

                        // 3. Recalcular total de cuenta de cobro
                        $cuenta = $detalle->cuentaCobro;
                        if ($cuenta) {
                            $cuenta->total_calculado = $cuenta->detalles->sum('subtotal');
                            $cuenta->save();
                        }
                    }
                }

                // 4. Guardar totales en hospitalización
                $hospitalizacion->update([
                    'total_estancia' => $costoTotal,
                    'fecha_alta' => now(),
                    'habitacion_id' => null,
                    'cama_id' => null,
                ]);
            }

            // 5. Liberar cama
            $cama->update(['disponibilidad' => 'disponible']);

            // 6. Verificar si la habitación quedó vacía
            $habitacion = $cama->habitacion;
            $camasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->count();

            if ($camasOcupadas === 0 && $habitacion->estado === 'ocupada') {
                $habitacion->update(['estado' => 'disponible']);
            }

            DB::commit();

            $mensaje = $hospitalizacion
                ? "Cama liberada. Estancia calculada: {$hospitalizacion->getDiasEstancia()} días. Total: Bs. " . number_format($hospitalizacion->total_estancia, 2)
                : 'Cama liberada exitosamente.';

            return redirect()->back()
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al liberar cama: ' . $e->getMessage());
        }
    }
}
