<x-app-layout>
    <div class="p-6 bg-slate-50 min-h-screen">

        <div class="mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <h1 class="text-2xl font-bold text-slate-800">Unidad de Terapia Intensiva (UTI)</h1>
            </div>
            <p class="text-slate-500 text-sm ml-11">Monitoreo continuo de pacientes críticos</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-xs font-bold uppercase mb-2">Camas Ocupadas</p>
                <p class="text-4xl font-black text-slate-800">3/6</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-xs font-bold uppercase mb-2">Pacientes Críticos</p>
                <p class="text-4xl font-black text-red-600">2</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-xs font-bold uppercase mb-2">Ventiladores Activos</p>
                <p class="text-4xl font-black text-slate-800">2</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-xs font-bold uppercase mb-2">Con Sedación</p>
                <p class="text-4xl font-black text-slate-800">2</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-xs font-bold uppercase mb-2">Promedio Estancia</p>
                <p class="text-4xl font-black text-slate-800">4 días</p>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden border-l-[6px] border-l-red-500">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-8">
                        <div class="flex gap-5">
                            <div class="bg-blue-50 text-blue-600 font-black px-4 py-3 rounded-2xl text-xl flex items-center justify-center border border-blue-100 italic">UTI-01</div>
                            <div>
                                <h3 class="text-2xl font-bold text-slate-800">García, Juan - 45 años</h3>
                                <p class="text-slate-400 text-sm font-bold uppercase">HC-001234</p>
                                <p class="text-slate-600 text-md font-medium mt-1 italic">Insuficiencia respiratoria aguda</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <span class="bg-red-50 text-red-600 text-xs font-black px-4 py-2 rounded-xl border border-red-100 flex items-center gap-2">
                                <span class="w-2 h-2 bg-red-600 rounded-full animate-pulse"></span> Estado Crítico
                            </span>
                            <span class="bg-blue-50 text-blue-600 text-xs font-black px-4 py-2 rounded-xl border border-blue-100">3 días</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                        <div>
                            <p class="text-slate-800 font-black text-sm mb-6 flex items-center gap-2 italic uppercase">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" stroke-width="2.5"/></svg>
                                Signos Vitales - Tiempo Real
                            </p>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-red-500 uppercase flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> P.A.</p>
                                    <p class="text-2xl font-black text-slate-800 mt-1">110/70</p>
                                    <p class="text-[10px] text-slate-400 font-bold">PAM: 83</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-blue-500 uppercase flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/></svg> F.C.</p>
                                    <p class="text-2xl font-black text-slate-800 mt-1">95 lpm</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-cyan-500 uppercase flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 10h16M7 7h10" stroke-width="2"/></svg> F.R.</p>
                                    <p class="text-2xl font-black text-slate-800 mt-1">18 rpm</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-orange-500 uppercase">Temp.</p>
                                    <p class="text-2xl font-black text-slate-800 mt-1">37.5 °C</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-green-500 uppercase flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71L12 2z"/></svg> Sat O₂</p>
                                    <p class="text-2xl font-black text-green-500 mt-1">92%</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-slate-800 font-black text-sm mb-6 uppercase italic">Soporte Vital</p>
                            <div class="flex items-center justify-between p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 10h16M7 7h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                                    <div><p class="text-sm font-bold text-slate-700 leading-none mb-1">Ventilación Mecánica</p><p class="text-[10px] text-slate-400 font-bold uppercase">Modo: SIMV | FiO₂: 60% | PEEP: 8 cmH₂O</p></div>
                                </div>
                                <span class="bg-blue-600 text-white text-[10px] font-black px-3 py-1 rounded-lg uppercase">Activo</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-purple-50/50 rounded-2xl border border-purple-100">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-width="2"/></svg>
                                    <div><p class="text-sm font-bold text-slate-700 leading-none mb-1">Sedación</p><p class="text-[10px] text-slate-400 font-bold uppercase">Ramsay: 4 | Midazolam 3mg/h</p></div>
                                </div>
                                <span class="bg-orange-400 text-white text-[10px] font-black px-3 py-1 rounded-lg uppercase">Activa</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-green-50/50 rounded-2xl border border-green-100">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/></svg>
                                    <p class="text-sm font-bold text-slate-700">Vía Central</p>
                                </div>
                                <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-3 py-1 rounded-lg uppercase">Yugular</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden border-l-[6px] border-l-red-500">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-8">
                        <div class="flex gap-5">
                            <div class="bg-blue-50 text-blue-600 font-black px-4 py-3 rounded-2xl text-xl flex items-center justify-center border border-blue-100 italic">UTI-02</div>
                            <div>
                                <h3 class="text-2xl font-bold text-slate-800">López, María - 62 años</h3>
                                <p class="text-slate-400 text-sm font-bold uppercase">HC-001245</p>
                                <p class="text-slate-600 text-md font-medium mt-1 italic">Shock séptico</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <span class="bg-red-50 text-red-600 text-xs font-black px-4 py-2 rounded-xl border border-red-100 flex items-center gap-2">
                                <span class="w-2 h-2 bg-red-600 rounded-full animate-pulse"></span> Estado Crítico
                            </span>
                            <span class="bg-blue-50 text-blue-600 text-xs font-black px-4 py-2 rounded-xl border border-blue-100">7 días</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                        <div>
                            <p class="text-slate-800 font-black text-sm mb-6 flex items-center gap-2 italic uppercase">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" stroke-width="2.5"/></svg>
                                Signos Vitales - Tiempo Real
                            </p>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-red-500 uppercase flex items-center gap-1">P.A.</p>
                                    <p class="text-2xl font-black text-slate-800 mt-1">95/60</p>
                                    <p class="text-[10px] text-slate-400 font-bold">PAM: 72</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-blue-500 uppercase">F.C.</p>
                                    <p class="text-2xl font-black text-slate-800 mt-1">115 lpm</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-cyan-500 uppercase">F.R.</p>
                                    <p class="text-2xl font-black text-slate-800 mt-1">22 rpm</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-orange-500 uppercase">Temp.</p>
                                    <p class="text-2xl font-black text-slate-800 mt-1">38.2 °C</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-red-500 uppercase italic">Sat O₂</p>
                                    <p class="text-2xl font-black text-red-500 mt-1 animate-pulse">89%</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-slate-800 font-black text-sm mb-6 uppercase italic">Soporte Vital</p>
                            <div class="flex items-center justify-between p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 10h16" stroke-width="2"/></svg>
                                    <div><p class="text-sm font-bold text-slate-700 leading-none mb-1">Ventilación Mecánica</p><p class="text-[10px] text-slate-400 font-bold uppercase">Modo: SIMV | FiO₂: 60% | PEEP: 8 cmH₂O</p></div>
                                </div>
                                <span class="bg-blue-600 text-white text-[10px] font-black px-3 py-1 rounded-lg uppercase">Activo</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-purple-50/50 rounded-2xl border border-purple-100">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10" stroke-width="2"/></svg>
                                    <div><p class="text-sm font-bold text-slate-700 leading-none mb-1">Sedación</p><p class="text-[10px] text-slate-400 font-bold uppercase">Ramsay: 4 | Midazolam 3mg/h</p></div>
                                </div>
                                <span class="bg-orange-400 text-white text-[10px] font-black px-3 py-1 rounded-lg uppercase">Activa</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-green-50/50 rounded-2xl border border-green-100">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3" stroke-width="2"/></svg>
                                    <p class="text-sm font-bold text-slate-700">Vía Central</p>
                                </div>
                                <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-3 py-1 rounded-lg uppercase">Yugular</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 mt-12">
                <p class="text-slate-800 font-black mb-6 flex items-center gap-3 uppercase text-sm italic">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5"/></svg>
                    Alertas del Sistema
                </p>
                <div class="space-y-3">
                    <div class="bg-red-50 text-red-700 text-sm p-5 rounded-2xl border border-red-100 font-medium flex items-center gap-3">
                        <span class="text-xl">⚠️</span> UTI-02: Saturación de O₂ baja (89%) - Revisar parámetros ventilatorios
                    </div>
                    <div class="bg-orange-50 text-orange-700 text-sm p-5 rounded-2xl border border-orange-100 font-medium flex items-center gap-3">
                        <span class="text-xl">⚠️</span> UTI-02: Temperatura elevada (38.2°C) - Considerar cultivos
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
