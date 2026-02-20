<x-app-layout>
    <div class="p-6 bg-slate-50 min-h-screen font-sans">

        <div class="mb-8 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="bg-blue-600 p-3 rounded-2xl shadow-lg shadow-blue-100/50">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    Centro Quirúrgico
                </h1>
                <p class="text-slate-500 text-sm ml-14 mt-2 font-medium tracking-wide uppercase">Monitoreo y gestión de salas en tiempo real</p>
            </div>

            <div>
                <button class="group bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nueva Cirugía
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">

            <div class="bg-white rounded-[2rem] shadow-sm hover:shadow-md transition-shadow border border-slate-100 overflow-hidden relative">
                <div class="px-6 py-4 bg-blue-50/40 border-b border-blue-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <span class="bg-blue-600 text-white font-black px-4 py-1.5 rounded-xl text-sm shadow-sm">QX-01</span>
                        <h3 class="font-bold text-slate-800 text-lg">Apendicectomía</h3>
                    </div>
                    <div class="flex items-center gap-2 bg-green-100 text-green-700 px-4 py-1.5 rounded-full font-bold text-xs uppercase tracking-wider">
                        <span class="relative flex h-2.5 w-2.5">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-600"></span>
                        </span>
                        En Proceso
                    </div>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1 tracking-wider">Paciente</p>
                            <p class="font-bold text-slate-700 text-base">Fernández, Roberto</p>
                            <p class="text-xs text-slate-400">HC-45029</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1 tracking-wider">Cirujano Principal</p>
                            <p class="font-bold text-slate-700 text-base">Dr. Silva, Jorge</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1 tracking-wider">Hora Inicio</p>
                            <p class="font-bold text-slate-700 text-lg">14:00 <span class="text-xs text-slate-400">hrs</span></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1 tracking-wider">Tiempo Transcurrido</p>
                            <p class="font-black text-blue-600 text-2xl font-mono">00:45:12</p>
                        </div>
                    </div>
                    <div class="mt-8">
                        <button class="w-full py-4 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-bold text-sm uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            Ver Monitor Quirúrgico
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm hover:shadow-md transition-shadow border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 bg-orange-50/40 border-b border-orange-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <span class="bg-slate-700 text-white font-black px-4 py-1.5 rounded-xl text-sm shadow-sm">QX-02</span>
                        <h3 class="font-bold text-slate-800 text-lg">Colecistectomía</h3>
                    </div>
                    <div class="flex items-center gap-2 bg-orange-100 text-orange-700 px-4 py-1.5 rounded-full font-bold text-xs uppercase tracking-wider">
                        En Preparación
                    </div>
                </div>
                <div class="p-8">
                    <div class="flex items-center gap-4 p-5 bg-orange-50 rounded-3xl border border-orange-200/60 mb-8">
                        <div class="bg-orange-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-orange-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-orange-800">Preparando Sala</h4>
                            <p class="text-sm text-orange-700/80 leading-tight mt-1">Personal de enfermería realizando asepsia y conteo de instrumental.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1 tracking-wider">Próximo Paciente</p>
                            <p class="font-bold text-slate-700 text-base">Torres, Ana</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1 tracking-wider">Hora Programada</p>
                            <p class="font-bold text-slate-700 text-lg">16:30 <span class="text-xs text-slate-400">hrs</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border-[3px] border-dashed border-slate-300 hover:border-blue-400 transition-colors bg-white/50 flex flex-col items-center justify-center p-12 cursor-pointer group">
                <div class="bg-slate-100 p-6 rounded-3xl mb-6 group-hover:bg-blue-50 group-hover:scale-110 transition-all">
                    <span class="text-4xl font-black text-slate-400 group-hover:text-blue-600 tracking-tight">QX-03</span>
                </div>
                <h3 class="text-slate-600 font-bold text-lg mb-2">Quirófano Disponible</h3>
                <p class="text-slate-400 text-sm mb-8 text-center">Listo para asignación inmediata</p>

                <button class="px-8 py-3 bg-white border-2 border-slate-200 rounded-2xl font-bold text-slate-600 uppercase tracking-widest text-sm group-hover:bg-blue-600 group-hover:text-white group-hover:border-transparent transition-all shadow-sm">
                    Asignar Cirugía Ahora
                </button>
            </div>

            <div class="bg-slate-100 rounded-[2rem] border border-slate-200 p-12 relative overflow-hidden flex flex-col items-center justify-center text-center opacity-80">
                <div class="absolute top-0 right-0 -mt-2 -mr-16 w-48 h-8 bg-red-500 transform rotate-45 flex items-center justify-center shadow-sm">
                    <span class="text-white text-[10px] font-black uppercase tracking-widest">Mantenimiento</span>
                </div>

                <div class="bg-slate-200 p-5 rounded-full shadow-inner mb-6">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                </div>
                <h3 class="font-black text-slate-600 text-xl mb-2">QX-04 Fuera de Servicio</h3>
                <p class="text-sm text-slate-500 font-medium max-w-xs mx-auto leading-relaxed">Programado para revisión técnica de equipos de anestesia hasta las 20:00 hrs.</p>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-100 flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <h3 class="font-bold text-slate-800 text-lg">Cirugías Programadas - Hoy</h3>
            </div>
            <div class="divide-y divide-slate-50">
                <div class="px-8 py-5 flex items-center justify-between hover:bg-slate-50/80 transition-colors group">
                    <div class="flex items-start gap-6">
                        <div class="font-mono text-xl font-black text-slate-400 group-hover:text-blue-600 transition-colors">16:30</div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-base mb-1">Colecistectomía Laparoscópica</h4>
                            <div class="flex items-center gap-4 text-sm font-medium text-slate-500">
                                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Torres, Ana</span>
                                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg> Dr. Ramírez</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                         <span class="bg-slate-100 text-slate-600 font-bold px-3 py-1 rounded-lg text-xs uppercase tracking-wider">QX-02</span>
                         <button class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-full transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                         </button>
                    </div>
                </div>
                <div class="px-8 py-5 flex items-center justify-between hover:bg-slate-50/80 transition-colors group">
                    <div class="flex items-start gap-6">
                        <div class="font-mono text-xl font-black text-slate-400 group-hover:text-blue-600 transition-colors">18:00</div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-base mb-1">Hernioplastia Inguinal</h4>
                            <div class="flex items-center gap-4 text-sm font-medium text-slate-500">
                                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Mendoza, Luis</span>
                                <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg> Dra. Herrera</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                         <span class="bg-slate-100 text-slate-600 font-bold px-3 py-1 rounded-lg text-xs uppercase tracking-wider">QX-01</span>
                         <button class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-full transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                         </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
