@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('internacion-staff.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">Evaluación del Paciente</h1>
                </div>
                <p class="text-sm text-gray-500 mt-1 ml-9">Internación #{{ $hospitalizacion->id }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-sm font-medium rounded-full
                    @if($hospitalizacion->estado === 'estable') bg-green-100 text-green-800
                    @elseif($hospitalizacion->estado === 'critico') bg-red-100 text-red-800
                    @elseif($hospitalizacion->estado === 'en_observacion') bg-yellow-100 text-yellow-800
                    @else bg-blue-100 text-blue-800 @endif">
                    {{ ucfirst($hospitalizacion->estado ?? 'Activo') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Info del Paciente -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Paciente</p>
                <p class="text-lg font-semibold text-gray-800">{{ $hospitalizacion->paciente?->nombre ?? 'Desconocido' }}</p>
                <p class="text-sm text-gray-500">CI: {{ $hospitalizacion->ci_paciente }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Habitación</p>
                <p class="text-lg font-semibold text-gray-800">{{ $hospitalizacion->habitacion_id ?? 'Por asignar' }}</p>
                <p class="text-sm text-gray-500">{{ $hospitalizacion->servicio ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Médico</p>
                <p class="text-lg font-semibold text-gray-800">{{ $hospitalizacion->medico?->user?->name ?? 'No asignado' }}</p>
                <p class="text-sm text-gray-500">{{ $hospitalizacion->medico?->especialidad?->nombre ?? '' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Ingreso</p>
                <p class="text-lg font-semibold text-gray-800">{{ $hospitalizacion->fecha_ingreso?->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-500">{{ $hospitalizacion->fecha_ingreso?->format('H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button onclick="mostrarTab('medicamentos')" id="tab-medicamentos" class="tab-btn border-b-2 border-indigo-500 text-indigo-600 py-4 px-6 font-medium text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    Medicamentos
                    <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs" id="count-medicamentos">0</span>
                </button>
                <button onclick="mostrarTab('catering')" id="tab-catering" class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-6 font-medium text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Catering
                    <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full text-xs" id="count-catering">0</span>
                </button>
                <button onclick="mostrarTab('drenajes')" id="tab-drenajes" class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-6 font-medium text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    Drenajes
                    <span class="bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full text-xs" id="count-drenajes">0</span>
                </button>
            </nav>
        </div>

        <!-- Tab Medicamentos -->
        <div id="panel-medicamentos" class="tab-panel p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nuevo Medicamento
                        </h3>
                        <form id="formMedicamento" class="space-y-4">
                            <!-- Buscador de medicamentos -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar Medicamento</label>
                                <input type="text" id="buscarMedicamento" placeholder="Escribe el nombre del medicamento..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500" autocomplete="off">
                                <!-- Resultados de búsqueda -->
                                <div id="resultadosBusqueda" class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
                                    <!-- Se llena dinámicamente -->
                                </div>
                                <!-- Medicamento seleccionado -->
                                <input type="hidden" id="medicamentoIdSeleccionado" required>
                                <div id="medicamentoSeleccionado" class="mt-2 p-3 bg-indigo-50 border border-indigo-200 rounded-lg hidden">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-indigo-900" id="nombreMedicamentoSel"></p>
                                            <p class="text-xs text-indigo-600" id="infoMedicamentoSel"></p>
                                        </div>
                                        <button type="button" onclick="limpiarMedicamentoSeleccionado()" class="text-indigo-400 hover:text-indigo-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" id="cantidadMedicamento" step="0.01" min="0.01" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500" required>
                                    <span id="unidadMedicamentoDisplay" class="text-sm text-gray-500 font-medium px-2"></span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" id="stock-info"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vía de Administración</label>
                                <select id="viaMedicamento" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
                                    <option value="">Seleccione...</option>
                                    <option value="oral">Oral</option>
                                    <option value="intravenosa">Intravenosa</option>
                                    <option value="intramuscular">Intramuscular</option>
                                    <option value="subcutanea">Subcutánea</option>
                                    <option value="topica">Tópica</option>
                                    <option value="rectal">Rectal</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea id="obsMedicamento" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white font-medium py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Registrar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lista -->
                <div class="lg:col-span-2">
                    <h3 class="font-semibold text-gray-800 mb-4">Historial de Medicamentos Administrados</h3>
                    <div id="listaMedicamentos" class="space-y-3">
                        <p class="text-gray-400 text-sm text-center py-8">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Catering -->
        <div id="panel-catering" class="tab-panel p-6 hidden">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-semibold text-gray-800">Registro de Comidas - {{ now()->format('d/m/Y') }}</h3>
                <span class="text-sm text-gray-500">Precios configurados: Desayuno Bs. {{ config('hospitalizacion.catering.precios.desayuno', 15) }}, Almuerzo Bs. {{ config('hospitalizacion.catering.precios.almuerzo', 25) }}</span>
            </div>
            <div id="catering-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Se carga dinámicamente -->
            </div>
        </div>

        <!-- Tab Drenajes -->
        <div id="panel-drenajes" class="tab-panel p-6 hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nuevo Drenaje
                        </h3>
                        <form id="formDrenaje" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Drenaje</label>
                                <select id="tipoDrenaje" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-cyan-500">
                                    <option value="">Seleccione...</option>
                                    <option value="Pleural">Pleural (Bs. {{ config('hospitalizacion.drenajes.precios.Pleural', 50) }})</option>
                                    <option value="Abdominal">Abdominal (Bs. {{ config('hospitalizacion.drenajes.precios.Abdominal', 60) }})</option>
                                    <option value="Torácico">Torácico (Bs. {{ config('hospitalizacion.drenajes.precios.Torácico', 55) }})</option>
                                    <option value="General">General (Bs. {{ config('hospitalizacion.drenajes.precios.General', 40) }})</option>
                                </select>
                            </div>
                            <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                                <input type="checkbox" id="drenajeRealizado" class="w-5 h-5 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                                <label for="drenajeRealizado" class="ml-3 text-sm font-medium text-gray-700">Drenaje Realizado</label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea id="obsDrenaje" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-cyan-500"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-cyan-600 text-white font-medium py-2 rounded-lg hover:bg-cyan-700 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Registrar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lista -->
                <div class="lg:col-span-2">
                    <h3 class="font-semibold text-gray-800 mb-4">Historial de Drenajes</h3>
                    <div id="listaDrenajes" class="space-y-3">
                        <p class="text-gray-400 text-sm text-center py-8">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const hospitalizacionId = '{{ $hospitalizacion->id }}';
    let medicamentosDisponibles = [];
    let medicamentosPaciente = [];
    let drenajesPaciente = [];

    // ==================== TABS ====================
    function mostrarTab(tab) {
        // Ocultar todos los paneles
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        // Desactivar todos los tabs
        document.querySelectorAll('.tab-btn').forEach(t => {
            t.classList.remove('border-indigo-500', 'text-indigo-600', 'border-orange-500', 'text-orange-600', 'border-cyan-500', 'text-cyan-600');
            t.classList.add('border-transparent', 'text-gray-500');
        });

        // Mostrar panel seleccionado
        document.getElementById(`panel-${tab}`).classList.remove('hidden');

        // Activar tab
        const tabBtn = document.getElementById(`tab-${tab}`);
        tabBtn.classList.remove('border-transparent', 'text-gray-500');
        if (tab === 'medicamentos') {
            tabBtn.classList.add('border-indigo-500', 'text-indigo-600');
        } else if (tab === 'catering') {
            tabBtn.classList.add('border-orange-500', 'text-orange-600');
        } else if (tab === 'drenajes') {
            tabBtn.classList.add('border-cyan-500', 'text-cyan-600');
        }

        // Cargar datos
        if (tab === 'medicamentos') cargarMedicamentos();
        else if (tab === 'catering') cargarCatering();
        else if (tab === 'drenajes') cargarDrenajes();
    }

    // ==================== MEDICAMENTOS ====================
    let medicamentoSeleccionado = null;
    let timeoutBusqueda = null;

    async function cargarMedicamentos() {
        await cargarMedicamentosPaciente();
        setupBuscadorMedicamentos();
    }

    function setupBuscadorMedicamentos() {
        const inputBuscar = document.getElementById('buscarMedicamento');
        const resultadosDiv = document.getElementById('resultadosBusqueda');

        inputBuscar.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(timeoutBusqueda);

            if (query.length < 2) {
                resultadosDiv.classList.add('hidden');
                return;
            }

            // Debounce para no hacer muchas peticiones
            timeoutBusqueda = setTimeout(() => buscarMedicamentos(query), 300);
        });

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!inputBuscar.contains(e.target) && !resultadosDiv.contains(e.target)) {
                resultadosDiv.classList.add('hidden');
            }
        });

        // Focus en el input muestra resultados si hay texto
        inputBuscar.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                buscarMedicamentos(this.value.trim());
            }
        });
    }

    async function buscarMedicamentos(query) {
        try {
            const response = await fetch(`/internacion-staff/api/medicamentos/buscar?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            const resultadosDiv = document.getElementById('resultadosBusqueda');

            if (data.success && data.medicamentos.length > 0) {
                resultadosDiv.innerHTML = data.medicamentos.map(m => `
                    <div onclick="seleccionarMedicamento(${m.id}, '${m.nombre.replace(/'/g, "\\'")}', '${m.unidad_medida}', ${m.cantidad}, ${m.precio || 0}, '${m.tipo}')"
                         class="p-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-0">
                        <p class="font-medium text-gray-800">${m.nombre}</p>
                        <p class="text-xs text-gray-500">${m.tipo} - Stock: ${m.cantidad} ${m.unidad_medida} - Bs. ${m.precio || 0}</p>
                    </div>
                `).join('');
                resultadosDiv.classList.remove('hidden');
            } else {
                resultadosDiv.innerHTML = '<div class="p-3 text-gray-500 text-sm text-center">No se encontraron medicamentos</div>';
                resultadosDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error al buscar medicamentos:', error);
        }
    }

    function seleccionarMedicamento(id, nombre, unidad, stock, precio, tipo) {
        medicamentoSeleccionado = { id, nombre, unidad, stock, precio, tipo };

        // Guardar en campo hidden
        document.getElementById('medicamentoIdSeleccionado').value = id;

        // Mostrar el medicamento seleccionado
        document.getElementById('nombreMedicamentoSel').textContent = nombre;
        document.getElementById('infoMedicamentoSel').textContent = `${tipo} - Stock: ${stock} ${unidad} - Bs. ${precio}`;
        document.getElementById('medicamentoSeleccionado').classList.remove('hidden');

        // Actualizar display de unidad
        document.getElementById('unidadMedicamentoDisplay').textContent = unidad;
        document.getElementById('stock-info').textContent = `Stock disponible: ${stock} ${unidad}`;

        // Limpiar buscador y ocultar resultados
        document.getElementById('buscarMedicamento').value = '';
        document.getElementById('resultadosBusqueda').classList.add('hidden');
    }

    function limpiarMedicamentoSeleccionado() {
        medicamentoSeleccionado = null;
        document.getElementById('medicamentoIdSeleccionado').value = '';
        document.getElementById('medicamentoSeleccionado').classList.add('hidden');
        document.getElementById('unidadMedicamentoDisplay').textContent = '';
        document.getElementById('stock-info').textContent = '';
        document.getElementById('buscarMedicamento').focus();
    }

    async function cargarMedicamentosPaciente() {
        try {
            const response = await fetch(`/internacion-staff/api/internacion/${hospitalizacionId}/medicamentos`);
            const data = await response.json();
            const lista = document.getElementById('listaMedicamentos');
            if (data.success) {
                medicamentosPaciente = data.medicamentos;
                document.getElementById('count-medicamentos').textContent = data.medicamentos.length;

                if (data.medicamentos.length > 0) {
                    lista.innerHTML = data.medicamentos.map(m => `
                        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl hover:shadow-sm transition">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">${m.medicamento}</p>
                                    <p class="text-sm text-gray-500">${m.cantidad} ${m.unidad} - ${m.fecha} ${m.hora}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        ${m.via_administracion ? `<span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">${m.via_administracion}</span>` : ''}
                                        <span class="text-xs text-gray-400">por ${m.administrado_por}</span>
                                    </div>
                                    ${m.observaciones ? `<p class="text-xs text-gray-500 mt-1">${m.observaciones}</p>` : ''}
                                </div>
                            </div>
                            ${m.cargo_generado ? '<span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Cargo generado</span>' : ''}
                        </div>
                    `).join('');
                } else {
                    lista.innerHTML = `
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            <p class="text-gray-400">No hay medicamentos registrados</p>
                            <p class="text-sm text-gray-300 mt-1">Use el formulario para agregar el primero</p>
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error('Error al cargar medicamentos del paciente:', error);
        }
    }

    document.getElementById('formMedicamento').addEventListener('submit', async function(e) {
        e.preventDefault();

        const medicamentoId = document.getElementById('medicamentoIdSeleccionado').value;
        const cantidad = document.getElementById('cantidadMedicamento').value;
        const via = document.getElementById('viaMedicamento').value;
        const obs = document.getElementById('obsMedicamento').value;

        // Validar que haya un medicamento seleccionado
        if (!medicamentoId || !medicamentoSeleccionado) {
            alert('Por favor busque y seleccione un medicamento');
            document.getElementById('buscarMedicamento').focus();
            return;
        }

        // Validar stock suficiente
        if (parseFloat(cantidad) > medicamentoSeleccionado.stock) {
            alert(`Stock insuficiente. Disponible: ${medicamentoSeleccionado.stock} ${medicamentoSeleccionado.unidad}`);
            return;
        }

        try {
            const response = await fetch(`/internacion-staff/api/internacion/${hospitalizacionId}/medicamentos`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    medicamento_id: medicamentoId,
                    cantidad: cantidad,
                    unidad: medicamentoSeleccionado.unidad, // La unidad viene del sistema
                    via_administracion: via,
                    observaciones: obs
                })
            });

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                // Limpiar formulario
                document.getElementById('formMedicamento').reset();
                limpiarMedicamentoSeleccionado();
                await cargarMedicamentosPaciente();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al registrar medicamento');
        }
    });

    // ==================== CATERING ====================
    async function cargarCatering() {
        try {
            const response = await fetch(`/internacion-staff/api/internacion/${hospitalizacionId}/catering`);
            const data = await response.json();
            if (data.success) {
                const dadosCount = data.catering.filter(c => c.estado === 'dado').length;
                document.getElementById('count-catering').textContent = dadosCount;

                const grid = document.getElementById('catering-grid');
                grid.innerHTML = data.catering.map(c => `
                    <div class="border-2 rounded-xl p-5 ${c.estado === 'dado' ? 'bg-green-50 border-green-300' : c.estado === 'no_aplica' ? 'bg-gray-50 border-gray-300' : 'bg-white border-gray-200'}">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 ${c.estado === 'dado' ? 'bg-green-100' : 'bg-gray-100'} rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 ${c.estado === 'dado' ? 'text-green-600' : 'text-gray-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">${c.tipo_label}</h4>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full ${c.estado_color === 'green' ? 'bg-green-100 text-green-700' : c.estado_color === 'red' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600'}">${c.estado_label}</span>
                                </div>
                            </div>
                            ${c.cargo_generado ? `<span class="text-xs font-medium text-green-600">Bs. ${c.precio}</span>` : ''}
                        </div>
                        ${c.hora_registro ? `<p class="text-sm text-gray-500 mb-3">Registrado a las ${c.hora_registro}</p>` : ''}
                        <div class="grid grid-cols-3 gap-2">
                            <button onclick="guardarCatering('${c.tipo_comida}', 'dado')" class="py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition ${c.estado === 'dado' ? 'ring-2 ring-green-300' : ''}">Dado</button>
                            <button onclick="guardarCatering('${c.tipo_comida}', 'no_dado')" class="py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition ${c.estado === 'no_dado' ? 'ring-2 ring-red-300' : ''}">No Dado</button>
                            <button onclick="guardarCatering('${c.tipo_comida}', 'no_aplica')" class="py-2 bg-gray-400 text-white text-sm font-medium rounded-lg hover:bg-gray-500 transition ${c.estado === 'no_aplica' ? 'ring-2 ring-gray-300' : ''}">N/A</button>
                        </div>
                    </div>
                `).join('');
            }
        } catch (error) {
            console.error('Error al cargar catering:', error);
        }
    }

    async function guardarCatering(tipoComida, estado) {
        try {
            const response = await fetch(`/internacion-staff/api/internacion/${hospitalizacionId}/catering`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tipo_comida: tipoComida,
                    estado: estado
                })
            });

            const data = await response.json();
            if (data.success) {
                await cargarCatering();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al registrar catering');
        }
    }

    // ==================== DRENAJES ====================
    async function cargarDrenajes() {
        try {
            const response = await fetch(`/internacion-staff/api/internacion/${hospitalizacionId}/drenajes`);
            const data = await response.json();
            const lista = document.getElementById('listaDrenajes');
            if (data.success) {
                drenajesPaciente = data.drenajes;
                document.getElementById('count-drenajes').textContent = data.drenajes.length;

                if (data.drenajes.length > 0) {
                    lista.innerHTML = data.drenajes.map(d => `
                        <div class="flex items-center justify-between p-4 ${d.realizado ? 'bg-cyan-50 border-cyan-200' : 'bg-gray-50 border-gray-200'} border rounded-xl">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 ${d.realizado ? 'bg-cyan-100' : 'bg-gray-200'} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 ${d.realizado ? 'text-cyan-600' : 'text-gray-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">${d.tipo_drenaje || 'Drenaje General'}</p>
                                    <p class="text-sm text-gray-500">${d.fecha} ${d.hora || ''}</p>
                                    <p class="text-xs text-gray-400 mt-1">Registrado por: ${d.registrado_por}</p>
                                    ${d.observaciones ? `<p class="text-xs text-gray-500 mt-1">${d.observaciones}</p>` : ''}
                                </div>
                            </div>
                            <div class="text-right">
                                ${d.realizado
                                    ? `<span class="text-xs bg-cyan-100 text-cyan-700 px-2 py-1 rounded-full font-medium">Realizado</span>${d.cargo_generado ? `<p class="text-xs text-green-600 mt-1">Cargo: Bs. ${d.precio}</p>` : ''}`
                                    : `<span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">No realizado</span>`
                                }
                            </div>
                        </div>
                    `).join('');
                } else {
                    lista.innerHTML = `
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            <p class="text-gray-400">No hay drenajes registrados</p>
                            <p class="text-sm text-gray-300 mt-1">Use el formulario para agregar el primero</p>
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error('Error al cargar drenajes:', error);
        }
    }

    document.getElementById('formDrenaje').addEventListener('submit', async function(e) {
        e.preventDefault();

        const tipo = document.getElementById('tipoDrenaje').value;
        const realizado = document.getElementById('drenajeRealizado').checked;
        const obs = document.getElementById('obsDrenaje').value;

        try {
            const response = await fetch(`/internacion-staff/api/internacion/${hospitalizacionId}/drenajes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tipo_drenaje: tipo,
                    realizado: realizado,
                    observaciones: obs
                })
            });

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                document.getElementById('formDrenaje').reset();
                await cargarDrenajes();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al registrar drenaje');
        }
    });

    // Cargar primera tab al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        cargarMedicamentos();
    });
</script>
@endsection
