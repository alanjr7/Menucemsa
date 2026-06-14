@extends('layouts.app')

@section('title', 'Almacén de Medicamentos')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Almacén de Medicamentos e Insumos</h1>
                <p class="text-gray-600 mt-1">Catálogo normalizado con trazabilidad por lote</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.almacen-medicamentos.importar.form') }}"
                   class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"/>
                    </svg>
                    Importar Excel
                </a>
                <a href="{{ route('admin.almacen-medicamentos.ajuste-inventario.form') }}"
                   class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Ajustar Inventario
                </a>
                <a href="{{ route('admin.almacen-medicamentos.transferir.form') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Transferir a Área
                </a>
                <a href="{{ route('admin.almacen-medicamentos.lote.form') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Registrar Lote
                </a>
                <a href="{{ route('admin.almacen-medicamentos.create') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear Medicamento Nuevo
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
        @php
        $statCards = [
            ['label' => 'Total Catálogo', 'value' => $stats['total'], 'color' => 'blue'],
            ['label' => 'Medicamentos', 'value' => $stats['medicamentos'], 'color' => 'green'],
            ['label' => 'Insumos', 'value' => $stats['insumos'], 'color' => 'purple'],
            ['label' => 'Bajo Stock', 'value' => $stats['bajo_stock'], 'color' => 'yellow'],
            ['label' => 'Agotados', 'value' => $stats['agotados'], 'color' => 'red'],
            ['label' => 'Lotes Vencidos', 'value' => $stats['vencidos'], 'color' => 'red'],
            ['label' => 'Por Vencer', 'value' => $stats['por_vencer'], 'color' => 'orange'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-600">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold text-{{ $card['color'] }}-600 mt-1">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Reportes Rápidos -->
    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('admin.almacen-medicamentos.reporte.bajo-stock') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors text-sm">
            Reporte Bajo Stock
        </a>
        <a href="{{ route('admin.almacen-medicamentos.reporte.vencimiento') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors text-sm">
            Reporte Vencimiento
        </a>
        <a href="{{ route('admin.almacen-medicamentos.historial') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-800 rounded-lg hover:bg-indigo-200 transition-colors text-sm">
            Historial de Dispensaciones
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.almacen-medicamentos.index') }}" class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="tipo" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Todos</option>
                    <option value="medicamento" {{ request('tipo') == 'medicamento' ? 'selected' : '' }}>Medicamento</option>
                    <option value="insumo" {{ request('tipo') == 'insumo' ? 'selected' : '' }}>Insumo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado Stock</label>
                <select name="estado_stock" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Todos</option>
                    <option value="con_stock" {{ request('estado_stock') == 'con_stock' ? 'selected' : '' }}>Con stock</option>
                    <option value="bajo" {{ request('estado_stock') == 'bajo' ? 'selected' : '' }}>Bajo Stock</option>
                    <option value="agotado" {{ request('estado_stock') == 'agotado' ? 'selected' : '' }}>Agotado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                <select name="area" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="central" {{ request('area', 'central') == 'central' ? 'selected' : '' }}>Central</option>
                    <option value="emergencia" {{ request('area') == 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                    <option value="cirugia" {{ request('area') == 'cirugia' ? 'selected' : '' }}>Cirugía</option>
                    <option value="hospitalizacion" {{ request('area') == 'hospitalizacion' ? 'selected' : '' }}>Hospitalización</option>
                    <option value="uti" {{ request('area') == 'uti' ? 'selected' : '' }}>UTI</option>
                    <option value="usi" {{ request('area') == 'usi' ? 'selected' : '' }}>USI</option>
                    <option value="farmacia" {{ request('area') == 'farmacia' ? 'selected' : '' }}>Farmacia</option>
                    <option value="neonato" {{ request('area') == 'neonato' ? 'selected' : '' }}>Neonato</option>
                    <option value="internacion" {{ request('area') == 'internacion' ? 'selected' : '' }}>Internación</option>
                    <option value="todos" {{ request('area') == 'todos' ? 'selected' : '' }}>Todos las áreas</option>
                </select>
            </div>
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Nombre o descripción"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Buscar</button>
                <a href="{{ route('admin.almacen-medicamentos.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Tabla Catálogo -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
         x-data="{
            dispensarModal: false,
            stockModal: false,
            modalPacientes: false,
            pacientesLoading: false,
            pacientesData: [],
            pacientesMed: '',
            item: {},
            lotes: [],
            loteSeleccionado: null,
            stockDisponible: 0,
            openDispensar(id, nombre, unidad, lotesJson) {
                this.item = { id, nombre, unidad };
                this.lotes = JSON.parse(lotesJson);
                this.loteSeleccionado = null;
                this.stockDisponible = 0;
                this.dispensarModal = true;
            },
            openStock(id, nombre, lotesJson) {
                this.item = { id, nombre };
                this.lotes = JSON.parse(lotesJson);
                this.loteSeleccionado = null;
                this.stockModal = true;
            },
            abrirPacientes(catalogoId, nombre) {
                this.modalPacientes = true;
                this.pacientesLoading = true;
                this.pacientesMed = nombre;
                this.pacientesData = [];

                const area = '{{ request('area') }}';
                axios.get(`/admin/almacen-medicamentos/${catalogoId}/pacientes-area?area=${area}`)
                    .then(r => { this.pacientesData = r.data; })
                    .finally(() => { this.pacientesLoading = false; });
            },
            onLoteChange(loteId) {
                const lote = this.lotes.find(l => l.id == loteId);
                this.stockDisponible = lote ? lote.stock_central : 0;
            }
         }">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Catálogo de Medicamentos e Insumos</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lotes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">P. Compra (ref.)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">P. Venta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ganancia (ref.)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento (próx.)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                @php
                    // Formatea un agregado de precios por lote: '—' si no hay, 'Bs. X' si todos
                    // los lotes coinciden, o 'Bs. min – max' (con badge "Varios") si difieren.
                    $aggPrecio = function ($valores, $sufijo = '', $prefijo = 'Bs. ') {
                        $vals = collect($valores)->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v)->values();
                        if ($vals->isEmpty()) {
                            return ['txt' => '—', 'varios' => false];
                        }
                        $u = $vals->unique();
                        if ($u->count() === 1) {
                            return ['txt' => $prefijo.number_format($u->first(), 2).$sufijo, 'varios' => false];
                        }
                        return ['txt' => $prefijo.number_format($vals->min(), 2).' – '.number_format($vals->max(), 2).$sufijo, 'varios' => true];
                    };
                @endphp
                @forelse($catalogo as $item)
                @php
                    $todosStocks = $item->lotes->flatMap->stocks;
                    $lotesParaJs = $item->lotes->map(fn($l) => [
                        'id' => $l->id,
                        'codigo' => $l->codigo_lote ?? 'Sin código',
                        'laboratorio' => $l->laboratorio,
                        'proveedor' => $l->proveedor,
                        'vencimiento' => $l->fecha_vencimiento?->format('d/m/Y') ?? 'Sin fecha',
                        'stock_central' => $l->stocks->where('ubicacion', 'central')->sum('cantidad_actual'),
                    ])->filter(fn($l) => $l['stock_central'] > 0)->values()->toJson();

                    $fechaVencimiento = $item->lotes
                        ->filter(fn($l) => $l->fecha_vencimiento && $l->fecha_vencimiento->isFuture())
                        ->sortBy('fecha_vencimiento')
                        ->first()?->fecha_vencimiento;

                    if ($mostrarTodos) {
                        $filas = $todosStocks->groupBy('ubicacion')->map(fn($stocks, $ub) => [
                            'ubicacion' => $ub,
                            'stock' => $stocks->sum('cantidad_actual'),
                            'stock_minimo' => $stocks->min('stock_minimo') ?? 0,
                        ])->values();
                        if ($filas->isEmpty()) {
                            $filas = collect([['ubicacion' => null, 'stock' => 0, 'stock_minimo' => 0]]);
                        }
                    } else {
                        $ubicacionActual = $area ?? 'central';
                        $stocksFiltrados = $todosStocks->where('ubicacion', $ubicacionActual);
                        $filas = collect([['ubicacion' => $ubicacionActual, 'stock' => $stocksFiltrados->sum('cantidad_actual'), 'stock_minimo' => $stocksFiltrados->min('stock_minimo') ?? 0]]);
                    }
                @endphp
                @foreach($filas as $fila)
                @php
                    $stockFila = $fila['stock'];
                    $stockMinFila = $fila['stock_minimo'];
                    $estadoStock = $stockFila <= 0 ? 'agotado' : ($stockFila <= $stockMinFila ? 'bajo' : 'normal');

                    // Lotes con stock en el área de esta fila (los que aportan precio/ganancia reales).
                    $ubicFila = $fila['ubicacion'] ?? 'central';
                    $lotesFila = $item->lotes
                        ->map(fn($l) => (object) [
                            'codigo' => $l->codigo_lote ?: ('Lote #'.$l->id),
                            'proveedor' => $l->proveedor,
                            'laboratorio' => $l->laboratorio,
                            'stock' => $l->stocks->where('ubicacion', $ubicFila)->sum('cantidad_actual'),
                            'precio_compra' => $l->precio_compra,
                            'precio_venta' => $l->precio_venta,
                            'ganancia' => $l->porcentaje_ganancia,
                            'vencimiento' => $l->fecha_vencimiento,
                        ])
                        ->filter(fn($l) => $l->stock > 0)
                        ->sortBy(fn($l) => optional($l->vencimiento)->format('Y-m-d') ?? '9999-12-31')
                        ->values();

                    $aggCompra = $aggPrecio($lotesFila->pluck('precio_compra'));
                    $aggVenta = $aggPrecio($lotesFila->pluck('precio_venta'));
                    $aggGanancia = $aggPrecio($lotesFila->pluck('ganancia'), '%', '');
                @endphp
                <tbody class="bg-white divide-y divide-gray-200" x-data="{ open: false }">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->nombre }}</div>
                            @if($item->descripcion)
                                <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($item->descripcion, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->tipo == 'medicamento' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $item->tipo_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $fila['ubicacion'] ? ucfirst($fila['ubicacion']) : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->unidad_medida }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($lotesFila->count() > 0)
                                <button type="button" @click="open = !open"
                                        class="inline-flex items-center gap-1 text-gray-700 hover:text-indigo-600 font-medium"
                                        :title="open ? 'Ocultar lotes' : 'Ver lotes'">
                                    <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    {{ $lotesFila->count() }} lote(s)
                                </button>
                            @else
                                <span class="text-gray-400">{{ $item->lotes->count() }} lote(s)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($estadoStock === 'agotado')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">0 {{ $item->unidad_medida }}</span>
                            @elseif($estadoStock === 'bajo')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $stockFila }} {{ $item->unidad_medida }}</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $stockFila }} {{ $item->unidad_medida }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($estadoStock === 'agotado')
                                <span class="text-xs text-red-600 font-medium">Agotado</span>
                            @elseif($estadoStock === 'bajo')
                                <span class="text-xs text-yellow-600 font-medium">Bajo stock</span>
                            @else
                                <span class="text-xs text-green-600 font-medium">Normal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $aggCompra['txt'] }}
                            @if($aggCompra['varios'])<span class="ml-1 px-1.5 py-0.5 text-[10px] font-semibold rounded bg-amber-100 text-amber-700">Varios</span>@endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $aggVenta['txt'] }}
                            @if($aggVenta['varios'])<span class="ml-1 px-1.5 py-0.5 text-[10px] font-semibold rounded bg-amber-100 text-amber-700">Varios</span>@endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $aggGanancia['txt'] }}
                            @if($aggGanancia['varios'])<span class="ml-1 px-1.5 py-0.5 text-[10px] font-semibold rounded bg-amber-100 text-amber-700">Varios</span>@endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($fechaVencimiento)
                                <span class="{{ $fechaVencimiento->diffInDays(now()) <= 30 ? 'text-amber-600 font-medium' : 'text-gray-600' }}">
                                    {{ $fechaVencimiento->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.almacen-medicamentos.show', $item) }}"
                                   class="text-gray-500 hover:text-gray-700" title="Ver detalle">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.almacen-medicamentos.edit', $item) }}"
                                   class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if($stockFila > 0)
                                <button type="button"
                                        @click="openDispensar({{ $item->id }}, '{{ addslashes($item->nombre) }}', '{{ $item->unidad_medida }}', '{{ addslashes($lotesParaJs) }}')"
                                        class="text-blue-600 hover:text-blue-900" title="Dispensar a área">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </button>
                                @endif
                                    <a href="{{ route('admin.almacen-medicamentos.show', $item) }}" class="text-sm text-gray-700 hover:text-gray-900 px-2 py-1 border rounded" title="Ver lotes">Lotes</a>
                                @if($area && !$mostrarTodos && $stockFila > 0)
                                <button type="button"
                                        @click="abrirPacientes({{ $item->id }}, '{{ addslashes($item->nombre) }}')"
                                        class="text-purple-600 hover:text-purple-900 text-xs font-medium" title="Ver pacientes">
                                    Pacientes
                                </button>
                                @endif
                                <a href="{{ route('admin.almacen-medicamentos.historial-item', $item) }}"
                                   class="text-purple-600 hover:text-purple-900" title="Historial">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.almacen-medicamentos.destroy', $item) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('¿Desactivar este ítem?')"
                                            class="text-red-600 hover:text-red-900" title="Desactivar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <!-- Fila-detalle: desglose por lote (proveedor/precio/ganancia/vencimiento) -->
                    <tr x-show="open" x-cloak>
                        <td colspan="12" class="px-6 py-3 bg-gray-50 border-l-4 border-indigo-300">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs">
                                    <thead>
                                        <tr class="text-gray-500">
                                            <th class="text-left font-semibold px-3 py-1.5">Proveedor / Lab.</th>
                                            <th class="text-left font-semibold px-3 py-1.5">Lote</th>
                                            <th class="text-right font-semibold px-3 py-1.5">Stock</th>
                                            <th class="text-right font-semibold px-3 py-1.5">P. Compra</th>
                                            <th class="text-right font-semibold px-3 py-1.5">P. Venta</th>
                                            <th class="text-right font-semibold px-3 py-1.5">Ganancia</th>
                                            <th class="text-left font-semibold px-3 py-1.5">Vencimiento</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($lotesFila as $l)
                                        <tr class="text-gray-700">
                                            <td class="px-3 py-1.5">{{ $l->laboratorio ?: ($l->proveedor ?: '—') }}@if($l->laboratorio && $l->proveedor)<span class="text-gray-400"> · {{ $l->proveedor }}</span>@endif</td>
                                            <td class="px-3 py-1.5">{{ $l->codigo }}</td>
                                            <td class="px-3 py-1.5 text-right font-medium">{{ $l->stock }} {{ $item->unidad_medida }}</td>
                                            <td class="px-3 py-1.5 text-right">{{ $l->precio_compra !== null ? 'Bs. '.number_format($l->precio_compra, 2) : '—' }}</td>
                                            <td class="px-3 py-1.5 text-right font-semibold text-emerald-700">{{ $l->precio_venta !== null ? 'Bs. '.number_format($l->precio_venta, 2) : '—' }}</td>
                                            <td class="px-3 py-1.5 text-right">{{ $l->ganancia !== null ? number_format($l->ganancia, 2).'%' : '—' }}</td>
                                            <td class="px-3 py-1.5">
                                                @if($l->vencimiento)
                                                    <span class="{{ $l->vencimiento->diffInDays(now()) <= 30 && $l->vencimiento->isFuture() ? 'text-amber-600 font-medium' : 'text-gray-600' }}">{{ $l->vencimiento->format('d/m/Y') }}</span>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
                @endforeach
                @empty
                <tbody class="bg-white">
                    <tr>
                        <td colspan="12" class="px-6 py-12 text-center text-gray-500">
                            No se encontraron medicamentos/insumos.
                        </td>
                    </tr>
                </tbody>
                @endforelse
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $catalogo->links() }}
        </div>

        <!-- Modal Dispensar -->
        <div x-show="dispensarModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="dispensarModal = false"></div>
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg">
                <form method="POST" :action="`/admin/almacen-medicamentos/${item.id}/dispensar`">
                    @csrf
                    <div class="p-6 space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Dispensar: <span x-text="item.nombre" class="text-blue-700"></span></h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lote a dispensar <span class="text-red-500">*</span></label>
                            <select name="lote_id" x-model="loteSeleccionado" @change="onLoteChange($event.target.value)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                                <option value="">Seleccione un lote</option>
                                <template x-for="lote in lotes" :key="lote.id">
                                    <option :value="lote.id" x-text="`Lote ${lote.codigo}${lote.laboratorio ? ' — ' + lote.laboratorio : ''} — vence ${lote.vencimiento} — disponible: ${lote.stock_central}`"></option>
                                </template>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Stock central disponible: <span x-text="stockDisponible" class="font-semibold"></span> <span x-text="item.unidad"></span></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad <span class="text-red-500">*</span></label>
                            <input type="number" name="cantidad" min="1" :max="stockDisponible"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Área destino <span class="text-red-500">*</span></label>
                            <select name="ubicacion_destino" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                                <option value="">Seleccione un área</option>
                                <option value="emergencia">Emergencia</option>
                                <option value="cirugia">Cirugía</option>
                                <option value="hospitalizacion">Hospitalización</option>
                                <option value="uti">UTI</option>
                                <option value="usi">USI</option>
                                <option value="neonato">Neonato</option>
                                <option value="internacion">Internación</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recibido por</label>
                            <input type="text" name="recibido_por" maxlength="150"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                   placeholder="Nombre de quien recibe físicamente">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end gap-3">
                        <button type="button" @click="dispensarModal = false"
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Dispensar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Pacientes -->
        <div x-show="modalPacientes" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="modalPacientes = false"></div>
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg">
                <div class="p-6">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="'Pacientes — ' + pacientesMed"></h3>
                        <button @click="modalPacientes = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 mb-4">
                        Área: <span class="font-medium capitalize">{{ request('area') }}</span>
                    </p>

                    <div x-show="pacientesLoading" class="text-center py-6 text-gray-500 text-sm">
                        <svg class="animate-spin h-5 w-5 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Cargando...
                    </div>

                    <div x-show="!pacientesLoading">
                        <template x-if="pacientesData.length === 0">
                            <p class="text-sm text-gray-500 text-center py-6">
                                Ningún paciente ha recibido este medicamento desde esta área.
                            </p>
                        </template>

                        <template x-if="pacientesData.length > 0">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Paciente</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">CI</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="p in pacientesData" :key="p.ci">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 font-medium text-gray-900" x-text="p.nombre"></td>
                                                <td class="px-4 py-3 text-gray-600" x-text="p.ci"></td>
                                                <td class="px-4 py-3 text-right font-semibold text-indigo-700" x-text="p.total_cantidad"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
