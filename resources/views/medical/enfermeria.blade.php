@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50 min-h-screen" x-data="{ tab: 'pacientes' }">

        <div class="mb-6">
            <div class="flex items-center gap-3 text-teal-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <h1 class="text-2xl font-bold text-gray-800">Enfermería</h1>
            </div>
            <p class="text-sm text-gray-500 ml-11 font-medium">Lic. María García - Turno: Tarde (14:00 - 22:00)</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center text-gray-800">
                <div><p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Pacientes Asignados</p><p class="text-3xl font-black">3</p></div>
                <div class="text-teal-500"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div><p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tareas Completadas</p><p class="text-3xl font-black text-green-600">2/5</p></div>
                <div class="text-green-500"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div><p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Alta Prioridad</p><p class="text-3xl font-black text-red-600">1</p></div>
                <div class="text-red-500"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center text-gray-800">
                <div><p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Próxima Tarea</p><p class="text-2xl font-black">14:30</p></div>
                <div class="text-blue-500"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            </div>
        </div>

        <div class="bg-gray-200/50 p-1 rounded-xl flex mb-6 w-full max-w-4xl mx-auto shadow-inner">
            <button @click="tab = 'pacientes'" :class="tab === 'pacientes' ? 'bg-white shadow-sm text-gray-700' : 'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all">Mis Pacientes</button>
            <button @click="tab = 'tareas'" :class="tab === 'tareas' ? 'bg-white shadow-sm text-gray-700' : 'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all">Tareas del Turno</button>
            <button @click="tab = 'signos'" :class="tab === 'signos' ? 'bg-white shadow-sm text-gray-700' : 'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all">Registro de Signos</button>
        </div>

        <div x-show="tab === 'pacientes'" class="space-y-4 max-w-6xl mx-auto">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="px-3 py-1 bg-teal-50 text-teal-600 font-bold rounded-lg text-sm border border-teal-100">H-201</div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h4 class="font-bold text-gray-800 text-lg">García, Juan</h4>
                            <span class="px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-bold rounded uppercase flex items-center gap-1">
                                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span> Alta Prioridad
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 font-medium">HC-001234</p>
                        <p class="text-sm text-gray-600 mt-1"><strong>Diagnóstico:</strong> Descompensación diabética</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition shadow-md shadow-blue-100">Ver Historia</button>
                    <button @click="tab = 'signos'" class="px-5 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition">Registrar</button>
                </div>
            </div>
            </div>

        <div x-show="tab === 'tareas'" x-cloak class="max-w-6xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Cronograma de Tareas - Turno Tarde</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-4 bg-green-50/50 rounded-xl border border-green-100 text-gray-400">
                    <div class="flex items-center gap-4 text-sm font-medium"><span class="font-bold">14:00</span> <p class="line-through">Signos vitales - García, Juan (H-201)</p></div>
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100">
                    <div class="flex items-center gap-4 text-sm font-medium text-gray-700"><span class="font-bold">14:30</span> <p>Curación post-operatoria - Sánchez, Carmen (H-203)</p></div>
                    <button class="px-5 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold shadow-md shadow-blue-50">Completar</button>
                </div>
            </div>
        </div>

        <div x-show="tab === 'signos'" x-cloak class="max-w-6xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-800">Registro de Signos Vitales</h3>
                <p class="text-sm text-gray-400 mt-1">Paciente: <span class="font-semibold text-gray-600">García, Juan - HC-001234 - Cama H-201</span></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Presión Arterial (mmHg)</label>
                        <div class="flex items-center gap-3">
                            <input type="text" placeholder="Sistólica" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-teal-500">
                            <span class="text-gray-400 font-bold">/</span>
                            <input type="text" placeholder="Diastólica" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-teal-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Frecuencia Respiratoria (rpm)</label>
                        <input type="text" placeholder="18" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Saturación O₂ (%)</label>
                        <input type="text" placeholder="98" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-teal-500">
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Frecuencia Cardíaca (lpm)</label>
                        <input type="text" placeholder="75" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Temperatura (°C)</label>
                        <input type="text" placeholder="36.5" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Glucosa (mg/dL)</label>
                        <input type="text" placeholder="120" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-teal-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-10">
                <button @click="tab = 'pacientes'" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition">Cancelar</button>
                <button class="px-6 py-2 bg-emerald-500 text-white rounded-xl text-sm font-bold hover:bg-emerald-600 transition flex items-center gap-2 shadow-lg shadow-emerald-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Guardar Signos Vitales
                </button>
            </div>

            <div class="mt-12">
                <h4 class="text-sm font-bold text-gray-800 mb-4 tracking-tight">Historial del Día</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                        <span class="text-sm font-bold text-gray-700">14:00</span>
                        <div class="flex gap-6 text-[11px] font-bold text-gray-500 uppercase tracking-tighter">
                            <span>PA: 145/90</span> <span>FC: 88</span> <span>T°: 36.8</span> <span>Sat: 96%</span> <span>Glucosa: 245</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 opacity-60">
                        <span class="text-sm font-bold text-gray-700">10:00</span>
                        <div class="flex gap-6 text-[11px] font-bold text-gray-500 uppercase tracking-tighter">
                            <span>PA: 150/95</span> <span>FC: 92</span> <span>T°: 37.0</span> <span>Sat: 95%</span> <span>Glucosa: 280</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
