<x-app-layout>
    <div x-data="{ tab: 'agenda' }" class="p-6">

        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Consulta Externa</h1>
                <p class="text-sm text-gray-500">Dr. {{ $medico->user->name ?? 'Médico' }} - {{ $medico->especialidad->nombre ?? 'Especialidad' }}</p>
            </div>
            <div class="flex gap-3">
                <button onclick="verAgenda()" class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
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
                <span class="font-bold">Consultas Pagadas - {{ now()->format('d/m/Y') }}</span>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($consultasPendientes as $consulta)
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-6">
                            <div class="text-blue-600 font-bold text-lg">{{ \Carbon\Carbon::parse($consulta->hora)->format('H:i') }}</div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $consulta->paciente->nombre }}</h4>
                                <p class="text-xs text-gray-400">CI: {{ $consulta->paciente->ci }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $consulta->motivo }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">Pagado</span>
                            <button data-paciente-ci="{{ $consulta->ci_paciente }}" data-consulta-nro="{{ $consulta->nro }}" onclick="handleVerButton(this)" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-900 shadow-sm transition">
                                Ver
                            </button>
                            <button data-consulta-nro="{{ $consulta->nro }}" onclick="handleIniciarConsulta(this)" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                                Atender
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-lg font-medium">No hay consultas pagadas pendientes</p>
                        <p class="text-sm mt-2">Los pacientes aparecerán aquí una vez que realicen el pago en caja</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div x-show="tab === 'atencion'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center gap-2 text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold">Consulta en Atención</span>
            </div>

            @forelse ($consultasEnAtencion as $consulta)
                <div class="p-6">
                    <div class="flex items-center gap-6 mb-4">
                        <div class="text-green-600 font-bold text-lg">{{ \Carbon\Carbon::parse($consulta->hora)->format('H:i') }}</div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800">{{ $consulta->paciente->nombre }}</h4>
                            <p class="text-xs text-gray-400">CI: {{ $consulta->paciente->ci }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $consulta->motivo }}</p>
                        </div>
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full border border-orange-200">
                            ● En Atención
                        </span>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h5 class="font-semibold text-gray-800 mb-3">Detalles de la Consulta</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Especialidad:</p>
                                <p class="font-medium">{{ $consulta->especialidad->nombre ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fecha:</p>
                                <p class="font-medium">{{ $consulta->fecha->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">Observaciones:</p>
                            <textarea class="w-full border border-gray-200 rounded-lg p-3 text-sm" rows="3" placeholder="Agregar observaciones de la consulta...">{{ $consulta->observaciones ?? '' }}</textarea>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-4">
                            <button data-consulta-nro="{{ $consulta->nro }}" onclick="handleCompletarConsulta(this)" class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm">
                                Completar Consulta
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-lg font-medium">No hay consultas en atención</p>
                </div>
            @endforelse
        </div>

        <div x-show="tab === 'historial'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center gap-2 text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold">Consultas Completadas Hoy</span>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($consultasCompletadas as $consulta)
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-6">
                            <div class="text-blue-600 font-bold text-lg">{{ \Carbon\Carbon::parse($consulta->hora)->format('H:i') }}</div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $consulta->paciente->nombre }}</h4>
                                <p class="text-xs text-gray-400">CI: {{ $consulta->paciente->ci }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $consulta->motivo }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            Completado
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-lg font-medium">No hay consultas completadas hoy</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div x-show="tab === 'atencion'" x-cloak>
            @include('medical.partials.atencion-actual')
        </div>

        <div x-show="tab === 'historial'" x-cloak>
            @include('medical.partials.historial')
        </div>

    </div>

    <script>
        function handleVerButton(button) {
            const pacienteCi = button.getAttribute('data-paciente-ci');
            const consultaNro = button.getAttribute('data-consulta-nro');
            
            console.log('handleVerButton called with:', pacienteCi, consultaNro);
            alert('Botón Ver funciona! CI: ' + pacienteCi + ', Consulta: ' + consultaNro);
            
            verDetallesPaciente(pacienteCi, consultaNro);
        }

        function handleIniciarConsulta(button) {
            const consultaId = button.getAttribute('data-consulta-nro');
            iniciarConsulta(consultaId);
        }

        function handleCompletarConsulta(button) {
            const consultaId = button.getAttribute('data-consulta-nro');
            completarConsulta(consultaId);
        }

        function verAgenda() {
            // Switch to the agenda tab
            document.querySelector('[x-data]')._x_dataStack[0].tab = 'agenda';
        }

        function verDetallesPaciente(pacienteCi, consultaNro) {
            console.log('verDetallesPaciente called with:', pacienteCi, consultaNro);
            
            // Show loading indicator
            alert('Cargando datos del paciente CI: ' + pacienteCi);
            
            // Show patient details in a modal or redirect to patient details
            fetch(`/api/paciente/${pacienteCi}?t=${Date.now()}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        mostrarModalPaciente(data.paciente, consultaNro);
                    } else {
                        alert('Error al obtener datos del paciente: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al obtener datos del paciente: ' + error.message);
                });
        }

        function mostrarModalPaciente(paciente, consultaNro) {
            // Create modal HTML
            const modalHtml = `
                <div id="pacienteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Detalles del Paciente</h3>
                            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Nombre:</p>
                                <p class="font-medium">${paciente.nombre}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">CI:</p>
                                <p class="font-medium">${paciente.ci}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Edad:</p>
                                <p class="font-medium">${paciente.edad || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Género:</p>
                                <p class="font-medium">${paciente.genero || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Teléfono:</p>
                                <p class="font-medium">${paciente.telefono || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Dirección:</p>
                                <p class="font-medium">${paciente.direccion || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Consulta:</p>
                                <p class="font-medium">${consultaNro}</p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button onclick="cerrarModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to page
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        function cerrarModal() {
            const modal = document.getElementById('pacienteModal');
            if (modal) {
                modal.remove();
            }
        }

        function iniciarConsulta(consultaId) {
            if (confirm('¿Está seguro de iniciar esta consulta?')) {
                fetch(`/consulta-externa/iniciar/${consultaId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al iniciar la consulta: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al iniciar la consulta');
                });
            }
        }

        function completarConsulta(consultaId) {
            if (confirm('¿Está seguro de completar esta consulta?')) {
                const observaciones = prompt('Observaciones de la consulta (opcional):');
                
                fetch(`/consulta-externa/completar/${consultaId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        observaciones: observaciones
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al completar la consulta: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al completar la consulta');
                });
            }
        }
    </script>
</x-app-layout>
