<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción - Ingreso a UTI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen" x-data="utiRecepcion()">
        @include('layouts.navigation')

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Ingreso a UTI</h1>
                        <p class="text-sm text-gray-500">Registro de pacientes para Terapia Intensiva</p>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-auto p-6">
                <div class="max-w-4xl mx-auto">
                    <!-- Search Patient -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-lg font-semibold mb-4">1. Buscar Paciente</h2>
                        <div class="flex gap-4">
                            <input 
                                type="text" 
                                x-model="searchCi" 
                                @keyup.enter="buscarPaciente()"
                                placeholder="Ingrese número de documento (CI)"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            <button 
                                @click="buscarPaciente()" 
                                :disabled="searching"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
                            >
                                <svg x-show="!searching" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span x-show="searching">Buscando...</span>
                                <span x-show="!searching">Buscar</span>
                            </button>
                        </div>
                        
                        <!-- Patient Info (if found) -->
                        <div x-show="pacienteEncontrado" x-cloak class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-start gap-4">
                                <div class="bg-green-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg text-gray-900" x-text="pacienteData?.nombre"></h3>
                                    <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                                        <p><span class="text-gray-500">CI:</span> <span x-text="pacienteData?.ci"></span></p>
                                        <p><span class="text-gray-500">Sexo:</span> <span x-text="pacienteData?.sexo"></span></p>
                                        <p><span class="text-gray-500">Teléfono:</span> <span x-text="pacienteData?.telefono"></span></p>
                                        <p><span class="text-gray-500">Seguro:</span> <span x-text="pacienteData?.seguro?.nombre || 'Particular'"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alert if patient not found -->
                        <div x-show="pacienteNoExiste" x-cloak class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <p class="text-yellow-800">Paciente no encontrado. Debe registrarse primero en el sistema.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Admission Form -->
                    <div x-show="pacienteEncontrado" x-cloak class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-lg font-semibold mb-4">2. Datos del Ingreso UTI</h2>
                        
                        <form @submit.prevent="registrarIngreso()" class="space-y-4">
                            <!-- Origin -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Procedencia *</label>
                                <div class="grid grid-cols-3 gap-4">
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{'border-blue-500 bg-blue-50': ingresoForm.tipo_ingreso === 'emergencia'}">
                                        <input type="radio" x-model="ingresoForm.tipo_ingreso" value="emergencia" class="sr-only">
                                        <div class="ml-3">
                                            <span class="block font-medium">Emergencia</span>
                                            <span class="block text-xs text-gray-500">Desde área de emergencias</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{'border-blue-500 bg-blue-50': ingresoForm.tipo_ingreso === 'quirofano'}">
                                        <input type="radio" x-model="ingresoForm.tipo_ingreso" value="quirofano" class="sr-only">
                                        <div class="ml-3">
                                            <span class="block font-medium">Quirófano</span>
                                            <span class="block text-xs text-gray-500">Post-cirugía</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{'border-blue-500 bg-blue-50': ingresoForm.tipo_ingreso === 'derivacion_interna'}">
                                        <input type="radio" x-model="ingresoForm.tipo_ingreso" value="derivacion_interna" class="sr-only">
                                        <div class="ml-3">
                                            <span class="block font-medium">Derivación Interna</span>
                                            <span class="block text-xs text-gray-500">Desde otra área</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Emergency Selection (if from emergency) -->
                            <div x-show="ingresoForm.tipo_ingreso === 'emergencia'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Emergencia de origen</label>
                                <select x-model="ingresoForm.emergency_id" class="w-full border-gray-300 rounded-lg">
                                    <option value="">Seleccione emergencia...</option>
                                    <template x-for="emg in emergenciasPendientes" :key="emg.id">
                                        <option :value="emg.id" x-text="emg.code + ' - ' + emg.paciente.nombre + ' (' + emg.hora_ingreso + ')'"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Payment Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Pago *</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="ingresoForm.tipo_pago" value="particular" class="mr-2">
                                        <span>Particular</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="ingresoForm.tipo_pago" value="seguro" class="mr-2">
                                        <span>Seguro</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Insurance Info -->
                            <div x-show="ingresoForm.tipo_pago === 'seguro'" class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Seguro *</label>
                                    <select x-model="ingresoForm.seguro_id" class="w-full border-gray-300 rounded-lg mt-1" :required="ingresoForm.tipo_pago === 'seguro'">
                                        <option value="">Seleccione...</option>
                                        <template x-for="seguro in seguros" :key="seguro.codigo">
                                            <option :value="seguro.codigo" x-text="seguro.nombre"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">N° Autorización</label>
                                    <input type="text" x-model="ingresoForm.nro_autorizacion" class="w-full border-gray-300 rounded-lg mt-1" placeholder="Si dispone">
                                </div>
                            </div>

                            <!-- Diagnosis -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Diagnóstico Principal</label>
                                <textarea x-model="ingresoForm.diagnostico_principal" rows="2" class="w-full border-gray-300 rounded-lg mt-1" placeholder="Ingrese el diagnóstico principal..."></textarea>
                            </div>

                            <!-- Available Beds -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cama Disponible *</label>
                                <div x-show="camasDisponibles.length === 0" class="text-red-600 text-sm">No hay camas disponibles</div>
                                <div class="grid grid-cols-4 md:grid-cols-6 gap-3">
                                    <template x-for="cama in camasDisponibles" :key="cama.id">
                                        <label 
                                            class="border-2 rounded-lg p-3 text-center cursor-pointer transition"
                                            :class="{
                                                'border-blue-500 bg-blue-50': ingresoForm.bed_id == cama.id,
                                                'border-green-300 bg-green-50 hover:border-green-500': ingresoForm.bed_id != cama.id
                                            }"
                                        >
                                            <input type="radio" x-model="ingresoForm.bed_id" :value="cama.id" class="sr-only" required>
                                            <p class="font-bold" x-text="cama.bed_number"></p>
                                            <p class="text-xs text-gray-500" x-text="cama.tipo"></p>
                                            <p class="text-xs font-medium" x-text="'$' + cama.precio_dia + '/día'"></p>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="pt-4 border-t">
                                <button 
                                    type="submit" 
                                    :disabled="registrando || camasDisponibles.length === 0"
                                    class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span x-show="registrando">Registrando...</span>
                                    <span x-show="!registrando">Registrar Ingreso UTI</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>

        <!-- Success Modal -->
        <div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full m-4 p-6 text-center">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Ingreso Registrado</h3>
                <p class="text-gray-600 mb-4">El paciente ha sido ingresado a UTI correctamente.</p>
                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <p class="text-sm text-gray-500">Número de Ingreso</p>
                    <p class="text-xl font-bold text-gray-900" x-text="ingresoRegistrado?.nro_ingreso"></p>
                    <p class="text-sm text-gray-500 mt-1">Cama asignada: <span class="font-medium" x-text="ingresoRegistrado?.cama"></span></p>
                </div>
                <button @click="resetForm()" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    Registrar Nuevo Ingreso
                </button>
            </div>
        </div>
    </div>

    <script>
        function utiRecepcion() {
            return {
                searchCi: '',
                searching: false,
                pacienteEncontrado: false,
                pacienteNoExiste: false,
                pacienteData: null,
                camasDisponibles: [],
                emergenciasPendientes: [],
                seguros: [],
                registrando: false,
                showSuccessModal: false,
                ingresoRegistrado: null,
                ingresoForm: {
                    tipo_ingreso: 'emergencia',
                    tipo_pago: 'particular',
                    seguro_id: '',
                    nro_autorizacion: '',
                    emergency_id: '',
                    diagnostico_principal: '',
                    bed_id: ''
                },

                init() {
                    this.loadCamasDisponibles();
                    this.loadEmergenciasPendientes();
                    this.loadSeguros();
                },

                async buscarPaciente() {
                    if (!this.searchCi.trim()) return;
                    this.searching = true;
                    this.pacienteEncontrado = false;
                    this.pacienteNoExiste = false;
                    
                    try {
                        const response = await fetch('/api/reception/uti/buscar-paciente', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            },
                            body: JSON.stringify({ ci: this.searchCi })
                        });
                        const data = await response.json();
                        
                        if (data.success && data.existe) {
                            this.pacienteData = data.paciente;
                            this.pacienteEncontrado = true;
                            this.ingresoForm.seguro_id = data.paciente.seguro?.codigo || '';
                        } else {
                            this.pacienteNoExiste = true;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    } finally {
                        this.searching = false;
                    }
                },

                async loadCamasDisponibles() {
                    try {
                        const response = await fetch('/api/reception/uti/camas-disponibles');
                        const data = await response.json();
                        if (data.success) this.camasDisponibles = data.camas;
                    } catch (error) { console.error('Error:', error); }
                },

                async loadEmergenciasPendientes() {
                    try {
                        const response = await fetch('/api/reception/uti/emergencias-pendientes');
                        const data = await response.json();
                        if (data.success) this.emergenciasPendientes = data.emergencias;
                    } catch (error) { console.error('Error:', error); }
                },

                async loadSeguros() {
                    try {
                        const response = await fetch('/api/reception/uti/seguros');
                        const data = await response.json();
                        if (data.success) this.seguros = data.seguros;
                    } catch (error) { console.error('Error:', error); }
                },

                async registrarIngreso() {
                    this.registrando = true;
                    try {
                        const payload = {
                            patient_id: this.pacienteData.ci,
                            ...this.ingresoForm
                        };
                        
                        const response = await fetch('/api/reception/uti/registrar-ingreso', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            this.ingresoRegistrado = data.admission;
                            this.showSuccessModal = true;
                        } else {
                            alert(data.message || 'Error al registrar ingreso');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al registrar ingreso');
                    } finally {
                        this.registrando = false;
                    }
                },

                resetForm() {
                    this.showSuccessModal = false;
                    this.pacienteEncontrado = false;
                    this.pacienteNoExiste = false;
                    this.pacienteData = null;
                    this.searchCi = '';
                    this.ingresoRegistrado = null;
                    this.ingresoForm = {
                        tipo_ingreso: 'emergencia',
                        tipo_pago: 'particular',
                        seguro_id: '',
                        nro_autorizacion: '',
                        emergency_id: '',
                        diagnostico_principal: '',
                        bed_id: ''
                    };
                    this.loadCamasDisponibles();
                    this.loadEmergenciasPendientes();
                }
            }
        }
    </script>
</body>
</html>
