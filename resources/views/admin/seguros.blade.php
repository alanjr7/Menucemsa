@extends('layouts.app')

@section('content')
<div class="w-full p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

        <div class="flex justify-between items-start mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2"/></svg>
                    </div>
                    <h1 class="text-[26px] font-black text-slate-800 tracking-tight">Seguros y Preautorizaciones</h1>
                </div>
                <p class="text-slate-500 text-[15px] font-medium mt-1 ml-11">Gestión de autorizaciones y convenios</p>
            </div>
            <button class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-md transition-all">
                <span class="text-lg">+</span> Nueva Preautorización
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Pendientes</p>
                <p class="text-[#e67e22] text-[32px] font-black tracking-tighter">5</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">En Proceso</p>
                <p class="text-[#1c7ed6] text-[32px] font-black tracking-tighter">8</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Aprobadas</p>
                <p class="text-[#0ca678] text-[32px] font-black tracking-tighter">12</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Monto Total</p>
                <p class="text-slate-800 text-[32px] font-black tracking-tighter italic">S/ 85,200</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-[20px] border border-slate-100 shadow-sm mb-6 flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" placeholder="Buscar por número, paciente o seguro..." class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none transition-all placeholder:text-slate-300">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            </div>
            <button class="bg-white border border-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all flex items-center gap-2">
                Filtrar
            </button>
        </div>

        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-8">
            <div class="p-8 border-b border-slate-50">
                <h3 class="font-bold text-slate-800 text-lg">Preautorizaciones</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold tracking-widest border-b border-slate-50">
                        <tr>
                            <th class="px-10 py-5">Número</th>
                            <th class="px-10 py-5">Fecha</th>
                            <th class="px-10 py-5">Paciente</th>
                            <th class="px-10 py-5">Seguro</th>
                            <th class="px-10 py-5">Servicio</th>
                            <th class="px-10 py-5">Monto</th>
                            <th class="px-10 py-5">Estado</th>
                            <th class="px-10 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="px-10 py-6 font-bold text-slate-800">PRE-2026-001</td>
                            <td class="px-10 py-6 text-slate-500">2026-02-03</td>
                            <td class="px-10 py-6 text-slate-600 font-medium">García, Juan</td>
                            <td class="px-10 py-6 text-slate-600">Pacífico Seguros</td>
                            <td class="px-10 py-6 text-slate-600">Hospitalización</td>
                            <td class="px-10 py-6 font-medium text-slate-800">S/ 5000.00</td>
                            <td class="px-10 py-6">
                                <span class="bg-green-50 text-green-600 border border-green-100 px-3 py-1.5 rounded-lg text-[12px] font-bold flex items-center gap-1.5 w-fit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg> Aprobada
                                </span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <button class="bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-xl text-[12px] font-bold hover:bg-slate-50 shadow-sm">Ver Detalle</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="px-10 py-6 font-bold text-slate-800">PRE-2026-002</td>
                            <td class="px-10 py-6 text-slate-500">2026-02-03</td>
                            <td class="px-10 py-6 text-slate-600 font-medium">Torres, Ana</td>
                            <td class="px-10 py-6 text-slate-600">RIMAC Seguros</td>
                            <td class="px-10 py-6 text-slate-600">Cirugía de vesícula</td>
                            <td class="px-10 py-6 font-medium text-slate-800">S/ 8500.00</td>
                            <td class="px-10 py-6">
                                <span class="bg-orange-50 text-orange-600 border border-orange-100 px-3 py-1.5 rounded-lg text-[12px] font-bold flex items-center gap-1.5 w-fit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg> En Proceso
                                </span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <button class="bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-xl text-[12px] font-bold hover:bg-slate-50 shadow-sm">Ver Detalle</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="px-10 py-6 font-bold text-slate-800">PRE-2026-003</td>
                            <td class="px-10 py-6 text-slate-500">2026-02-02</td>
                            <td class="px-10 py-6 text-slate-600 font-medium">Mendoza, Luis</td>
                            <td class="px-10 py-6 text-slate-600">La Positiva</td>
                            <td class="px-10 py-6 text-slate-600">Resonancia magnética</td>
                            <td class="px-10 py-6 font-medium text-slate-800">S/ 1200.00</td>
                            <td class="px-10 py-6">
                                <span class="bg-blue-50 text-blue-600 border border-blue-100 px-3 py-1.5 rounded-lg text-[12px] font-bold flex items-center gap-1.5 w-fit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg> Pendiente
                                </span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <button class="bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-xl text-[12px] font-bold hover:bg-slate-50 shadow-sm">Ver Detalle</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-8">
            <h3 class="font-bold text-slate-800 text-lg mb-6">Convenios Activos</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-[20px] border border-slate-100 bg-white hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-slate-800 text-[16px]">Pacífico Seguros</h4>
                            <p class="text-slate-400 text-[13px] font-medium mt-1">145 pacientes afiliados</p>
                        </div>
                        <div class="text-blue-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="1.5"/></svg>
                        </div>
                    </div>
                    <p class="text-slate-500 text-[12px]">Vigencia: <span class="font-semibold">31/12/2026</span></p>
                </div>

                <div class="p-6 rounded-[20px] border border-slate-100 bg-white hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-slate-800 text-[16px]">RIMAC Seguros</h4>
                            <p class="text-slate-400 text-[13px] font-medium mt-1">198 pacientes afiliados</p>
                        </div>
                        <div class="text-blue-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="1.5"/></svg>
                        </div>
                    </div>
                    <p class="text-slate-500 text-[12px]">Vigencia: <span class="font-semibold">30/06/2027</span></p>
                </div>

                <div class="p-6 rounded-[20px] border border-slate-100 bg-white hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-slate-800 text-[16px]">La Positiva</h4>
                            <p class="text-slate-400 text-[13px] font-medium mt-1">87 pacientes afiliados</p>
                        </div>
                        <div class="text-blue-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="1.5"/></svg>
                        </div>
                    </div>
                    <p class="text-slate-500 text-[12px]">Vigencia: <span class="font-semibold">15/09/2026</span></p>
                </div>

                <div class="p-6 rounded-[20px] border border-slate-100 bg-white hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-slate-800 text-[16px]">Mapfre</h4>
                            <p class="text-slate-400 text-[13px] font-medium mt-1">112 pacientes afiliados</p>
                        </div>
                        <div class="text-blue-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="1.5"/></svg>
                        </div>
                    </div>
                    <p class="text-slate-500 text-[12px]">Vigencia: <span class="font-semibold">31/12/2026</span></p>
                </div>

                <div class="p-6 rounded-[20px] border border-slate-100 bg-white hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-slate-800 text-[16px]">EPS Pacifico</h4>
                            <p class="text-slate-400 text-[13px] font-medium mt-1">234 pacientes afiliados</p>
                        </div>
                        <div class="text-blue-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="1.5"/></svg>
                        </div>
                    </div>
                    <p class="text-slate-500 text-[12px]">Vigencia: <span class="font-semibold">31/03/2027</span></p>
                </div>

                <div class="p-6 rounded-[20px] border border-slate-100 bg-white hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-bold text-slate-800 text-[16px]">SIS (Convenio)</h4>
                            <p class="text-slate-400 text-[13px] font-medium mt-1">456 pacientes afiliados</p>
                        </div>
                        <div class="text-blue-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="1.5"/></svg>
                        </div>
                    </div>
                    <p class="text-slate-500 text-[12px]">Vigencia: <span class="font-semibold italic">Indefinido</span></p>
                </div>
            </div>
        </div>

    </div>
@endsection
