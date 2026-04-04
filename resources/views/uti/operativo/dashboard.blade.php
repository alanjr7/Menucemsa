<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTI - Panel Operativo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        .pulse-red { animation: pulse-red 2s infinite; }
        @keyframes pulse-red {
            0%, 100% { background-color: #fee2e2; }
            50% { background-color: #fecaca; }
        }
        .pulse-yellow { animation: pulse-yellow 2s infinite; }
        @keyframes pulse-yellow {
            0%, 100% { background-color: #fef3c7; }
            50% { background-color: #fde68a; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen" x-data="utiOperativo()">
        <!-- Sidebar -->
        @include('layouts.navigation')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Panel Operativo UTI</h1>
                        <p class="text-sm text-gray-500">Unidad de Terapia Intensiva - Monitoreo 24/7</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-500" x-text="currentDate"></span>
                        <div class="flex gap-2">
                            <button @click="viewMode = 'cards'" :class="{'bg-blue-600 text-white': viewMode === 'cards', 'bg-gray-200': viewMode !== 'cards'}" class="px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </button>
                            <button @click="viewMode = 'list'" :class="{'bg-blue-600 text-white': viewMode === 'list', 'bg-gray-200': viewMode !== 'list'}" class="px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="bg-white px-6 py-4 border-b border-gray-200">
                <div class="grid grid-cols-6 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-xs text-blue-600 font-medium uppercase">Total Pacientes</p>
                        <p class="text-2xl font-bold text-blue-900" x-text="stats.total"></p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-xs text-green-600 font-medium uppercase">Estables</p>
                        <p class="text-2xl font-bold text-green-900" x-text="stats.estables"></p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-xs text-yellow-600 font-medium uppercase">Críticos</p>
                        <p class="text-2xl font-bold text-yellow-900" x-text="stats.criticos"></p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4">
                        <p class="text-xs text-red-600 font-medium uppercase">Muy Críticos</p>
                        <p class="text-2xl font-bold text-red-900" x-text="stats.muy_criticos"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 font-medium uppercase">Camas Disp.</p>
                        <p class="text-2xl font-bold text-gray-900" x-text="stats.camas_disponibles"></p>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <p class="text-xs text-indigo-600 font-medium uppercase">Camas Ocup.</p>
                        <p class="text-2xl font-bold text-indigo-900" x-text="stats.camas_ocupadas"></p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white px-6 py-3 border-b border-gray-200">
                <div class="flex gap-4">
                    <select x-model="filterEstado" @change="loadPacientes()" class="border-gray-300 rounded-lg text-sm">
                        <option value="todos">Todos los estados</option>
                        <option value="estable">Estable</option>
                        <option value="critico">Crítico</option>
                        <option value="muy_critico">Muy Crítico</option>
                    </select>
                    <button @click="loadPacientes()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Actualizar
                    </button>
                </div>
            </div>

            <!-- Content -->
            <main class="flex-1 overflow-auto p-6">
                <!-- Loading -->
                <div x-show="loading" class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>

                <!-- Empty State -->
                <div x-show="!loading && pacientes.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <p class="text-gray-500">No hay pacientes en UTI actualmente</p>
                </div>

                <!-- Cards View -->
                <div x-show="!loading && pacientes.length > 0 && viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template x-for="paciente in pacientes" :key="paciente.id">
                        <div class="bg-white rounded-lg shadow-md border-l-4 overflow-hidden" :class="{
                            'border-green-500': paciente.estado_clinico === 'estable',
                            'border-yellow-500': paciente.estado_clinico === 'critico',
                            'border-red-500': paciente.estado_clinico === 'muy_critico',
                            'pulse-red': paciente.estado_clinico === 'muy_critico',
                            'pulse-yellow': paciente.estado_clinico === 'critico'
                        }">
                            <!-- Card Header -->
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-medium px-2 py-1 rounded-full" :class="{
                                        'bg-green-100 text-green-800': paciente.estado_clinico === 'estable',
                                        'bg-yellow-100 text-yellow-800': paciente.estado_clinico === 'critico',
                                        'bg-red-100 text-red-800': paciente.estado_clinico === 'muy_critico'
                                    }" x-text="paciente.estado_clinico.toUpperCase()"></span>
                                    <span class="text-xs text-gray-500" x-text="paciente.cama"></span>
                                </div>
                                <h3 class="font-bold text-gray-900" x-text="paciente.paciente.nombre"></h3>
                                <p class="text-sm text-gray-500">CI: <span x-text="paciente.paciente.ci"></span></p>
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 space-y-3">
                                <!-- Tiempo en UTI -->
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm text-gray-600" x-text="paciente.tiempo_texto + ' en UTI'"></span>
                                </div>

                                <!-- Diagnóstico -->
                                <p class="text-sm text-gray-600 line-clamp-2" x-text="paciente.diagnostico_principal || 'Sin diagnóstico principal'"></p>

                                <!-- Últimos Signos Vitales -->
                                <div x-show="paciente.ultimos_signos" class="bg-gray-50 rounded-lg p-2 text-xs">
                                    <p class="font-medium text-gray-700 mb-1">Últimos signos vitales:</p>
                                    <div class="grid grid-cols-2 gap-1 text-gray-600">
                                        <span x-show="paciente.ultimos_signos?.presion">PA: <span x-text="paciente.ultimos_signos?.presion"></span></span>
                                        <span x-show="paciente.ultimos_signos?.fc">FC: <span x-text="paciente.ultimos_signos?.fc"></span></span>
                                        <span x-show="paciente.ultimos_signos?.temp">Temp: <span x-text="paciente.ultimos_signos?.temp"></span>°C</span>
                                        <span x-show="paciente.ultimos_signos?.sat">SpO2: <span x-text="paciente.ultimos_signos?.sat"></span>%</span>
                                    </div>
                                    <p class="text-gray-400 mt-1" x-text="paciente.ultimos_signos?.fecha + ' - ' + paciente.ultimos_signos?.turno"></p>
                                </div>

                                <!-- Estado del Día -->
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="{
                                        'bg-green-500': paciente.estado_dia.validado,
                                        'bg-yellow-500': !paciente.estado_dia.validado && paciente.estado_dia.ronda_completada,
                                        'bg-red-500': !paciente.estado_dia.validado && !paciente.estado_dia.ronda_completada
                                    }"></span>
                                    <span class="text-xs" x-text="paciente.estado_dia.label"></span>
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="p-4 border-t border-gray-100 bg-gray-50">
                                <div class="flex gap-2">
                                    <a :href="`/uti-operativo/paciente/${paciente.id}`" class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                                        Ver Detalle
                                    </a>
                                    <button @click="showQuickActions(paciente)" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- List View -->
                <div x-show="!loading && pacientes.length > 0 && viewMode === 'list'" class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-700">Paciente</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-700">Cama</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-700">Estado Clínico</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-700">Tiempo UTI</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-700">Estado Día</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="paciente in pacientes" :key="paciente.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="font-medium text-gray-900" x-text="paciente.paciente.nombre"></p>
                                            <p class="text-gray-500 text-xs">CI: <span x-text="paciente.paciente.ci"></span></p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3" x-text="paciente.cama"></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium" :class="{
                                            'bg-green-100 text-green-800': paciente.estado_clinico === 'estable',
                                            'bg-yellow-100 text-yellow-800': paciente.estado_clinico === 'critico',
                                            'bg-red-100 text-red-800': paciente.estado_clinico === 'muy_critico'
                                        }" x-text="paciente.estado_clinico.toUpperCase()"></span>
                                    </td>
                                    <td class="px-4 py-3" x-text="paciente.tiempo_texto"></td>
                                    <td class="px-4 py-3">
                                        <span class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full" :class="{
                                                'bg-green-500': paciente.estado_dia.validado,
                                                'bg-yellow-500': !paciente.estado_dia.validado && paciente.estado_dia.ronda_completada,
                                                'bg-red-500': !paciente.estado_dia.validado && !paciente.estado_dia.ronda_completada
                                            }"></span>
                                            <span x-text="paciente.estado_dia.label"></span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a :href="`/uti-operativo/paciente/${paciente.id}`" class="text-blue-600 hover:text-blue-800 font-medium">Ver detalle</a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>

        <!-- Quick Actions Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="showModal = false">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full m-4">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Acciones Rápidas</h3>
                    <p class="text-sm text-gray-600 mb-4" x-text="selectedPaciente?.paciente?.nombre"></p>
                    <div class="space-y-2">
                        <button @click="quickAction('signos')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-100 flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Registrar Signos Vitales
                        </button>
                        <button @click="quickAction('medicamento')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-100 flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            Registrar Medicamento
                        </button>
                        <button @click="quickAction('evolucion')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-100 flex items-center gap-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Registrar Evolución
                        </button>
                        <button @click="quickAction('alimentacion')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-100 flex items-center gap-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Registrar Alimentación
                        </button>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button @click="showModal = false" class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function utiOperativo() {
            return {
                loading: false,
                viewMode: 'cards',
                filterEstado: 'todos',
                pacientes: [],
                stats: {
                    total: 0,
                    estables: 0,
                    criticos: 0,
                    muy_criticos: 0,
                    camas_disponibles: 0,
                    camas_ocupadas: 0,
                },
                currentDate: new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }),
                showModal: false,
                selectedPaciente: null,

                init() {
                    this.loadPacientes();
                },

                async loadPacientes() {
                    this.loading = true;
                    try {
                        const response = await fetch(`/api/uti-operativo/pacientes?estado_clinico=${this.filterEstado}`);
                        const data = await response.json();
                        if (data.success) {
                            this.pacientes = data.pacientes;
                            this.stats = data.stats;
                        }
                    } catch (error) {
                        console.error('Error cargando pacientes:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                showQuickActions(paciente) {
                    this.selectedPaciente = paciente;
                    this.showModal = true;
                },

                quickAction(action) {
                    if (this.selectedPaciente) {
                        window.location.href = `/uti-operativo/paciente/${this.selectedPaciente.id}?action=${action}`;
                    }
                }
            }
        }
    </script>
</body>
</html>
