<x-app-layout>
    <div x-data="{ tab: 'agenda' }" class="p-6">

        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Consulta Externa</h1>
                <p class="text-sm text-gray-500">Dr. Carlos Mendoza - Medicina General</p>
            </div>
            <div class="flex gap-3">
                <button class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Ver Agenda
                </button>
                <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-md transition">
                    <span class="text-lg mr-2">+</span> Nueva Consulta
                </button>
            </div>
        </div>

        <div class="bg-gray-100 p-1 rounded-xl flex mb-6">
            <button @click="tab = 'agenda'"
                :class="tab === 'agenda' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                Agenda del Día
            </button>
            <button @click="tab = 'atencion'"
                :class="tab === 'atencion' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                Atención Actual
            </button>
            <button @click="tab = 'historial'"
                :class="tab === 'historial' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                Historial
            </button>
        </div>

        <div x-show="tab === 'agenda'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center gap-2 text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold">Consultas Programadas - 03/02/2026</span>
            </div>

            <div class="divide-y divide-gray-100">
                <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition">
                    <div class="flex items-center gap-6">
                        <div class="text-blue-600 font-bold text-lg">08:00</div>
                        <div>
                            <h4 class="font-bold text-gray-800">García, Juan</h4>
                            <p class="text-xs text-gray-400">HC-001234</p>
                            <p class="text-sm text-gray-600 mt-1">Control de hipertensión</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                        Completado
                    </span>
                </div>

                <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition border-l-4 border-orange-400">
                    <div class="flex items-center gap-6">
                        <div class="text-blue-600 font-bold text-lg">08:30</div>
                        <div>
                            <h4 class="font-bold text-gray-800">López, María</h4>
                            <p class="text-xs text-gray-400">HC-001235</p>
                            <p class="text-sm text-gray-600 mt-1">Dolor abdominal</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full border border-orange-200">
                        ● En Proceso
                    </span>
                </div>

                <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition">
                    <div class="flex items-center gap-6">
                        <div class="text-blue-600 font-bold text-lg">09:00</div>
                        <div>
                            <h4 class="font-bold text-gray-800">Rodríguez, Pedro</h4>
                            <p class="text-xs text-gray-400">HC-001236</p>
                            <p class="text-sm text-gray-600 mt-1">Control post-operatorio</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">Pendiente</span>
                        <button @click="tab = 'atencion'" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                            Atender
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'atencion'" x-cloak>
            @include('medical.partials.atencion-actual')
        </div>

        <div x-show="tab === 'historial'" x-cloak>
            @include('medical.partials.historial')
        </div>

    </div> </x-app-layout>
