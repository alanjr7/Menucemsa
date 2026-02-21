@extends('layouts.app')

@section('content')
<div x-data="{ tab: 'servicios' }" class="w-full p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-[26px] font-black text-slate-800 tracking-tight">Tarifarios y Precios</h1>
                <p class="text-slate-500 text-[15px] font-medium mt-1">Gestión de precios por servicio y tipo de seguro</p>
            </div>
            <button class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-md transition-all">
                <span class="text-lg">+</span> Nuevo Servicio
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-2">Total Servicios</p>
                <p class="text-slate-800 text-[32px] font-black tracking-tighter">248</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-2">Consultas</p>
                <p class="text-[#1c7ed6] text-[32px] font-black tracking-tighter">45</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-2">Procedimientos</p>
                <p class="text-[#0ca678] text-[32px] font-black tracking-tighter">128</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-2">Cirugías</p>
                <p class="text-[#be4bdb] text-[32px] font-black tracking-tighter">75</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-[20px] border border-slate-100 shadow-sm mb-6 flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" placeholder="Buscar por código o descripción..." class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none transition-all">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            </div>
            <button class="bg-white border border-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50">Exportar</button>
        </div>

        <div class="bg-[#e9ecef] p-1.5 rounded-2xl flex w-full mb-8">
            <button @click="tab = 'servicios'" :class="tab === 'servicios' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600'" class="flex-1 py-3 rounded-[14px] text-sm font-bold transition-all">Servicios y Consultas</button>
            <button @click="tab = 'procedimientos'" :class="tab === 'procedimientos' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600'" class="flex-1 py-3 rounded-[14px] text-sm font-bold transition-all">Procedimientos</button>
            <button @click="tab = 'cirugias'" :class="tab === 'cirugias' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600'" class="flex-1 py-3 rounded-[14px] text-sm font-bold transition-all">Cirugías</button>
        </div>

        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden">

            <div x-show="tab === 'servicios'">
                <div class="p-8 border-b border-slate-50"><h3 class="font-bold text-slate-800 text-lg">Tarifario de Servicios y Consultas</h3></div>
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold border-b border-slate-50">
                        <tr><th class="px-10 py-5">Código</th><th class="px-10 py-5">Descripción</th><th class="px-10 py-5">Particular (S/)</th><th class="px-10 py-5">SIS (S/)</th><th class="px-10 py-5">EPS (S/)</th><th class="px-10 py-5 text-right">Acciones</th></tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        <tr><td class="px-10 py-6 font-bold">CONS-001</td><td class="px-10 py-6">Consulta Medicina General</td><td class="px-10 py-6">150.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">120.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                        <tr><td class="px-10 py-6 font-bold">CONS-002</td><td class="px-10 py-6">Consulta Especializada</td><td class="px-10 py-6">200.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">160.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                        <tr><td class="px-10 py-6 font-bold">LAB-001</td><td class="px-10 py-6">Hemograma Completo</td><td class="px-10 py-6">45.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">38.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                        <tr><td class="px-10 py-6 font-bold">LAB-002</td><td class="px-10 py-6">Perfil Lipídico</td><td class="px-10 py-6">85.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">72.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                        <tr><td class="px-10 py-6 font-bold">IMG-001</td><td class="px-10 py-6">Radiografía de Tórax</td><td class="px-10 py-6">120.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">100.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                    </tbody>
                </table>
            </div>

            <div x-show="tab === 'procedimientos'" x-cloak>
                <div class="p-8 border-b border-slate-50"><h3 class="font-bold text-slate-800 text-lg">Tarifario de Procedimientos</h3></div>
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold border-b border-slate-50">
                        <tr><th class="px-10 py-5">Código</th><th class="px-10 py-5">Descripción</th><th class="px-10 py-5">Particular (S/)</th><th class="px-10 py-5">SIS (S/)</th><th class="px-10 py-5">EPS (S/)</th><th class="px-10 py-5 text-right">Acciones</th></tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        <tr><td class="px-10 py-6 font-bold">PROC-001</td><td class="px-10 py-6">Sutura simple</td><td class="px-10 py-6">180.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">150.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                        <tr><td class="px-10 py-6 font-bold">PROC-002</td><td class="px-10 py-6">Extracción de uña</td><td class="px-10 py-6">220.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">185.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                        <tr><td class="px-10 py-6 font-bold">PROC-003</td><td class="px-10 py-6">Drenaje de absceso</td><td class="px-10 py-6">280.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">235.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                    </tbody>
                </table>
            </div>

            <div x-show="tab === 'cirugias'" x-cloak>
                <div class="p-8 border-b border-slate-50"><h3 class="font-bold text-slate-800 text-lg">Tarifario de Cirugías</h3></div>
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold border-b border-slate-50">
                        <tr><th class="px-10 py-5">Código</th><th class="px-10 py-5">Descripción</th><th class="px-10 py-5">Particular (S/)</th><th class="px-10 py-5">SIS (S/)</th><th class="px-10 py-5">EPS (S/)</th><th class="px-10 py-5 text-right">Acciones</th></tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        <tr><td class="px-10 py-6 font-bold">CIR-001</td><td class="px-10 py-6">Apendicectomía</td><td class="px-10 py-6">3500.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">2800.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                        <tr><td class="px-10 py-6 font-bold">CIR-002</td><td class="px-10 py-6">Colecistectomía</td><td class="px-10 py-6">4200.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">3400.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                        <tr><td class="px-10 py-6 font-bold">CIR-003</td><td class="px-10 py-6">Hernia inguinal</td><td class="px-10 py-6">3800.00</td><td class="px-10 py-6 text-slate-400 italic">Convenio</td><td class="px-10 py-6">3000.00</td><td class="px-10 py-6 text-right"><button class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold">Editar</button></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
