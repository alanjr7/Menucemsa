@extends('layouts.app')

@section('content')
<div class="w-full p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2"/></svg>
                </div>
                <h1 class="text-[26px] font-black text-slate-800 tracking-tight">Historial de Seguros</h1>
            </div>
            <p class="text-slate-500 text-[15px] font-medium mt-1 ml-11">Registro de autorizaciones y rechazos de seguros</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.seguros') }}" class="bg-white hover:bg-gray-50 text-slate-700 px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm border border-slate-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2"/></svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[13px] font-medium mb-1">Autorizados</p>
            <p class="text-[#0ca678] text-[32px] font-black tracking-tighter">{{ $stats['total_autorizados'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[13px] font-medium mb-1">Rechazados</p>
            <p class="text-[#e03131] text-[32px] font-black tracking-tighter">{{ $stats['total_rechazados'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[13px] font-medium mb-1">Total Cubierto</p>
            <p class="text-slate-800 text-[32px] font-black tracking-tighter">Bs. {{ number_format($stats['monto_total_cubierto'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[13px] font-medium mb-1">Total Paciente</p>
            <p class="text-slate-800 text-[32px] font-black tracking-tighter">Bs. {{ number_format($stats['monto_total_paciente'] ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-5 rounded-[20px] border border-slate-100 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.seguros.historial') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Paciente</label>
                <input type="text" name="paciente" value="{{ request('paciente') }}" placeholder="Nombre o CI" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Seguro</label>
                <select name="seguro_id" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none">
                    <option value="">Todos</option>
                    @foreach($seguros as $seguro)
                        <option value="{{ $seguro->id }}" {{ request('seguro_id') == $seguro->id ? 'selected' : '' }}>{{ $seguro->nombre_empresa }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                <select name="estado" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none">
                    <option value="">Todos</option>
                    <option value="autorizado" {{ request('estado') == 'autorizado' ? 'selected' : '' }}>Autorizado</option>
                    <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-sm transition-all">
                    Filtrar
                </button>
                <a href="{{ route('admin.seguros.historial') }}" class="bg-gray-100 hover:bg-gray-200 text-slate-700 px-6 py-3 rounded-xl text-sm font-bold transition-all">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Historial -->
    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-6">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">Registros</h3>
            <div class="flex gap-2">
                <a href="{{ route('admin.seguros.historial.exportar', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-[12px] font-bold shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                    Exportar CSV
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-slate-400 text-[11px] uppercase font-bold tracking-widest border-b border-slate-50">
                    <tr>
                        <th class="px-6 py-5">Fecha</th>
                        <th class="px-6 py-5">Paciente</th>
                        <th class="px-6 py-5">CI</th>
                        <th class="px-6 py-5">Seguro</th>
                        <th class="px-6 py-5">Tipo Atención</th>
                        <th class="px-6 py-5">Monto Total</th>
                        <th class="px-6 py-5">Cobertura</th>
                        <th class="px-6 py-5">Copago</th>
                        <th class="px-6 py-5">Estado</th>
                        <th class="px-6 py-5">Autorizado Por</th>
                        <th class="px-6 py-5">Cargos</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] divide-y divide-slate-50">
                    @forelse($historial as $registro)
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="px-6 py-4 text-slate-500">{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-slate-600 font-medium">{{ $registro->paciente?->nombre ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $registro->paciente_ci }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $registro->seguro?->nombre_empresa ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $registro->tipo_atencion_label }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">Bs. {{ number_format($registro->total_calculado, 2) }}</td>
                        <td class="px-6 py-4 font-medium text-green-600">Bs. {{ number_format($registro->seguro_monto_cobertura ?? 0, 2) }}</td>
                        <td class="px-6 py-4 font-medium text-orange-600">Bs. {{ number_format($registro->seguro_monto_paciente ?? 0, 2) }}</td>
                        <td class="px-6 py-4">
                            @if($registro->seguro_estado === 'autorizado')
                                <span class="bg-green-50 text-green-600 border border-green-100 px-3 py-1.5 rounded-lg text-[12px] font-bold">Autorizado</span>
                            @else
                                <span class="bg-red-50 text-red-600 border border-red-100 px-3 py-1.5 rounded-lg text-[12px] font-bold">Rechazado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $registro->seguroAutorizadoPor?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <button onclick="verCargos('{{ $registro->id }}')" class="text-blue-600 hover:text-blue-800 text-xs font-bold">
                                Ver detalles
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-8 text-center text-slate-500">
                            No hay registros en el historial
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-50">
            {{ $historial->links() }}
        </div>
    </div>

</div>

<!-- Modal de Cargos -->
<div id="modalCargos" class="fixed inset-0 bg-black/50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-[24px] p-8 w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">Detalle de Cargos</h3>
                <button onclick="cerrarModalCargos()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
                </button>
            </div>
            <div id="cargosContent"></div>
        </div>
    </div>
</div>

<script>
let cargosData = {};

// Cargar datos de cargos para cada registro
@php
$cargosPorCuenta = [];
foreach($historial as $registro) {
    $cargosPorTipo = [];
    foreach($registro->detalles as $detalle) {
        $tipo = $detalle->tipo_item_label;
        if (!isset($cargosPorTipo[$tipo])) {
            $cargosPorTipo[$tipo] = [];
        }
        $cargosPorTipo[$tipo][] = [
            'descripcion' => $detalle->descripcion,
            'cantidad' => $detalle->cantidad,
            'precio' => $detalle->precio_unitario,
            'subtotal' => $detalle->subtotal
        ];
    }
    $cargosPorCuenta[$registro->id] = $cargosPorTipo;
}
@endphp

cargosData = @json($cargosPorCuenta);

console.log('Datos de cargos cargados:', cargosData);

function verCargos(cuentaId) {
    console.log('Ver cargos para ID:', cuentaId);
    console.log('Cargos disponibles:', cargosData[cuentaId]);
    
    const cargos = cargosData[cuentaId] || {};
    let html = '<div class="space-y-4">';
    
    if (Object.keys(cargos).length === 0) {
        html += '<p class="text-slate-500 text-center">No hay cargos registrados</p>';
    } else {
        for (const [tipo, items] of Object.entries(cargos)) {
            html += `
                <div class="bg-slate-50 rounded-xl p-4">
                    <h4 class="font-bold text-slate-800 mb-3">${tipo}</h4>
                    <div class="space-y-2">
            `;
            
            items.forEach(item => {
                html += `
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <p class="text-slate-700">${item.descripcion}</p>
                            <p class="text-slate-400 text-xs">Cantidad: ${item.cantidad} x Bs. ${item.precio}</p>
                        </div>
                        <p class="font-medium text-slate-800">Bs. ${item.subtotal}</p>
                    </div>
                `;
            });
            
            html += '</div></div>';
        }
    }
    
    html += '</div>';
    
    document.getElementById('cargosContent').innerHTML = html;
    document.getElementById('modalCargos').classList.remove('hidden');
}

function cerrarModalCargos() {
    document.getElementById('modalCargos').classList.add('hidden');
}
</script>
@endsection
