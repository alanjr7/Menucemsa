<x-app-layout>
    <div class="p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-[28px] font-black text-slate-800 tracking-tight">Caja Central</h1>
                <p class="text-slate-500 text-[15px] font-medium">Gestión de cobros y movimientos de caja</p>
            </div>
            <div class="flex gap-3">
                <button class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm hover:bg-slate-50 transition-all">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Imprimir Cierre
                </button>
                <button class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-lg shadow-blue-100 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Nuevo Cobro
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50 relative overflow-hidden">
                <p class="text-slate-400 text-sm font-bold mb-4">Ingresos del Día</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[#00a65a] text-3xl font-black tracking-tighter">S/ {{ number_format($resumen['ingresos'], 2) }}</p>
                        <p class="text-slate-400 text-[11px] font-bold mt-1">{{ $resumen['ingresos_count'] }} transacciones</p>
                    </div>
                    <svg class="w-10 h-10 text-[#00a65a]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50">
                <p class="text-slate-400 text-sm font-bold mb-4">Egresos del Día</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[#f03e3e] text-3xl font-black tracking-tighter">S/ {{ number_format($resumen['egresos'], 2) }}</p>
                        <p class="text-slate-400 text-[11px] font-bold mt-1">{{ $resumen['egresos_count'] }} transacciones</p>
                    </div>
                    <span class="text-[#f03e3e] text-4xl font-light">$</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50">
                <p class="text-slate-400 text-sm font-bold mb-4">Saldo en Caja</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[#1c7ed6] text-3xl font-black tracking-tighter">S/ {{ number_format($resumen['saldo'], 2) }}</p>
                        <p class="text-slate-400 text-[11px] font-bold mt-1">Actualizado</p>
                    </div>
                    <span class="text-[#1c7ed6] text-4xl font-light">$</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50">
                <p class="text-slate-400 text-sm font-bold mb-4">Pendientes</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[#f39c12] text-3xl font-black tracking-tighter">S/ {{ number_format($resumen['pendientes_monto'], 2) }}</p>
                        <p class="text-slate-400 text-[11px] font-bold mt-1">{{ $resumen['pendientes_count'] }} pendientes</p>
                    </div>
                    <svg class="w-10 h-10 text-[#f39c12]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-8 flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" placeholder="Buscar por paciente, historia clínica o número de recibo..." class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none transition-all">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            </div>
            <button class="bg-slate-50 text-slate-600 px-8 py-2.5 rounded-xl text-sm font-bold border border-slate-200 hover:bg-slate-100 transition-all">Filtrar</button>
        </div>

        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-slate-50">
                <h3 class="font-bold text-slate-700 text-lg">Movimientos del Día - {{ $fecha->format('d/m/Y') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[12px] uppercase font-bold tracking-wider">
                        <tr class="border-b border-slate-50">
                            <th class="px-8 py-5">Hora</th>
                            <th class="px-8 py-5">Tipo</th>
                            <th class="px-8 py-5">Concepto</th>
                            <th class="px-8 py-5">Paciente</th>
                            <th class="px-8 py-5">Monto</th>
                            <th class="px-8 py-5">Estado</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        @forelse ($movimientos as $movimiento)
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-8 py-5 font-bold text-slate-700">{{ optional($movimiento->FECHA)->format('H:i') ?? '—' }}</td>
                                <td class="px-8 py-5">
                                    @if ($movimiento->TOTAL >= 0)
                                        <span class="bg-[#e6fcf5] text-[#0ca678] px-3 py-1 rounded-md text-[11px] font-bold">Ingreso</span>
                                    @else
                                        <span class="bg-[#fff5f5] text-[#f03e3e] px-3 py-1 rounded-md text-[11px] font-bold">Egreso</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-slate-600">{{ $movimiento->DETALLE ?? 'Sin detalle' }}</td>
                                <td class="px-8 py-5 text-slate-500 font-medium">{{ $movimiento->CODIGO }}</td>
                                <td class="px-8 py-5 font-bold {{ $movimiento->TOTAL >= 0 ? 'text-[#0ca678]' : 'text-[#f03e3e]' }}">S/ {{ number_format(abs((float) $movimiento->TOTAL), 2) }}</td>
                                <td class="px-8 py-5">
                                    <span class="bg-[#e7f5ff] text-[#1c7ed6] px-3 py-1 rounded-full text-[11px] font-bold border border-[#d0ebff]">Registrado</span>
                                </td>
                                <td class="px-8 py-5 text-right text-slate-400 text-xs">-</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-8 text-center text-slate-400">No hay movimientos registrados para hoy.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[24px] border border-slate-100 shadow-sm">
            <h3 class="font-bold text-slate-700 mb-6 text-lg">Resumen por Forma de Pago</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#fcfdfe] border border-slate-100 p-6 rounded-[20px] flex justify-between items-center">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase mb-2">Efectivo</p>
                        <p class="text-slate-800 text-2xl font-black">S/ {{ number_format($metodosPago['EFECTIVO'] ?? 0, 2) }}</p>
                    </div>
                    <span class="text-[#40c057] text-4xl font-light">$</span>
                </div>
                <div class="bg-[#fcfdfe] border border-slate-100 p-6 rounded-[20px] flex justify-between items-center">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase mb-2">Tarjeta</p>
                        <p class="text-slate-800 text-2xl font-black">S/ {{ number_format($metodosPago['TARJETA'] ?? 0, 2) }}</p>
                    </div>
                    <svg class="w-10 h-10 text-[#228be6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" stroke-width="2"/></svg>
                </div>
                <div class="bg-[#fcfdfe] border border-slate-100 p-6 rounded-[20px] flex justify-between items-center">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase mb-2">Transferencia</p>
                        <p class="text-slate-800 text-2xl font-black">S/ {{ number_format($metodosPago['TRANSFERENCIA'] ?? 0, 2) }}</p>
                    </div>
                    <svg class="w-10 h-10 text-[#be4bdb]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" stroke-width="2"/></svg>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
