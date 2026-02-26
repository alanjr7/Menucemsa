<x-app-layout>
    <div class="min-h-screen bg-gray-50/50 p-6">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1 font-medium">Resumen general del sistema</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-start transition-transform hover:scale-[1.02]">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Ventas Hoy</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-2">$0.00</h3>
                    <p class="text-[11px] text-gray-400 mt-1 font-bold">0 transacciones</p>
                </div>
                <div class="p-3 bg-green-500 rounded-xl text-white shadow-lg shadow-green-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-start transition-transform hover:scale-[1.02]">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Productos en Stock</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-2">1225</h3>
                    <p class="text-[11px] text-gray-400 mt-1 font-bold">15 productos distintos</p>
                </div>
                <div class="p-3 bg-blue-500 rounded-xl text-white shadow-lg shadow-blue-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-start transition-transform hover:scale-[1.02]">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Alertas de Stock</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-2">0</h3>
                    <p class="text-[11px] text-gray-400 mt-1 font-bold">Requieren reabastecimiento</p>
                </div>
                <div class="p-3 bg-orange-500 rounded-xl text-white shadow-lg shadow-orange-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-start transition-transform hover:scale-[1.02]">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Ventas</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-2">0</h3>
                    <p class="text-[11px] text-gray-400 mt-1 font-bold">Historial completo</p>
                </div>
                <div class="p-3 bg-purple-500 rounded-xl text-white shadow-lg shadow-purple-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden min-h-[300px] flex flex-col">
                <div class="p-6 flex items-center justify-between">
                    <h2 class="font-black text-gray-800 text-lg">Alertas de Inventario</h2>
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-1 flex items-center justify-center p-6">
                    <p class="text-sm font-bold text-gray-400">No hay productos con stock bajo</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden min-h-[300px] flex flex-col">
                <div class="p-6 flex items-center justify-between">
                    <h2 class="font-black text-gray-800 text-lg">Últimas Ventas</h2>
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="flex-1 flex items-center justify-center p-6">
                    <p class="text-sm font-bold text-gray-400">No hay ventas registradas</p>
                </div>
            </div>
        </div>

    <div class="mb-4">
            <h2 class="font-black text-gray-800 text-xl tracking-tight">Accesos Rápidos</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <a href="{{ route('farmacia.pos') }}" class="bg-blue-600 p-8 rounded-2xl flex flex-col gap-4 text-white shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all active:scale-95 text-left group">
                <div class="p-3 bg-white/20 rounded-xl w-fit group-hover:bg-white/30 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="font-black text-xl uppercase tracking-tight">Nueva Venta</p>
                    <p class="text-xs text-blue-100 font-medium">Iniciar punto de venta</p>
                </div>
            </a>

            <a href="{{ route('farmacia.inventario') }}" class="bg-purple-600 p-8 rounded-2xl flex flex-col gap-4 text-white shadow-xl shadow-purple-200 hover:bg-purple-700 transition-all active:scale-95 text-left group">
                <div class="p-3 bg-white/20 rounded-xl w-fit group-hover:bg-white/30 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <p class="font-black text-xl uppercase tracking-tight">Inventario</p>
                    <p class="text-xs text-purple-100 font-medium">Gestionar productos</p>
                </div>
            </a>

            <a href="{{ route('farmacia.clientes') }}" class="bg-green-600 p-8 rounded-2xl flex flex-col gap-4 text-white shadow-xl shadow-green-200 hover:bg-green-700 transition-all active:scale-95 text-left group">
                <div class="p-3 bg-white/20 rounded-xl w-fit group-hover:bg-white/30 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="font-black text-xl uppercase tracking-tight">Clientes</p>
                    <p class="text-xs text-green-100 font-medium">Administrar clientes</p>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
