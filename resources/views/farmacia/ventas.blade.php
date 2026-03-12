@extends('layouts.app')

@section('content')
<div class="p-8 bg-[#f8fafc] min-h-screen font-sans">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Historial de Ventas</h1>
            <p class="text-gray-500 text-sm">{{ $totalVentas }} ventas registradas</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase mb-1">Total Ventas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalVentas }}</p>
                </div>
                <div class="bg-blue-500 p-3 rounded-xl shadow-lg shadow-blue-200 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase mb-1">Ingresos Totales</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($ingresosTotales, 2) }}</p>
                </div>
                <div class="bg-green-500 p-3 rounded-xl shadow-lg shadow-green-200 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase mb-1">Promedio por Venta</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($promedioPorVenta, 2) }}</p>
                </div>
                <div class="bg-purple-500 p-3 rounded-xl shadow-lg shadow-purple-200 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-8">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text"
                    class="block w-full pl-12 pr-4 py-3 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-gray-400 text-sm bg-gray-50/50"
                    placeholder="Buscar por ID, cliente o vendedor...">
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden min-h-[400px] flex flex-col">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Lista de Ventas</h2>
                </div>
                @if($ventas->count() > 0)
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($ventas as $venta)
                                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="verDetalle('{{ $venta->CODIGO_VENTA }}')">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $venta->CODIGO_VENTA }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $venta->FECHA_VENTA->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $venta->CLIENTE ?: 'Cliente General' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">${{ number_format($venta->TOTAL, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $venta->ESTADO }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                        <div class="bg-gray-50 p-6 rounded-full mb-4">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-400 font-medium">No hay ventas registradas</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden min-h-[400px] flex flex-col">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Detalle de Venta</h2>
                </div>
                <div id="ventaDetalle" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                    <div class="bg-gray-50 p-6 rounded-full mb-4">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <p class="text-gray-400 font-medium">Selecciona una venta para ver los detalles</p>
                </div>
            </div>

        </div>
</div>

<script>
function verDetalle(codigoVenta) {
    fetch(`/farmacia/ventas/${codigoVenta}`)
        .then(response => response.json())
        .then(data => {
            const detalleHtml = `
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">${data.CODIGO_VENTA}</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Fecha:</p>
                                <p class="font-medium">${new Date(data.FECHA_VENTA).toLocaleString()}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Cliente:</p>
                                <p class="font-medium">${data.CLIENTE || 'Cliente General'}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Método de Pago:</p>
                                <p class="font-medium">${data.METODO_PAGO}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Estado:</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    ${data.ESTADO}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-800 mb-3">Productos</h4>
                        <div class="space-y-2">
                            ${data.detalles.map(item => `
                                <div class="flex justify-between items-center py-2 border-b">
                                    <div>
                                        <p class="font-medium">${item.NOMBRE_PRODUCTO}</p>
                                        <p class="text-sm text-gray-500">${item.CANTIDAD} x $${parseFloat(item.PRECIO_UNITARIO).toFixed(2)}</p>
                                    </div>
                                    <p class="font-bold">$${parseFloat(item.SUBTOTAL).toFixed(2)}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="border-t pt-4 mt-4">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-bold text-gray-800">Total:</p>
                            <p class="text-xl font-bold text-green-600">$${parseFloat(data.TOTAL).toFixed(2)}</p>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('ventaDetalle').innerHTML = detalleHtml;
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
</script>
@endsection
