@extends('layouts.app')

@section('content')
    <script>
        function utiAdmin() {
            return {
                activeTab: 'dashboard',
                stats: null,
                camas: [],
                pacientesList: [],
                tarifario: [],
                alertas: [],
                alertasCount: 0,
                filtros: { estado_clinico: 'todos', tipo_pago: 'todos', dias_estancia: 'todos' },
                showNuevaCamaModal: false,
                showNuevaTarifaModal: false,
                nuevaCama: { bed_number: '', tipo: '', equipamiento: '', precio_dia: '' },
                nuevaTarifa: { concepto: '', tipo: '', precio: '', unidad: '', descripcion: '' },

                init() {
                    this.loadStats();
                    this.loadCamas();
                    this.loadAlertas();
                    this.loadTarifario();
                },

                async loadStats() {
                    try {
                        const response = await fetch('/uti-admin/api/estadisticas');
                        const data = await response.json();
                        if (data.success) this.stats = data;
                    } catch (error) { console.error('Error:', error); }
                },

                async loadCamas() {
                    try {
                        const response = await fetch('/uti-admin/api/camas-grid');
                        const data = await response.json();
                        if (data.success) this.camas = data.camas;
                    } catch (error) { console.error('Error:', error); }
                },

                async loadPacientes() {
                    try {
                        const params = new URLSearchParams(this.filtros);
                        const response = await fetch(`/uti-admin/api/pacientes?${params}`);
                        const data = await response.json();
                        if (data.success) this.pacientesList = data.pacientes;
                    } catch (error) { console.error('Error:', error); }
                },

                async loadAlertas() {
                    try {
                        const response = await fetch('/uti-admin/api/alertas');
                        const data = await response.json();
                        if (data.success) {
                            this.alertas = data.alertas;
                            this.alertasCount = data.total;
                        }
                    } catch (error) { console.error('Error:', error); }
                },

                verCostos(id) {
                    window.open(`/uti-admin/costos/${id}`, '_blank');
                },

                async crearCama() {
                    try {
                        const response = await fetch('/uti-admin/api/camas', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.nuevaCama)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showNuevaCamaModal = false;
                            this.nuevaCama = { bed_number: '', tipo: '', equipamiento: '', precio_dia: '' };
                            this.loadCamas();
                            this.loadStats();
                        } else {
                            alert('Error: ' + (data.message || 'No se pudo crear la cama'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al crear la cama');
                    }
                },

                async loadTarifario() {
                    try {
                        const response = await fetch('/uti-admin/api/tarifario');
                        const data = await response.json();
                        if (data.success) {
                            this.tarifario = data.tarifas.map(t => ({
                                ...t,
                                tipo_label: {
                                    'estadia': '🛏️ Estadía',
                                    'alimentacion': '🍽️ Alimentación',
                                    'procedimiento': '🔧 Procedimiento',
                                    'insumo': '📦 Insumo',
                                    'medicamento': '💊 Medicamento'
                                }[t.tipo] || t.tipo
                            }));
                        }
                    } catch (error) { console.error('Error:', error); }
                },

                async crearTarifa() {
                    try {
                        const response = await fetch('/uti-admin/api/tarifario', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.nuevaTarifa)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showNuevaTarifaModal = false;
                            this.nuevaTarifa = { concepto: '', tipo: '', precio: '', unidad: '', descripcion: '' };
                            this.loadTarifario();
                        } else {
                            alert('Error: ' + (data.message || 'No se pudo crear la tarifa'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al crear la tarifa');
                    }
                },

                editarTarifa(tarifa) {
                    // TODO: Implementar edición
                    alert('Función de editar en desarrollo');
                }
            }
        }
    </script>

    <div class="flex h-screen" x-data="utiAdmin()" x-init="init()">
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header del Sistema -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-end">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Panel Administrativo UTI</h1>
                        <p class="text-sm text-gray-500">Supervisión, control financiero y gestión de camas</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider animate-pulse">● En Vivo</span>
                        <p class="text-[10px] text-gray-400 mt-1 font-medium">{{ now()->format('d M, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="bg-white border-b border-gray-200 px-6 py-2">
                <div class="flex gap-2">
                    <button @click="activeTab = 'dashboard'" :class="{'bg-blue-600 text-white': activeTab === 'dashboard', 'bg-gray-200': activeTab !== 'dashboard'}" class="px-4 py-2 rounded-lg text-sm font-medium transition">Dashboard</button>
                    <button @click="activeTab = 'camas'" :class="{'bg-blue-600 text-white': activeTab === 'camas', 'bg-gray-200': activeTab !== 'camas'}" class="px-4 py-2 rounded-lg text-sm font-medium transition">Camas</button>
                    <button @click="activeTab = 'pacientes'" :class="{'bg-blue-600 text-white': activeTab === 'pacientes', 'bg-gray-200': activeTab !== 'pacientes'}" class="px-4 py-2 rounded-lg text-sm font-medium transition">Pacientes</button>
                    <button @click="activeTab = 'tarifario'" :class="{'bg-blue-600 text-white': activeTab === 'tarifario', 'bg-gray-200': activeTab !== 'tarifario'}" class="px-4 py-2 rounded-lg text-sm font-medium transition">Tarifario</button>
                    <button @click="activeTab = 'alertas'" :class="{'bg-blue-600 text-white': activeTab === 'alertas', 'bg-gray-200': activeTab !== 'alertas'}" class="px-4 py-2 rounded-lg text-sm font-medium transition relative">
                        Alertas
                        <span x-show="alertasCount > 0" x-text="alertasCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center"></span>
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <main class="flex-1 overflow-auto p-6">
                <!-- Dashboard Tab -->
                <div x-show="activeTab === 'dashboard'" class="max-w-7xl mx-auto">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">Total Camas</p>
                                    <p class="text-3xl font-bold text-gray-900" x-text="stats?.camas?.total"></p>
                                </div>
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">Ocupación</p>
                                    <p class="text-3xl font-bold text-gray-900"><span x-text="stats?.camas?.ocupacion_porcentaje"></span>%</p>
                                    <p class="text-xs text-gray-400" x-text="stats?.camas?.ocupadas + ' de ' + stats?.camas?.total + ' camas'"></p>
                                </div>
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">Pacientes Activos</p>
                                    <p class="text-3xl font-bold text-gray-900" x-text="stats?.pacientes?.activos"></p>
                                </div>
                                <div class="bg-yellow-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">Listos para Alta</p>
                                    <p class="text-3xl font-bold text-gray-900" x-text="stats?.pacientes?.alta_clinica"></p>
                                </div>
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alertas Summary -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-red-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-red-600 font-medium">Estancias Prolongadas</p>
                                    <p class="text-2xl font-bold text-red-900" x-text="stats?.alertas?.estancias_prolongadas"></p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-yellow-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-yellow-600 font-medium">Sin Preautorización</p>
                                    <p class="text-2xl font-bold text-yellow-900" x-text="stats?.alertas?.sin_preautorizacion"></p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-orange-100 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-orange-600 font-medium">Sin Registro Hoy</p>
                                    <p class="text-2xl font-bold text-orange-900" x-text="stats?.alertas?.sin_registro_hoy"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid de Camas -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4">Mapa de Camas UTI</h2>
                        <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                            <template x-for="cama in camas" :key="cama.id">
                                <div class="border-2 rounded-lg p-3 text-center cursor-pointer hover:shadow-md transition" :class="{
                                    'border-red-500 bg-red-50': cama.status === 'ocupada',
                                    'border-green-500 bg-green-50': cama.status === 'disponible',
                                    'border-yellow-500 bg-yellow-50': cama.status === 'mantenimiento',
                                    'border-blue-500 bg-blue-50': cama.status === 'reservada'
                                }" @click="cama.status === 'ocupada' ? window.location.href = '/uti-operativo/paciente/' + cama.paciente?.id : null">
                                    <p class="font-bold text-lg" x-text="cama.numero"></p>
                                    <p class="text-xs" :class="{
                                        'text-red-600': cama.status === 'ocupada',
                                        'text-green-600': cama.status === 'disponible',
                                        'text-yellow-600': cama.status === 'mantenimiento',
                                        'text-blue-600': cama.status === 'reservada'
                                    }" x-text="cama.status_label"></p>
                                    <div x-show="cama.paciente" class="mt-2">
                                        <p class="text-xs font-medium truncate" x-text="cama.paciente?.nombre?.split(' ')[0]"></p>
                                        <p class="text-xs text-gray-500" x-text="cama.paciente?.dias_en_uti + ' días'"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Camas Tab -->
                <div x-show="activeTab === 'camas'" class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Gestión de Camas</h2>
                        <button @click="showNuevaCamaModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Nueva Cama</button>
                    </div>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">Número</th>
                                    <th class="px-4 py-3 text-left">Tipo</th>
                                    <th class="px-4 py-3 text-left">Estado</th>
                                    <th class="px-4 py-3 text-left">Precio/Día</th>
                                    <th class="px-4 py-3 text-left">Paciente</th>
                                    <th class="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="cama in camas" :key="cama.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium" x-text="cama.numero"></td>
                                        <td class="px-4 py-3" x-text="cama.tipo"></td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="{
                                                'bg-red-100 text-red-800': cama.status === 'ocupada',
                                                'bg-green-100 text-green-800': cama.status === 'disponible',
                                                'bg-yellow-100 text-yellow-800': cama.status === 'mantenimiento',
                                                'bg-blue-100 text-blue-800': cama.status === 'reservada'
                                            }" x-text="cama.status_label"></span>
                                        </td>
                                        <td class="px-4 py-3" x-text="'$' + cama.precio_dia"></td>
                                        <td class="px-4 py-3" x-text="cama.paciente?.nombre || '-'"></td>
                                        <td class="px-4 py-3 text-right">
                                            <button x-show="cama.status !== 'ocupada'" @click="editarCama(cama)" class="text-blue-600 hover:text-blue-800 text-sm">Editar</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pacientes Tab -->
                <div x-show="activeTab === 'pacientes'" class="max-w-7xl mx-auto">
                    <div class="bg-white rounded-lg shadow p-4 mb-4">
                        <div class="flex flex-wrap gap-4">
                            <select x-model="filtros.estado_clinico" @change="loadPacientes()" class="border-gray-300 rounded-lg text-sm">
                                <option value="todos">Todos los estados clínicos</option>
                                <option value="estable">Estable</option>
                                <option value="critico">Crítico</option>
                                <option value="muy_critico">Muy Crítico</option>
                            </select>
                            <select x-model="filtros.tipo_pago" @change="loadPacientes()" class="border-gray-300 rounded-lg text-sm">
                                <option value="todos">Todos los tipos de pago</option>
                                <option value="particular">Particular</option>
                                <option value="seguro">Seguro</option>
                            </select>
                            <select x-model="filtros.dias_estancia" @change="loadPacientes()" class="border-gray-300 rounded-lg text-sm">
                                <option value="todos">Todos los días</option>
                                <option value="7">Más de 7 días</option>
                                <option value="14">Más de 14 días</option>
                                <option value="30">Más de 30 días</option>
                            </select>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">Paciente</th>
                                    <th class="px-4 py-3 text-left">Cama</th>
                                    <th class="px-4 py-3 text-left">Estado</th>
                                    <th class="px-4 py-3 text-left">Días</th>
                                    <th class="px-4 py-3 text-left">Pago</th>
                                    <th class="px-4 py-3 text-left">Autorización</th>
                                    <th class="px-4 py-3 text-right">Costo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="p in pacientesList" :key="p.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <p class="font-medium" x-text="p.paciente.nombre"></p>
                                            <p class="text-gray-500 text-xs" x-text="'CI: ' + p.paciente.ci"></p>
                                        </td>
                                        <td class="px-4 py-3" x-text="p.cama"></td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="{
                                                'bg-green-100 text-green-800': p.estado_clinico === 'estable',
                                                'bg-yellow-100 text-yellow-800': p.estado_clinico === 'critico',
                                                'bg-red-100 text-red-800': p.estado_clinico === 'muy_critico'
                                            }" x-text="p.estado_clinico.toUpperCase()"></span>
                                        </td>
                                        <td class="px-4 py-3" x-text="p.dias_en_uti"></td>
                                        <td class="px-4 py-3">
                                            <span x-text="p.tipo_pago === 'seguro' ? p.seguro : 'Particular'" class="text-sm"></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span x-show="p.nro_autorizacion" x-text="p.nro_autorizacion" class="text-green-600 text-sm"></span>
                                            <button x-show="!p.nro_autorizacion && p.tipo_pago === 'seguro'" @click="agregarAutorizacion(p)" class="text-red-600 text-sm hover:underline">Agregar</button>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button @click="verCostos(p.id)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver Costos</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tarifario Tab -->
                <div x-show="activeTab === 'tarifario'" class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Tarifario UTI</h2>
                        <button @click="showNuevaTarifaModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Nueva Tarifa</button>
                    </div>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">Concepto</th>
                                    <th class="px-4 py-3 text-left">Tipo</th>
                                    <th class="px-4 py-3 text-left">Precio</th>
                                    <th class="px-4 py-3 text-left">Unidad</th>
                                    <th class="px-4 py-3 text-left">Estado</th>
                                    <th class="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="tarifa in tarifario" :key="tarifa.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium" x-text="tarifa.concepto"></td>
                                        <td class="px-4 py-3" x-text="tarifa.tipo_label"></td>
                                        <td class="px-4 py-3" x-text="'$' + tarifa.precio"></td>
                                        <td class="px-4 py-3" x-text="tarifa.unidad"></td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="tarifa.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" x-text="tarifa.activo ? 'Activo' : 'Inactivo'"></span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button @click="editarTarifa(tarifa)" class="text-blue-600 hover:text-blue-800 text-sm">Editar</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Alertas Tab -->
                <div x-show="activeTab === 'alertas'" class="max-w-7xl mx-auto">
                    <h2 class="text-xl font-semibold mb-4">Alertas del Sistema</h2>
                    <div class="space-y-3">
                        <template x-for="alerta in alertas" :key="alerta.admission_id + alerta.tipo">
                            <div class="bg-white rounded-lg shadow p-4 border-l-4" :class="{
                                'border-red-500 bg-red-50': alerta.nivel === 'danger',
                                'border-yellow-500 bg-yellow-50': alerta.nivel === 'warning',
                                'border-blue-500 bg-blue-50': alerta.nivel === 'info'
                            }">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium" x-text="alerta.paciente"></p>
                                        <p class="text-sm text-gray-600" x-text="alerta.mensaje"></p>
                                    </div>
                                    <button @click="window.location.href = '/uti-operativo/paciente/' + alerta.admission_id" class="text-blue-600 hover:text-blue-800 text-sm">Ver paciente</button>
                                </div>
                            </div>
                        </template>
                        <div x-show="alertas.length === 0" class="text-center py-8 text-gray-500">
                            No hay alertas pendientes
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modal Nueva Cama -->
        <div x-show="showNuevaCamaModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.away="showNuevaCamaModal = false">
            <div class="bg-white rounded-lg p-6 w-full max-w-md" @click.stop>
                <h3 class="text-lg font-semibold mb-4">Nueva Cama UTI</h3>
                <form @submit.prevent="crearCama()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de Cama</label>
                        <input type="text" x-model="nuevaCama.bed_number" class="w-full border-gray-300 rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select x-model="nuevaCama.tipo" class="w-full border-gray-300 rounded-lg" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="UCI">UCI</option>
                            <option value="UCIM">UCIM</option>
                            <option value="INTERMEDIA">Intermedia</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Equipamiento</label>
                        <textarea x-model="nuevaCama.equipamiento" class="w-full border-gray-300 rounded-lg" rows="2"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio por Día</label>
                        <input type="number" x-model="nuevaCama.precio_dia" step="0.01" min="0" class="w-full border-gray-300 rounded-lg" required>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showNuevaCamaModal = false" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Crear Cama</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Nueva Tarifa -->
        <div x-show="showNuevaTarifaModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.away="showNuevaTarifaModal = false">
            <div class="bg-white rounded-lg p-6 w-full max-w-md" @click.stop>
                <h3 class="text-lg font-semibold mb-4">Nueva Tarifa UTI</h3>
                <form @submit.prevent="crearTarifa()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                        <input type="text" x-model="nuevaTarifa.concepto" class="w-full border-gray-300 rounded-lg" placeholder="Ej: Ventilación Mecánica" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select x-model="nuevaTarifa.tipo" class="w-full border-gray-300 rounded-lg" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="estadia">Estadía</option>
                            <option value="alimentacion">Alimentación</option>
                            <option value="procedimiento">Procedimiento</option>
                            <option value="insumo">Insumo</option>
                            <option value="medicamento">Medicamento</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                        <input type="number" x-model="nuevaTarifa.precio" step="0.01" min="0" class="w-full border-gray-300 rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unidad</label>
                        <input type="text" x-model="nuevaTarifa.unidad" class="w-full border-gray-300 rounded-lg" placeholder="Ej: día, hora, unidad, dosis" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                        <textarea x-model="nuevaTarifa.descripcion" class="w-full border-gray-300 rounded-lg" rows="2"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showNuevaTarifaModal = false" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Crear Tarifa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
