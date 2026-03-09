@extends('layouts.app')

@section('content')
<div class="w-full p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-[26px] font-black text-slate-800 tracking-tight">Cuentas por Cobrar</h1>
                <p class="text-slate-500 text-[15px] font-medium mt-1">Seguimiento de cobros y morosidad</p>
            </div>
            <button class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-md transition-all">
                <span class="text-lg">$</span> Registrar Pago
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 flex flex-col justify-center items-center relative overflow-hidden">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Total por Cobrar</p>
                <div class="flex items-baseline gap-2">
                    <p class="text-slate-800 text-[28px] font-black tracking-tighter">S/ 19200.00</p>
                    <span class="text-blue-500 text-2xl font-bold">$</span>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 flex flex-col justify-center items-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Vencidas</p>
                <div class="flex items-center gap-3">
                    <p class="text-red-600 text-[28px] font-black tracking-tighter">S/ 11700.00</p>
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Cuentas Activas</p>
                <p class="text-slate-800 text-[32px] font-black tracking-tighter">4</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Índice Morosidad</p>
                <p class="text-orange-500 text-[32px] font-black tracking-tighter">60.9%</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-[20px] border border-slate-100 shadow-sm mb-6 flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" placeholder="Buscar por número, paciente o empresa..." class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            </div>
            <select class="bg-white border border-slate-200 text-slate-600 px-4 py-2.5 rounded-xl text-sm font-bold outline-none">
                <option>Todas</option>
                <option>Vencidas</option>
            </select>
            <button class="bg-white border border-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all">Exportar</button>
        </div>

        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-8">
            <div class="p-8 border-b border-slate-50"><h3 class="font-bold text-slate-800 text-lg">Relación de Cuentas por Cobrar</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold border-b border-slate-50">
                        <tr>
                            <th class="px-8 py-5">Número</th><th class="px-8 py-5">Cliente</th><th class="px-8 py-5">Fecha</th><th class="px-8 py-5">Vencimiento</th><th class="px-8 py-5">Monto Total</th><th class="px-8 py-5">Saldo</th><th class="px-8 py-5">Estado</th><th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        <tr>
                            <td class="px-8 py-6 font-bold">CC-2026-001</td><td class="px-8 py-6">García, Juan</td><td class="px-8 py-6">2026-01-15</td><td class="px-8 py-6">2026-02-15</td><td class="px-8 py-6 font-medium">S/ 2500.00</td><td class="px-8 py-6 font-bold text-slate-800">S/ 2500.00</td>
                            <td class="px-8 py-6"><span class="bg-green-100 text-green-600 px-3 py-1 rounded-lg text-xs font-bold border border-green-200">✓ Vigente</span></td>
                            <td class="px-8 py-6 text-right space-x-2"><button class="border border-slate-200 px-4 py-2 rounded-xl text-xs font-bold">Ver Detalle</button><button class="bg-[#0061df] text-white px-4 py-2 rounded-xl text-xs font-bold">Registrar Pago</button></td>
                        </tr>
                        <tr>
                            <td class="px-8 py-6 font-bold">CC-2026-002</td><td class="px-8 py-6 text-slate-600">Empresa ABC SAC</td><td class="px-8 py-6">2026-01-05</td><td class="px-8 py-6">2026-02-05</td><td class="px-8 py-6 font-medium">S/ 8500.00</td><td class="px-8 py-6 font-bold text-slate-800">S/ 8500.00</td>
                            <td class="px-8 py-6"><span class="bg-orange-50 text-orange-600 px-3 py-1 rounded-lg text-xs font-bold border border-orange-100">⚠ Vencido 2d</span></td>
                            <td class="px-8 py-6 text-right space-x-2"><button class="border border-slate-200 px-4 py-2 rounded-xl text-xs font-bold">Ver Detalle</button><button class="bg-[#0061df] text-white px-4 py-2 rounded-xl text-xs font-bold">Registrar Pago</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                <h3 class="font-bold text-slate-800 text-lg">Alertas de Morosidad</h3>
            </div>
            <div class="space-y-4">
                <div class="bg-red-50 border border-red-100 p-5 rounded-2xl flex justify-between items-center">
                    <p class="text-red-800 font-medium">Cuenta vencida +30 días: <span class="font-bold text-red-900">CC-2025-145 - López, María - S/ 3,200.00</span></p>
                </div>
                <div class="bg-orange-50 border border-orange-100 p-5 rounded-2xl">
                    <p class="text-orange-800 font-medium">Vence hoy: <span class="font-bold text-orange-900">3 cuentas por S/ 12,450.00</span></p>
                </div>
            </div>
        </div>
    </div>
@endsection
