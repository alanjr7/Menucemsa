@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-slate-50 min-h-screen">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="bg-blue-600 p-2 rounded-lg shadow-lg shadow-blue-100">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    Hospitalización
                </h1>
                <p class="text-slate-500 text-sm ml-12 italic uppercase font-bold tracking-wider">Gestión de camas y pacientes hospitalizados</p>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md flex items-center gap-2 transition-all">
                <span>+</span> Ingreso Hospitalario
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-col items-center text-center">
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Camas Ocupadas</p>
                <p class="text-2xl font-black text-slate-800">14 / 22</p>
                <p class="text-[10px] text-blue-600 font-bold">64% ocupación</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-col items-center text-center">
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Disponibles</p>
                <p class="text-2xl font-black text-green-600">8</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-col items-center text-center">
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Altas Programadas</p>
                <p class="text-2xl font-black text-blue-600">3</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-col items-center text-center">
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Promedio Estancia</p>
                <p class="text-2xl font-black text-slate-800">4.2 días</p>
            </div>
        </div>

        <div class="mb-10">
            <div class="flex items-center gap-4 mb-4">
                <h3 class="font-black text-slate-800 uppercase text-sm tracking-tighter">Piso 2 - Medicina General</h3>
                <div class="h-[1px] bg-slate-200 flex-1"></div>
                <span class="text-[10px] text-slate-400 font-bold uppercase">Ocupación: 85%</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-[2rem] border-l-4 border-l-red-500 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <span class="bg-slate-100 text-slate-800 font-black px-3 py-1 rounded-xl text-xs">H-201</span>
                                <div>
                                    <h4 class="font-bold text-slate-800">García, Juan - 45 años</h4>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">HC-001234 • Descompensación diabética</p>
                                </div>
                            </div>
                            <span class="text-[9px] font-black text-red-600 uppercase bg-red-50 px-2 py-1 rounded-full">Ocupada</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 mb-6">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>3 días de hospitalización</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl font-bold text-[10px] uppercase transition-all">Ver Historia</button>
                            <button class="py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-[10px] uppercase transition-all shadow-md shadow-blue-100">Dar Alta</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] border-l-4 border-l-green-500 shadow-sm border border-slate-100 flex flex-col items-center justify-center p-8 border-dashed group hover:border-green-400 transition-all cursor-pointer">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-slate-100 text-slate-400 font-black px-3 py-1 rounded-xl text-xs group-hover:bg-green-100 group-hover:text-green-600 transition-colors">H-202</span>
                        <h4 class="font-black text-slate-400 uppercase text-xs tracking-widest">Cama disponible</h4>
                    </div>
                    <button class="px-6 py-2 bg-slate-800 text-white rounded-xl font-black text-[10px] uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all">Asignar Paciente</button>
                </div>
            </div>
        </div>

        <div class="mb-10">
            <div class="flex items-center gap-4 mb-4">
                <h3 class="font-black text-slate-800 uppercase text-sm tracking-tighter">Piso 3 - Cirugía</h3>
                <div class="h-[1px] bg-slate-200 flex-1"></div>
            </div>
            </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center gap-2">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Altas Programadas - Hoy</h3>
            </div>
            <div class="divide-y divide-slate-50">
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 font-bold text-xs italic">ML</div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm">López, María</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">H-205 • Neumonía • 5 días</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 border border-slate-200 rounded-lg text-[10px] font-black uppercase text-slate-500 hover:bg-slate-50 transition-all">Ver Epicrisis</button>
                        <button class="px-4 py-2 bg-green-500 text-white rounded-lg text-[10px] font-black uppercase hover:bg-green-600 transition-all shadow-sm">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
