<x-app-layout>
    <div class="p-8 bg-[#f8fafc] min-h-screen font-sans">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1e293b]">Reportes y Estadísticas</h1>
                <p class="text-gray-500 text-sm font-medium">Análisis de ventas y desempeño</p>
            </div>
            <div class="relative">
                <select class="appearance-none bg-white border border-blue-500 text-blue-600 px-6 py-2.5 pr-12 rounded-xl font-bold focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer shadow-sm">
                    <option>Todo el Tiempo</option>
                    <option>Hoy</option>
                    <option>Últimos 7 días</option>
                    <option>Este Mes</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center transition-transform hover:scale-[1.02]">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Ventas</p>
                    <p class="text-3xl font-black text-gray-800">0</p>
                </div>
                <div class="bg-blue-500 p-4 rounded-2xl shadow-lg shadow-blue-100 text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center transition-transform hover:scale-[1.02]">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Ingresos Totales</p>
                    <p class="text-3xl font-black text-green-500">$0.00</p>
                </div>
                <div class="bg-green-500 p-4 rounded-2xl shadow-lg shadow-green-100 text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center transition-transform hover:scale-[1.02]">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Promedio por Venta</p>
                    <p class="text-3xl font-black text-gray-800">$0.00</p>
                </div>
                <div class="bg-purple-500 p-4 rounded-2xl shadow-lg shadow-purple-100 text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center transition-transform hover:scale-[1.02]">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Alertas de Stock</p>
                    <p class="text-3xl font-black text-orange-500">0</p>
                </div>
                <div class="bg-orange-500 p-4 rounded-2xl shadow-lg shadow-orange-100 text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

     <div class="bg-white p-8 rounded-[2rem] border border-gray-200 shadow-sm min-h-[450px] flex flex-col">
    <h2 class="text-xl font-bold text-[#1e293b] mb-10">Ventas de los Últimos 7 Días</h2>

    <div class="flex-1 relative w-full mb-10 ml-2">
        <div class="absolute left-0 top-0 bottom-8 w-10 flex flex-col justify-between text-right pr-4 text-sm text-gray-500 font-medium">
            <span class="leading-none translate-y-[-50%]">4</span>
            <span class="leading-none translate-y-[-50%]">3</span>
            <span class="leading-none translate-y-[-50%]">2</span>
            <span class="leading-none translate-y-[-50%]">1</span>
            <span class="leading-none translate-y-[50%]">0</span>
        </div>

        <div class="absolute left-10 top-0 right-4 bottom-8 border-l border-b border-gray-400">

            <div class="absolute left-[-6px] top-0 bottom-0 w-[6px] flex flex-col justify-between">
                <div class="w-full h-px bg-gray-400"></div>
                <div class="w-full h-px bg-gray-400"></div>
                <div class="w-full h-px bg-gray-400"></div>
                <div class="w-full h-px bg-gray-400"></div>
                <div class="w-full h-px bg-transparent"></div> </div>

            <div class="absolute inset-0 grid grid-cols-7 pointer-events-none">
                <div class="border-r border-dashed border-gray-300"></div>
                <div class="border-r border-dashed border-gray-300"></div>
                <div class="border-r border-dashed border-gray-300"></div>
                <div class="border-r border-dashed border-gray-300"></div>
                <div class="border-r border-dashed border-gray-300"></div>
                <div class="border-r border-dashed border-gray-300"></div>
                <div class="border-r border-dashed border-gray-300"></div>

                <div class="absolute inset-0 flex flex-col justify-between">
                    <div class="w-full border-t border-dashed border-gray-300"></div>
                    <div class="w-full border-t border-dashed border-gray-300"></div>
                    <div class="w-full border-t border-dashed border-gray-300"></div>
                    <div class="w-full border-t border-dashed border-gray-300"></div>
                    <div class="w-full h-0"></div>
                </div>
            </div>

         <div class="absolute inset-0 flex justify-around">
    @php
        $dias = [
            ['fecha' => '19/2', 'valor' => 4],
            ['fecha' => '20/2', 'valor' => 4],
            ['fecha' => '21/2', 'valor' => 4],
            ['fecha' => '22/2', 'valor' => 4],
            ['fecha' => '23/2', 'valor' => 4],
            ['fecha' => '24/2', 'valor' => 4],
            ['fecha' => '25/2', 'valor' => 4],
        ];
    @endphp

    @foreach($dias as $dia)
        <div class="relative h-full flex flex-col justify-end items-center group flex-1">

            <div class="absolute -bottom-[6px] w-px h-[6px] bg-gray-400"></div>

            <span class="absolute -bottom-10 text-sm text-gray-500 font-medium">
                {{ $dia['fecha'] }}
            </span>

            <div class="w-10 relative z-10 cursor-pointer transition-colors duration-200
                        bg-transparent group-hover:bg-[#cccccc]"
                 style="height: {{ ($dia['valor'] / 4) * 100 }}%">

                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                            bg-white border border-gray-200 shadow-md px-4 py-3 min-w-[110px]
                            opacity-0 group-hover:opacity-100 transition-opacity z-50
                            pointer-events-none rounded-sm">
                    <p class="text-[14px] text-gray-700 font-medium mb-1">{{ $dia['fecha'] }}</p>
                    <p class="text-[14px] text-blue-500 font-semibold whitespace-nowrap">Total ($) : 0</p>
                </div>
            </div>

            @if($dia['valor'] == 0)
                <div class="absolute inset-0 w-10 mx-auto cursor-pointer group">
                     <div class="absolute bottom-10 left-1/2 -translate-x-1/2 bg-white border border-gray-200 shadow-md px-4 py-3 min-w-[110px] opacity-0 group-hover:opacity-100 transition-opacity z-50 pointer-events-none rounded-sm">
                        <p class="text-[14px] text-gray-700 font-medium mb-1">{{ $dia['fecha'] }}</p>
                        <p class="text-[14px] text-blue-500 font-semibold">Total ($) : 0</p>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
        </div>
    </div>
</div>

            <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm min-h-[450px] flex flex-col">
                <h2 class="text-xl font-black text-gray-800 mb-8">Ventas por Método de Pago</h2>
                <div class="flex-1 flex flex-col items-center justify-center text-center">
                    <div class="w-40 h-40 border-[12px] border-gray-50 rounded-full mb-6 relative">
                    </div>
                    <p class="text-gray-400 font-bold text-sm">Sin datos para mostrar</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50">
                <h2 class="text-xl font-black text-gray-800">Top 10 Productos Más Vendidos</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-8 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">#</th>
                            <th class="px-8 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Producto</th>
                            <th class="px-8 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Cantidad Vendida</th>
                            <th class="px-8 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">Total Generado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-400 font-bold text-sm">
                                No hay datos de ventas en el periodo seleccionado
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
