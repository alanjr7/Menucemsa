<x-app-layout>
    <div class="min-h-screen bg-gray-50/50 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Gestión de Farmacia</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">Control de stock, lotes y vencimientos</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 shadow-sm flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Registrar Ingreso
                </button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-md shadow-blue-200 flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Dispensar Medicamento
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Productos</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-2">485</h3>
                    <p class="text-xs text-gray-400 mt-1 font-medium">Items activos</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-blue-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.628.283a2 2 0 01-1.631 0l-.628-.283a6 6 0 00-3.86-.517l-2.387.477a2 2 0 00-1.022.547l-1.16 1.16a2 2 0 000 2.828l1.16 1.16a2 2 0 002.828 0l1.16-1.16a2 2 0 00.547-1.022l.477-2.387a6 6 0 01.517-3.86l.283-.628a2 2 0 000-1.631l-.283-.628a6 6 0 01-.517-3.86l-.477-2.387a2 2 0 00-.547-1.022l-1.16-1.16a2 2 0 00-2.828 0l-1.16 1.16a2 2 0 000 2.828l1.16 1.16z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest">Stock Bajo</p>
                    <h3 class="text-3xl font-black text-red-500 mt-2">12</h3>
                    <p class="text-xs text-gray-400 mt-1 font-medium">Requiere reposición</p>
                </div>
                <div class="p-3 bg-red-50 rounded-xl text-red-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-bold text-orange-400 uppercase tracking-widest">Por Vencer</p>
                    <h3 class="text-3xl font-black text-orange-500 mt-2">5</h3>
                    <p class="text-xs text-gray-400 mt-1 font-medium">Próximos 30 días</p>
                </div>
                <div class="p-3 bg-orange-50 rounded-xl text-orange-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-bold text-green-500 uppercase tracking-widest">Valor Inventario</p>
                    <h3 class="text-3xl font-black text-green-600 mt-2">S/ 85,340</h3>
                    <p class="text-xs text-gray-400 mt-1 font-medium">Valorizado</p>
                </div>
                <div class="p-3 bg-green-50 rounded-xl text-green-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-50 flex items-center gap-3 bg-gray-50/50">
        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <h2 class="font-bold text-gray-800 text-lg">Alertas Críticas</h2>
    </div>

    <div class="p-6 space-y-3">
        <div class="flex items-center gap-4 p-4 bg-red-50/50 border-l-4 border-red-500 rounded-r-xl">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm font-bold text-red-700 uppercase">STOCK CRÍTICO: PARACETAMOL (45 UNIDADES)</p>
        </div>

        <div class="flex items-center gap-4 p-4 bg-red-50/50 border-l-4 border-red-500 rounded-r-xl">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm font-bold text-red-700 uppercase">PRÓXIMO VENCIMIENTO: METFORMINA - VENCE EN 25 DÍAS</p>
        </div>

        <div class="flex items-center gap-4 p-4 bg-orange-50/50 border-l-4 border-orange-400 rounded-r-xl">
            <svg class="w-5 h-5 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm font-bold text-orange-700 uppercase">STOCK BAJO: AMOXICILINA (25 UNIDADES)</p>
        </div>

        <div class="flex items-center gap-4 p-4 bg-orange-50/50 border-l-4 border-orange-400 rounded-r-xl">
            <svg class="w-5 h-5 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm font-bold text-orange-700 uppercase">PRÓXIMO VENCIMIENTO: AMOXICILINA - VENCE EN 35 DÍAS</p>
        </div>
    </div>
</div>
    </div>


    <div class="mt-8 bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex gap-4 items-center">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <input type="text" placeholder="Buscar por código, nombre, lote o ubicación..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        <button class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-gray-600 hover:bg-gray-100 transition">
            Filtros
        </button>
    </div>

    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-10">
        <div class="p-5 border-b border-gray-50 bg-gray-50/30">
            <h2 class="font-bold text-gray-800 text-lg">Inventario de Medicamentos</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] font-black text-gray-400 uppercase tracking-wider border-b border-gray-50">
                        <th class="px-6 py-4">Código</th>
                        <th class="px-6 py-4">Producto</th>
                        <th class="px-6 py-4">Lote</th>
                        <th class="px-6 py-4">Stock</th>
                        <th class="px-6 py-4">Nivel</th>
                        <th class="px-6 py-4">Vencimiento</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Ubicación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 text-xs font-bold text-gray-700">MED-001</td>
                        <td class="px-6 py-4 text-xs font-medium text-gray-600">Paracetamol 500mg</td>
                        <td class="px-6 py-4 text-xs text-gray-500 font-mono">L2026-001</td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-700">45 <span class="text-gray-400">/ 100</span></td>
                        <td class="px-6 py-4">
                            <div class="w-16">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[9px] font-bold text-red-500">45%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-red-500 h-1.5 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">2026-12-15</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-black rounded uppercase">Crítico</span>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-500">A-01</td>
                    </tr>

                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 text-xs font-bold text-gray-700">MED-002</td>
                        <td class="px-6 py-4 text-xs font-medium text-gray-600">Ibuprofeno 400mg</td>
                        <td class="px-6 py-4 text-xs text-gray-500 font-mono">L2026-002</td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-700">180 <span class="text-gray-400">/ 150</span></td>
                        <td class="px-6 py-4">
                            <div class="w-16">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[9px] font-bold text-green-500">120%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-green-500 h-1.5 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">2027-06-20</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded uppercase">OK</span>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-500">A-02</td>
                    </tr>

                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 text-xs font-bold text-gray-700">MED-003</td>
                        <td class="px-6 py-4 text-xs font-medium text-gray-600">Amoxicilina 500mg</td>
                        <td class="px-6 py-4 text-xs text-gray-500 font-mono">L2025-045</td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-700">25 <span class="text-gray-400">/ 80</span></td>
                        <td class="px-6 py-4">
                            <div class="w-16">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[9px] font-bold text-orange-500">31%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-orange-500 h-1.5 rounded-full" style="width: 31%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">2026-03-10</td>
                        <td class="px-6 py-4 flex gap-1">
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-black rounded uppercase">Crítico</span>
                            <span class="px-2 py-1 bg-red-50 text-red-500 border border-red-100 text-[10px] font-black rounded uppercase">Vence</span>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-500">B-01</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
