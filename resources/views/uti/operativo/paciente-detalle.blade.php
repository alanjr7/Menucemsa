<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTI - Detalle del Paciente</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen" x-data="utiPacienteDetalle({{ $admission->id }})">
        @include('layouts.navigation')

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('uti.operativo.index') }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900" x-text="paciente?.nombre || 'Cargando...'"></h1>
                            <p class="text-sm text-gray-500">CI: <span x-text="paciente?.ci"></span> | Ingreso: <span x-text="admission?.nro_ingreso"></span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium" :class="{
                            'bg-green-100 text-green-800': admission?.estado_clinico === 'estable',
                            'bg-yellow-100 text-yellow-800': admission?.estado_clinico === 'critico',
                            'bg-red-100 text-red-800': admission?.estado_clinico === 'muy_critico'
                        }" x-text="admission?.estado_clinico?.toUpperCase()"></span>
                        <button @click="showCambiarEstado = true" class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-sm hover:bg-gray-200">
                            Cambiar
                        </button>
                    </div>
                </div>
            </header>

            <!-- Patient Info Bar -->
            <div class="bg-blue-50 border-b border-blue-100 px-6 py-3">
                <div class="flex flex-wrap gap-6 text-sm">
                    <div><span class="text-gray-500">Cama:</span> <span class="font-medium" x-text="cama?.numero || 'Sin cama'"></span></div>
                    <div><span class="text-gray-500">Tiempo en UTI:</span> <span class="font-medium" x-text="admission?.tiempo_texto"></span></div>
                    <div><span class="text-gray-500">Médico:</span> <span class="font-medium" x-text="medico?.nombre || 'No asignado'"></span></div>
                    <div><span class="text-gray-500">Tipo Pago:</span> <span class="font-medium" x-text="admission?.tipo_pago === 'seguro' ? 'Seguro' : 'Particular'"></span></div>
                    <div x-show="admission?.nro_autorizacion"><span class="text-gray-500">Autorización:</span> <span class="font-medium" x-text="admission?.nro_autorizacion"></span></div>
                </div>
            </div>

            <!-- Main Content -->
            <main class="flex-1 overflow-auto p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Validation Status -->
                    <div class="bg-white rounded-lg shadow-sm p-4 mb-6" :class="{
                        'border-l-4 border-green-500': validaciones?.dia_validado,
                        'border-l-4 border-yellow-500': !validaciones?.dia_validado && validaciones?.ronda_completada,
                        'border-l-4 border-red-500': !validaciones?.dia_validado && !validaciones?.ronda_completada
                    }">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <span class="text-lg font-semibold">Estado del Día:</span>
                                <div class="flex gap-4">
                                    <span class="flex items-center gap-2" :class="validaciones?.tiene_signos_manana ? 'text-green-600' : 'text-gray-400'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Signos Mañana
                                    </span>
                                    <span class="flex items-center gap-2" :class="validaciones?.tiene_signos_tarde ? 'text-green-600' : 'text-gray-400'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Signos Tarde
                                    </span>
                                    <span class="flex items-center gap-2" :class="validaciones?.tiene_signos_noche ? 'text-green-600' : 'text-gray-400'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Signos Noche
                                    </span>
                                    <span class="flex items-center gap-2" :class="validaciones?.ronda_completada ? 'text-green-600' : 'text-gray-400'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Ronda Médica
                                    </span>
                                </div>
                            </div>
                            <button @click="validarDia()" x-show="validaciones?.puede_cerrar_dia && !validaciones?.dia_validado" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                Validar Día
                            </button>
                            <span x-show="validaciones?.dia_validado" class="text-green-600 font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Día Validado
                            </span>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="border-b border-gray-200">
                            <nav class="flex gap-1">
                                <button @click="activeTab = 'signos'" :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'signos'}" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-blue-600">Signos Vitales</button>
                                <button @click="activeTab = 'evolucion'" :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'evolucion'}" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-blue-600">Evolución Médica</button>
                                <button @click="activeTab = 'medicamentos'" :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'medicamentos'}" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-blue-600">Medicamentos</button>
                                <button @click="activeTab = 'insumos'" :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'insumos'}" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-blue-600">Insumos</button>
                                <button @click="activeTab = 'alimentacion'" :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'alimentacion'}" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-blue-600">Alimentación</button>
                                <button @click="activeTab = 'acciones'" :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'acciones'}" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-blue-600">Acciones</button>
                            </nav>
                        </div>

                        <div class="p-6">
                            <!-- Signos Vitales Tab -->
                            <div x-show="activeTab === 'signos'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">Registro de Signos Vitales</h3>
                                    <button @click="showSignosModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                                        + Nuevo Registro
                                    </button>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left">Fecha</th>
                                                <th class="px-4 py-2 text-left">Turno</th>
                                                <th class="px-4 py-2 text-left">PA</th>
                                                <th class="px-4 py-2 text-left">FC</th>
                                                <th class="px-4 py-2 text-left">FR</th>
                                                <th class="px-4 py-2 text-left">Temp</th>
                                                <th class="px-4 py-2 text-left">SpO2</th>
                                                <th class="px-4 py-2 text-left">Glicemia</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="signo in signosVitales" :key="signo.id">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-2" x-text="signo.fecha"></td>
                                                    <td class="px-4 py-2" x-text="signo.turno"></td>
                                                    <td class="px-4 py-2" x-text="signo.presion || '-'"></td>
                                                    <td class="px-4 py-2" x-text="signo.fc || '-'"></td>
                                                    <td class="px-4 py-2" x-text="signo.fr || '-'"></td>
                                                    <td class="px-4 py-2" x-text="signo.temp ? signo.temp + '°C' : '-'"></td>
                                                    <td class="px-4 py-2" x-text="signo.sat ? signo.sat + '%' : '-'"></td>
                                                    <td class="px-4 py-2" x-text="signo.glicemia || '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Evolución Tab -->
                            <div x-show="activeTab === 'evolucion'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">Evolución Médica</h3>
                                    <button @click="showEvolucionModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                                        + Nueva Ronda
                                    </button>
                                </div>
                                <div class="space-y-4">
                                    <template x-for="evo in evoluciones" :key="evo.id">
                                        <div class="border border-gray-200 rounded-lg p-4" :class="{'bg-green-50 border-green-200': evo.dia_validado}">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <span class="font-medium" x-text="evo.fecha"></span>
                                                    <span class="text-sm text-gray-500 ml-2" x-text="' - Dr. ' + evo.medico"></span>
                                                </div>
                                                <span x-show="evo.dia_validado" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Validado</span>
                                                <span x-show="!evo.dia_validado && evo.ronda_completada" class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Ronda Completada</span>
                                            </div>
                                            <p class="text-sm text-gray-700 mb-2" x-text="evo.evolucion_medica"></p>
                                            <div x-show="evo.indicaciones" class="text-sm">
                                                <span class="font-medium">Indicaciones:</span>
                                                <p class="text-gray-600" x-text="evo.indicaciones"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Medicamentos Tab -->
                            <div x-show="activeTab === 'medicamentos'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">Medicamentos Administrados</h3>
                                    <button @click="showMedicamentoModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                                        + Registrar Medicamento
                                    </button>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left">Fecha/Hora</th>
                                                <th class="px-4 py-2 text-left">Medicamento</th>
                                                <th class="px-4 py-2 text-left">Dosis</th>
                                                <th class="px-4 py-2 text-left">Vía</th>
                                                <th class="px-4 py-2 text-left">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="med in medicamentos" :key="med.id">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-2" x-text="med.fecha + ' ' + med.hora"></td>
                                                    <td class="px-4 py-2 font-medium" x-text="med.medicamento"></td>
                                                    <td class="px-4 py-2" x-text="med.dosis"></td>
                                                    <td class="px-4 py-2" x-text="med.via"></td>
                                                    <td class="px-4 py-2">
                                                        <span x-show="med.cargo_generado" class="text-green-600 text-xs">✓ Cargo generado</span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Insumos Tab -->
                            <div x-show="activeTab === 'insumos'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">Insumos Utilizados</h3>
                                    <button @click="showInsumoModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                                        + Registrar Insumo
                                    </button>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left">Fecha/Hora</th>
                                                <th class="px-4 py-2 text-left">Insumo</th>
                                                <th class="px-4 py-2 text-left">Cantidad</th>
                                                <th class="px-4 py-2 text-left">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="ins in insumos" :key="ins.id">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-2" x-text="ins.fecha"></td>
                                                    <td class="px-4 py-2 font-medium" x-text="ins.insumo"></td>
                                                    <td class="px-4 py-2" x-text="ins.cantidad"></td>
                                                    <td class="px-4 py-2">
                                                        <span x-show="ins.cargo_generado" class="text-green-600 text-xs">✓ Cargo generado</span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Alimentación Tab -->
                            <div x-show="activeTab === 'alimentacion'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">Control de Alimentación</h3>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <template x-for="comida in ['desayuno', 'almuerzo', 'merienda', 'cena']" :key="comida">
                                        <div class="border rounded-lg p-4" :class="getAlimentacionClase(comida)">
                                            <h4 class="font-medium capitalize" x-text="comida"></h4>
                                            <div class="mt-2 space-y-2">
                                                <button @click="registrarAlimentacion(comida, 'dado')" class="w-full px-3 py-2 bg-green-100 text-green-700 rounded hover:bg-green-200 text-sm">Dado</button>
                                                <button @click="registrarAlimentacion(comida, 'no_dado')" class="w-full px-3 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm">No Dado</button>
                                                <button @click="registrarAlimentacion(comida, 'no_aplica')" class="w-full px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">No Aplica</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Acciones Tab -->
                            <div x-show="activeTab === 'acciones'" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <button @click="showTrasladarModal = true" class="p-6 border-2 border-blue-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition text-center">
                                        <svg class="w-12 h-12 text-blue-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        <h4 class="font-medium text-gray-900">Trasladar Paciente</h4>
                                        <p class="text-sm text-gray-500 mt-1">A Hospitalización o Quirófano</p>
                                    </button>
                                    <button @click="showAltaModal = true" :disabled="!validaciones?.dia_validado" class="p-6 border-2 border-green-200 rounded-lg hover:border-green-400 hover:bg-green-50 transition text-center disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="w-12 h-12 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <h4 class="font-medium text-gray-900">Dar Alta Clínica</h4>
                                        <p class="text-sm text-gray-500 mt-1" x-text="validaciones?.dia_validado ? 'Disponible' : 'Requiere día validado'"></p>
                                    </button>
                                    <div class="p-6 border-2 border-gray-200 rounded-lg">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <h4 class="font-medium text-gray-900">Estado Actual</h4>
                                        <p class="text-sm text-gray-500 mt-1" x-text="admission?.estado === 'activo' ? 'Activo en UTI' : admission?.estado"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modals -->
        <!-- Signos Vitales Modal -->
        <div x-show="showSignosModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full m-4">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Registrar Signos Vitales</h3>
                    <form @submit.prevent="guardarSignos()">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Turno</label>
                                <select x-model="signosForm.turno" class="mt-1 block w-full rounded-lg border-gray-300">
                                    <option value="manana">Mañana</option>
                                    <option value="tarde">Tarde</option>
                                    <option value="noche">Noche</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hora</label>
                                <input type="time" x-model="signosForm.hora" class="mt-1 block w-full rounded-lg border-gray-300" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PA Sistólica</label>
                                <input type="number" x-model="signosForm.presion_arterial_sistolica" class="mt-1 block w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PA Diastólica</label>
                                <input type="number" x-model="signosForm.presion_arterial_diastolica" class="mt-1 block w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Frec. Cardiaca</label>
                                <input type="number" x-model="signosForm.frecuencia_cardiaca" class="mt-1 block w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Frec. Respiratoria</label>
                                <input type="number" x-model="signosForm.frecuencia_respiratoria" class="mt-1 block w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Temperatura (°C)</label>
                                <input type="number" step="0.1" x-model="signosForm.temperatura" class="mt-1 block w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Saturación O2 (%)</label>
                                <input type="number" step="0.1" x-model="signosForm.saturacion_o2" class="mt-1 block w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea x-model="signosForm.observaciones" rows="2" class="mt-1 block w-full rounded-lg border-gray-300"></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="showSignosModal = false" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg">Cancelar</button>
                            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Evolución Modal -->
        <div x-show="showEvolucionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full m-4">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Registrar Evolución Médica</h3>
                    <form @submit.prevent="guardarEvolucion()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Evolución Médica</label>
                            <textarea x-model="evolucionForm.evolucion_medica" rows="4" class="mt-1 block w-full rounded-lg border-gray-300" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Indicaciones</label>
                            <textarea x-model="evolucionForm.indicaciones" rows="2" class="mt-1 block w-full rounded-lg border-gray-300"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Plan de Tratamiento</label>
                            <textarea x-model="evolucionForm.plan_tratamiento" rows="2" class="mt-1 block w-full rounded-lg border-gray-300"></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="showEvolucionModal = false" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg">Cancelar</button>
                            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Alta Modal -->
        <div x-show="showAltaModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full m-4">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-4">Dar Alta Clínica</h3>
                    <p class="text-sm text-gray-600 mb-4">Esta acción liberará la cama UTI y enviará al paciente a caja para el cobro.</p>
                    <form @submit.prevent="darAltaClinica()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Destino</label>
                            <select x-model="altaForm.destino_alta" class="mt-1 block w-full rounded-lg border-gray-300" required>
                                <option value="">Seleccione...</option>
                                <option value="hospitalizacion">Hospitalización</option>
                                <option value="domicilio">Domicilio</option>
                                <option value="otro_hospital">Otro Hospital</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea x-model="altaForm.observaciones" rows="2" class="mt-1 block w-full rounded-lg border-gray-300"></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="showAltaModal = false" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg">Cancelar</button>
                            <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg">Confirmar Alta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cambiar Estado Modal -->
        <div x-show="showCambiarEstado" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full m-4">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Cambiar Estado Clínico</h3>
                    <div class="space-y-2">
                        <button @click="cambiarEstadoClinico('estable')" class="w-full px-4 py-3 text-left rounded-lg hover:bg-green-50 border border-green-200">
                            <span class="font-medium text-green-700">Estable</span>
                        </button>
                        <button @click="cambiarEstadoClinico('critico')" class="w-full px-4 py-3 text-left rounded-lg hover:bg-yellow-50 border border-yellow-200">
                            <span class="font-medium text-yellow-700">Crítico</span>
                        </button>
                        <button @click="cambiarEstadoClinico('muy_critico')" class="w-full px-4 py-3 text-left rounded-lg hover:bg-red-50 border border-red-200">
                            <span class="font-medium text-red-700">Muy Crítico</span>
                        </button>
                    </div>
                    <button @click="showCambiarEstado = false" class="w-full mt-4 bg-gray-200 text-gray-700 py-2 rounded-lg">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function utiPacienteDetalle(admissionId) {
            return {
                admissionId: admissionId,
                activeTab: 'signos',
                loading: false,
                admission: null,
                paciente: null,
                cama: null,
                medico: null,
                validaciones: null,
                signosVitales: [],
                evoluciones: [],
                medicamentos: [],
                insumos: [],
                alimentacion: [],
                showSignosModal: false,
                showEvolucionModal: false,
                showMedicamentoModal: false,
                showInsumoModal: false,
                showAltaModal: false,
                showTrasladarModal: false,
                showCambiarEstado: false,
                signosForm: { turno: 'manana', fecha: new Date().toISOString().split('T')[0], hora: new Date().toTimeString().slice(0,5) },
                evolucionForm: { evolucion_medica: '', indicaciones: '', plan_tratamiento: '' },
                altaForm: { destino_alta: '', observaciones: '' },

                init() {
                    this.loadDetalle();
                },

                async loadDetalle() {
                    try {
                        const response = await fetch(`/api/uti-operativo/paciente/${this.admissionId}/detalle`);
                        const data = await response.json();
                        if (data.success) {
                            this.admission = data.admission;
                            this.paciente = data.paciente;
                            this.cama = data.cama;
                            this.medico = data.medico;
                            this.validaciones = data.validaciones_hoy;
                            this.signosVitales = data.signos_vitales;
                            this.evoluciones = data.evoluciones;
                            this.medicamentos = data.medicamentos;
                            this.insumos = data.insumos;
                            this.alimentacion = data.alimentacion;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                async guardarSignos() {
                    try {
                        const response = await fetch(`/api/uti-operativo/paciente/${this.admissionId}/signos`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                            body: JSON.stringify(this.signosForm)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showSignosModal = false;
                            this.loadDetalle();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                async guardarEvolucion() {
                    try {
                        this.evolucionForm.fecha = new Date().toISOString().split('T')[0];
                        this.evolucionForm.medico_id = this.medico?.id;
                        const response = await fetch(`/api/uti-operativo/paciente/${this.admissionId}/evolucion`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                            body: JSON.stringify(this.evolucionForm)
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showEvolucionModal = false;
                            this.loadDetalle();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                async validarDia() {
                    if (!confirm('¿Está seguro de validar el día? Esta acción generará el cargo por estadía.')) return;
                    try {
                        const response = await fetch(`/api/uti-operativo/paciente/${this.admissionId}/validar-dia`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                            body: JSON.stringify({ fecha: new Date().toISOString().split('T')[0] })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.loadDetalle();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                async cambiarEstadoClinico(estado) {
                    try {
                        const response = await fetch(`/api/uti-operativo/paciente/${this.admissionId}/estado-clinico`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                            body: JSON.stringify({ estado_clinico: estado })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showCambiarEstado = false;
                            this.loadDetalle();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                async darAltaClinica() {
                    try {
                        const response = await fetch(`/api/uti-operativo/paciente/${this.admissionId}/alta-clinica`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                            body: JSON.stringify(this.altaForm)
                        });
                        const data = await response.json();
                        if (data.success) {
                            window.location.href = '{{ route('uti.operativo.index') }}';
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                getAlimentacionClase(tipo) {
                    const reg = this.alimentacion.find(a => a.tipo_comida === tipo);
                    if (!reg) return 'border-gray-200';
                    return reg.estado === 'dado' ? 'border-green-400 bg-green-50' : 
                           reg.estado === 'no_dado' ? 'border-red-400 bg-red-50' : 'border-gray-200';
                },

                async registrarAlimentacion(tipo, estado) {
                    try {
                        const response = await fetch(`/api/uti-operativo/paciente/${this.admissionId}/alimentacion`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                            body: JSON.stringify({ tipo_comida: tipo, estado: estado, fecha: new Date().toISOString().split('T')[0] })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.loadDetalle();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }
            }
        }
    </script>
</body>
</html>
