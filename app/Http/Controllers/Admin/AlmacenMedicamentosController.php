<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlmacenMedicamento;
use App\Models\DispensacionAlmacen;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AlmacenMedicamentosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|administrador');
    }

    public function index(Request $request)
    {
        $query = AlmacenMedicamento::query();

        // Filtros
        if ($request->filled('area')) {
            $query->porArea($request->area);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('lote', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('estado_stock')) {
            if ($request->estado_stock === 'bajo') {
                $query->bajoStock();
            } elseif ($request->estado_stock === 'agotado') {
                $query->where('cantidad', 0);
            }
        }

        if ($request->filled('estado_vencimiento')) {
            if ($request->estado_vencimiento === 'vencido') {
                $query->vencidos();
            } elseif ($request->estado_vencimiento === 'por_vencer') {
                $query->porVencer();
            }
        }

        $almacenMedicamentos = $query->activos()->orderBy('nombre')->paginate(20);

        // Estadísticas
        $stats = [
            'total' => AlmacenMedicamento::activos()->count(),
            'medicamentos' => AlmacenMedicamento::activos()->medicamentos()->count(),
            'insumos' => AlmacenMedicamento::activos()->insumos()->count(),
            'bajo_stock' => AlmacenMedicamento::activos()->bajoStock()->count(),
            'agotados' => AlmacenMedicamento::activos()->where('cantidad', 0)->count(),
            'vencidos' => AlmacenMedicamento::activos()->vencidos()->count(),
            'por_vencer' => AlmacenMedicamento::activos()->porVencer()->count(),
            'total_central' => AlmacenMedicamento::activos()->central()->count(),
            'por_dispensar' => AlmacenMedicamento::activos()->central()->bajoStock()->count(),
        ];

        $areas = ['emergencia', 'cirugia', 'internacion', 'uti', 'usi', 'neonato', 'central'];

        return view('admin.almacen-medicamentos.index', compact('almacenMedicamentos', 'stats', 'areas'));
    }

    public function create()
    {
        $areas = ['emergencia' => 'Emergencia', 'cirugia' => 'Cirugía', 'internacion' => 'Internación', 'uti' => 'UTI', 'usi' => 'USI', 'neonato' => 'Neonato', 'central' => 'Almacén Central'];
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades', 'ml', 'mg', 'gr', 'cm', 'cajas', 'frascos', 'sobres'];

        return view('admin.almacen-medicamentos.create', compact('areas', 'tipos', 'unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'area' => 'required|in:emergencia,cirugia,internacion,uti,usi,neonato,central',
            'precio' => 'nullable|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'porcentaje_ganancia' => 'nullable|numeric|min:0|max:999',
            'precio_venta' => 'nullable|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date|after:today',
            'lote' => 'nullable|string|max:100',
            'cantidad' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'tipo' => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
        ]);

        $medicamento = AlmacenMedicamento::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'area' => $request->area,
            'precio' => $request->precio,
            'precio_compra' => $request->precio_compra,
            'porcentaje_ganancia' => $request->porcentaje_ganancia,
            'precio_venta' => $request->precio_venta,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'lote' => $request->lote,
            'cantidad' => $request->cantidad,
            'stock_minimo' => $request->stock_minimo,
            'unidad_medida' => $request->unidad_medida,
            'tipo' => $request->tipo,
            'observaciones' => $request->observaciones,
        ]);

        // Registrar actividad en log
        Log::info('Usuario ' . Auth::user()->name . ' creó nuevo medicamento/insumo en almacén: ' . $medicamento->nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'create',
            'module' => 'almacen_medicamentos'
        ]);

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo agregado correctamente al almacén.');
    }

    public function show(AlmacenMedicamento $almacenMedicamento)
    {
        return view('admin.almacen-medicamentos.show', compact('almacenMedicamento'));
    }

    public function edit(AlmacenMedicamento $almacenMedicamento)
    {
        $areas = ['emergencia' => 'Emergencia', 'cirugia' => 'Cirugía', 'internacion' => 'Internación', 'uti' => 'UTI', 'usi' => 'USI', 'neonato' => 'Neonato', 'central' => 'Almacén Central'];
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades', 'ml', 'mg', 'gr', 'cm', 'cajas', 'frascos', 'sobres'];

        return view('admin.almacen-medicamentos.edit', compact('almacenMedicamento', 'areas', 'tipos', 'unidades'));
    }

    public function update(Request $request, AlmacenMedicamento $almacenMedicamento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'area' => 'required|in:emergencia,cirugia,internacion,uti,usi,neonato,central',
            'precio' => 'nullable|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'porcentaje_ganancia' => 'nullable|numeric|min:0|max:999',
            'precio_venta' => 'nullable|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'lote' => 'nullable|string|max:100',
            'cantidad' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'tipo' => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
        ]);

        $almacenMedicamento->update($request->only([
            'nombre', 'descripcion', 'area', 'precio', 'precio_compra',
            'porcentaje_ganancia', 'precio_venta', 'fecha_vencimiento',
            'lote', 'cantidad', 'stock_minimo', 'unidad_medida', 'tipo', 'observaciones',
        ]));

        // Registrar actividad en log
        Log::info('Usuario ' . Auth::user()->name . ' actualizó medicamento/insumo en almacén: ' . $almacenMedicamento->nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $almacenMedicamento->id,
            'action' => 'update',
            'module' => 'almacen_medicamentos'
        ]);

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo actualizado correctamente.');
    }

    public function destroy(AlmacenMedicamento $almacenMedicamento)
    {
        $nombre = $almacenMedicamento->nombre;
        $almacenMedicamento->update(['activo' => false]);

        // Registrar actividad en log
        Log::info('Usuario ' . Auth::user()->name . ' desactivó medicamento/insumo en almacén: ' . $nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $almacenMedicamento->id,
            'action' => 'deactivate',
            'module' => 'almacen_medicamentos'
        ]);

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo desactivado correctamente.');
    }

    public function actualizarStock(Request $request, AlmacenMedicamento $almacenMedicamento)
    {
        $request->validate([
            'cantidad' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        $cantidadAnterior = $almacenMedicamento->cantidad;
        $almacenMedicamento->update(['cantidad' => $request->cantidad]);

        // Registrar actividad en log
        Log::info('Usuario ' . Auth::user()->name . ' actualizó stock de ' . $almacenMedicamento->nombre . ': ' . $cantidadAnterior . ' → ' . $request->cantidad . '. Motivo: ' . $request->motivo, [
            'user_id' => Auth::id(),
            'medicamento_id' => $almacenMedicamento->id,
            'action' => 'update_stock',
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $request->cantidad,
            'motivo' => $request->motivo,
            'module' => 'almacen_medicamentos'
        ]);

        return redirect()->back()
            ->with('success', 'Stock actualizado correctamente.');
    }

    public function reporteBajoStock()
    {
        $medicamentos = AlmacenMedicamento::activos()
            ->bajoStock()
            ->orderBy('cantidad')
            ->get();

        return view('admin.almacen-medicamentos.reporte-bajo-stock', compact('medicamentos'));
    }

    public function reporteVencimiento()
    {
        $vencidos = AlmacenMedicamento::activos()
            ->vencidos()
            ->orderBy('fecha_vencimiento')
            ->get();

        $porVencer = AlmacenMedicamento::activos()
            ->porVencer()
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('admin.almacen-medicamentos.reporte-vencimiento', compact('vencidos', 'porVencer'));
    }

    public function porArea($area)
    {
        $medicamentos = AlmacenMedicamento::activos()
            ->porArea($area)
            ->orderBy('nombre')
            ->paginate(20);

        $stats = [
            'total' => $medicamentos->total(),
            'medicamentos' => $medicamentos->where('tipo', 'medicamento')->count(),
            'insumos' => $medicamentos->where('tipo', 'insumo')->count(),
            'bajo_stock' => $medicamentos->filter(fn($m) => $m->estaBajoStock())->count(),
            'agotados' => $medicamentos->where('cantidad', 0)->count(),
        ];

        return view('admin.almacen-medicamentos.por-area', compact('medicamentos', 'area', 'stats'));
    }

    public function dispensar(Request $request, AlmacenMedicamento $almacenMedicamento)
    {
        if ($almacenMedicamento->area !== 'central') {
            abort(403, 'Solo se pueden dispensar ítems del almacén central.');
        }

        $request->validate([
            'cantidad' => 'required|integer|min:1|max:' . $almacenMedicamento->cantidad,
            'area_destino' => 'required|in:emergencia,cirugia,hospitalizacion,uti,usi,neonato,internacion',
            'recibido_por' => 'nullable|string|max:150',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        if ($request->cantidad > $almacenMedicamento->cantidad) {
            return redirect()->back()
                ->with('error', 'La cantidad solicitada excede el stock disponible (' . $almacenMedicamento->cantidad . ' ' . $almacenMedicamento->unidad_medida . ').');
        }

        DB::transaction(function () use ($request, $almacenMedicamento) {
            // 1. Descontar del central
            $almacenMedicamento->decrement('cantidad', $request->cantidad);

            // 2. Incrementar (o crear) en área destino
            $destino = AlmacenMedicamento::where('nombre', $almacenMedicamento->nombre)
                ->where('area', $request->area_destino)
                ->first();

            if ($destino) {
                $destino->increment('cantidad', $request->cantidad);
            } else {
                AlmacenMedicamento::create([
                    'nombre'             => $almacenMedicamento->nombre,
                    'descripcion'        => $almacenMedicamento->descripcion,
                    'area'               => $request->area_destino,
                    'precio'             => $almacenMedicamento->precio,
                    'precio_compra'      => $almacenMedicamento->precio_compra,
                    'porcentaje_ganancia'=> $almacenMedicamento->porcentaje_ganancia,
                    'precio_venta'       => $almacenMedicamento->precio_venta,
                    'fecha_vencimiento'  => $almacenMedicamento->fecha_vencimiento,
                    'lote'               => $almacenMedicamento->lote,
                    'cantidad'           => $request->cantidad,
                    'stock_minimo'       => $almacenMedicamento->stock_minimo,
                    'unidad_medida'      => $almacenMedicamento->unidad_medida,
                    'tipo'               => $almacenMedicamento->tipo,
                    'observaciones'      => 'Transferido desde almacén central.',
                ]);
            }

            // 3. Registrar dispensación
            DispensacionAlmacen::create([
                'almacen_medicamento_id' => $almacenMedicamento->id,
                'cantidad' => $request->cantidad,
                'area_destino' => $request->area_destino,
                'dispensado_por' => Auth::id(),
                'recibido_por' => $request->recibido_por,
                'observaciones' => $request->observaciones,
                'fecha_dispensacion' => now(),
            ]);

            Log::info('Usuario ' . Auth::user()->name . ' dispensó ' . $request->cantidad . ' ' . $almacenMedicamento->unidad_medida . ' de ' . $almacenMedicamento->nombre . ' al área ' . $request->area_destino, [
                'user_id' => Auth::id(),
                'medicamento_id' => $almacenMedicamento->id,
                'cantidad' => $request->cantidad,
                'area_destino' => $request->area_destino,
                'action' => 'dispensar',
                'module' => 'almacen_medicamentos'
            ]);
        });

        $mensaje = 'Dispensación registrada correctamente.';
        if ($almacenMedicamento->cantidad - $request->cantidad <= $almacenMedicamento->stock_minimo) {
            $mensaje .= ' Atención: el stock quedó por debajo del mínimo.';
        }

        return redirect()->back()->with('success', $mensaje);
    }

    public function historialDispensaciones(Request $request)
    {
        $query = DispensacionAlmacen::with(['medicamento', 'dispensadoPor']);

        if ($request->filled('area_destino')) {
            $query->porArea($request->area_destino);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_dispensacion', '>=', $request->fecha_desde . ' 00:00:00');
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_dispensacion', '<=', $request->fecha_hasta . ' 23:59:59');
        }

        if ($request->filled('buscar')) {
            $query->whereHas('medicamento', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        $dispensaciones = $query->recientes()->paginate(20);

        $stats = [
            'total' => DispensacionAlmacen::count(),
            'ultimos_30_dias' => DispensacionAlmacen::ultimosDias(30)->count(),
            'por_area' => [
                'emergencia' => DispensacionAlmacen::porArea('emergencia')->count(),
                'cirugia' => DispensacionAlmacen::porArea('cirugia')->count(),
                'hospitalizacion' => DispensacionAlmacen::porArea('hospitalizacion')->count(),
                'uti' => DispensacionAlmacen::porArea('uti')->count(),
                'usi' => DispensacionAlmacen::porArea('usi')->count(),
                'neonato' => DispensacionAlmacen::porArea('neonato')->count(),
                'internacion' => DispensacionAlmacen::porArea('internacion')->count(),
            ],
        ];

        $areas = [
            'emergencia' => 'Emergencia',
            'cirugia' => 'Cirugía',
            'hospitalizacion' => 'Hospitalización',
            'uti' => 'UTI',
            'usi' => 'USI',
            'neonato' => 'Neonato',
            'internacion' => 'Internación'
        ];

        return view('admin.almacen-medicamentos.historial-dispensaciones', compact('dispensaciones', 'stats', 'areas'));
    }

    public function detalleDispensacion(DispensacionAlmacen $dispensacion)
    {
        $dispensacion->load(['medicamento', 'dispensadoPor', 'paciente', 'entregadoPor']);

        return view('admin.almacen-medicamentos.detalle-dispensacion', compact('dispensacion'));
    }

    public function registrarPaciente(Request $request, DispensacionAlmacen $dispensacion)
    {
        $request->validate([
            'paciente_ci' => 'required|integer|exists:pacientes,ci',
        ]);

        $dispensacion->update([
            'paciente_ci'            => $request->paciente_ci,
            'entregado_por'          => Auth::id(),
            'fecha_entrega_paciente' => now(),
        ]);

        return redirect()
            ->route('admin.almacen-medicamentos.detalle-dispensacion', $dispensacion->id)
            ->with('success', 'Paciente registrado correctamente.');
    }

    public function historialItem(AlmacenMedicamento $almacenMedicamento)
    {
        $dispensaciones = $almacenMedicamento->dispensaciones()
            ->with('dispensadoPor')
            ->recientes()
            ->paginate(15);

        return view('admin.almacen-medicamentos.historial-item', compact('almacenMedicamento', 'dispensaciones'));
    }

    public function transferirForm()
    {
        $medicamentos = AlmacenMedicamento::activos()
            ->central()
            ->where('cantidad', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view('admin.almacen-medicamentos.transferir', compact('medicamentos'));
    }

    public function procesarTransferencia(Request $request)
    {
        // Validar que items sea un JSON string válido y decodificarlo
        $request->validate([
            'area_destino' => 'required|in:emergencia,cirugia,hospitalizacion,uti,usi,neonato,internacion',
            'recibido_por' => 'nullable|string|max:150',
            'items' => 'required|string', // Llega como JSON string desde Alpine.js
        ]);

        $items = json_decode($request->items, true);

        // Validar que el JSON decodificado sea un array válido
        if (!is_array($items) || count($items) === 0) {
            return redirect()->back()
                ->with('error', 'Debe seleccionar al menos un medicamento para transferir.')
                ->withInput();
        }

        // Validar estructura de cada item
        foreach ($items as $index => $item) {
            if (!isset($item['id']) || !isset($item['cantidad'])) {
                return redirect()->back()
                    ->with('error', 'Datos de medicamentos inválidos. Verifique la selección.')
                    ->withInput();
            }

            if (!is_numeric($item['id']) || $item['id'] <= 0) {
                return redirect()->back()
                    ->with('error', 'ID de medicamento inválido en item #' . ($index + 1))
                    ->withInput();
            }

            if (!is_numeric($item['cantidad']) || $item['cantidad'] < 1) {
                return redirect()->back()
                    ->with('error', 'La cantidad debe ser al menos 1 en item #' . ($index + 1))
                    ->withInput();
            }
        }

        try {
            DB::transaction(function () use ($items, $request) {
                foreach ($items as $item) {
                    $central = AlmacenMedicamento::where('id', $item['id'])
                        ->where('area', 'central')
                        ->lockForUpdate()
                        ->first();

                    if (!$central) {
                        throw new \Exception("Medicamento no encontrado en almacén central");
                    }

                    if ($item['cantidad'] > $central->cantidad) {
                        throw new \Exception("Stock insuficiente para {$central->nombre}. Disponible: {$central->cantidad}");
                    }

                    // Descontar del central
                    $central->decrement('cantidad', $item['cantidad']);

                    // Buscar o crear en área destino
                    $destino = AlmacenMedicamento::where('nombre', $central->nombre)
                        ->where('area', $request->area_destino)
                        ->first();

                    if ($destino) {
                        $destino->increment('cantidad', $item['cantidad']);
                    } else {
                        AlmacenMedicamento::create([
                            'nombre' => $central->nombre,
                            'descripcion' => $central->descripcion,
                            'area' => $request->area_destino,
                            'precio' => $central->precio,
                            'precio_compra' => $central->precio_compra,
                            'porcentaje_ganancia' => $central->porcentaje_ganancia,
                            'precio_venta' => $central->precio_venta,
                            'fecha_vencimiento' => $central->fecha_vencimiento,
                            'lote' => $central->lote,
                            'cantidad' => $item['cantidad'],
                            'stock_minimo' => $central->stock_minimo,
                            'unidad_medida' => $central->unidad_medida,
                            'tipo' => $central->tipo,
                            'observaciones' => 'Transferido desde almacén central.',
                        ]);
                    }

                    // Registrar dispensación
                    DispensacionAlmacen::create([
                        'almacen_medicamento_id' => $central->id,
                        'cantidad' => $item['cantidad'],
                        'area_destino' => $request->area_destino,
                        'dispensado_por' => Auth::id(),
                        'recibido_por' => $request->recibido_por,
                        'fecha_dispensacion' => now(),
                    ]);

                    Log::info('Transferencia masiva: ' . $central->nombre . ' x' . $item['cantidad'] . ' a ' . $request->area_destino, [
                        'user_id' => Auth::id(),
                        'medicamento_id' => $central->id,
                        'cantidad' => $item['cantidad'],
                        'area_destino' => $request->area_destino,
                        'action' => 'transferencia_masiva',
                        'module' => 'almacen_medicamentos'
                    ]);
                }
            });

            return redirect()->route('admin.almacen-medicamentos.historial')
                ->with('success', 'Transferencia completada correctamente. Se transfirieron ' . count($items) . ' medicamentos a ' . ucfirst($request->area_destino) . '.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error en la transferencia: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function buscarPacienteApi(Request $request)
    {
        $ci = $request->input('ci');

        if (!$ci || !is_numeric($ci)) {
            return response()->json(['error' => 'C.I. inválido'], 422);
        }

        $paciente = Paciente::where('ci', (int) $ci)
            ->select('ci', 'nombre', 'sexo', 'fecha_nacimiento', 'telefono')
            ->first();

        if (!$paciente) {
            return response()->json(['error' => 'Paciente no encontrado'], 404);
        }

        return response()->json($paciente);
    }
}
