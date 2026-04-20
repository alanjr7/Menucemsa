@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard de Internación</h1>
            <p class="text-sm text-gray-500">Personal de Internación - Gestión de pacientes</p>
        </div>
        <div class="flex gap-3">
            <button onclick="cargarInternaciones()" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar
            </button>
            <a href="{{ route('internacion-staff.habitaciones.index') }}" class="flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Gestionar Habitaciones
            </a>
            <a href="{{ route('internacion-staff.medicamentos.index') }}" class="flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                Gestionar Medicamentos
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Pacientes Activos</span>
            <span class="text-3xl font-bold text-blue-600" id="stat-activos">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">En Espera</span>
            <span class="text-3xl font-bold text-yellow-600" id="stat-espera">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">En Atención</span>
            <span class="text-3xl font-bold text-green-600" id="stat-atencion">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Hoy Ingresados</span>
            <span class="text-3xl font-bold text-indigo-600" id="stat-hoy">0</span>
        </div>
    </div>

    <!-- Lista de Pacientes en Internación -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-gray-800">Pacientes en Internación</h2>
            <div class="flex gap-2">
                <select id="filtro-estado" onchange="cargarInternaciones()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500">
                    <option value="todos">Todos los estados</option>
                    <option value="activo">Activos</option>
                    <option value="en_observacion">En Observación</option>
                    <option value="estable">Estables</option>
                    <option value="critico">Críticos</option>
                </select>
            </div>
        </div>

        <!-- Tabla de Pacientes -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 rounded-l-lg">Código</th>
                        <th scope="col" class="px-6 py-3">Paciente</th>
                        <th scope="col" class="px-6 py-3">Tipo</th>
                        <th scope="col" class="px-6 py-3">Servicio</th>
                        <th scope="col" class="px-6 py-3">Habitación</th>
                        <th scope="col" class="px-6 py-3">Ingreso</th>
                        <th scope="col" class="px-6 py-3">Estado</th>
                        <th scope="col" class="px-6 py-3 rounded-r-lg">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-internaciones">
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p>Cargando pacientes...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Acciones -->
<div id="modalAcciones" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Acciones del Paciente</h3>
                    <p class="text-blue-100 text-sm mt-1" id="modal-paciente-nombre"></p>
                </div>
                <button onclick="cerrarModal()" class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <button onclick="cambiarEstado('estable')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-green-50 hover:border-green-300 transition-all text-left">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-800">Marcar Estable</span>
                        <p class="text-xs text-gray-500">Paciente estable</p>
                    </div>
                </button>

                <button onclick="cambiarEstado('critico')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-red-50 hover:border-red-300 transition-all text-left">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-800">Marcar Crítico</span>
                        <p class="text-xs text-gray-500">Paciente en estado crítico</p>
                    </div>
                </button>

                <button onclick="derivarAUti()" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-red-50 hover:border-red-300 transition-all text-left">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-800">Enviar a UTI</span>
                        <p class="text-xs text-gray-500">Unidad de Terapia Intensiva</p>
                    </div>
                </button>

                <button onclick="darAlta()" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-green-50 hover:border-green-300 transition-all text-left">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-800">Dar Alta</span>
                        <p class="text-xs text-gray-500">Dar de alta al paciente</p>
                    </div>
                </button>

                <div class="border-t border-gray-200 my-3 col-span-full"></div>

                <a id="linkEvaluar" href="#" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 transition-all text-left">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-800">Evaluar Paciente</span>
                        <p class="text-xs text-gray-500">Medicamentos, catering, drenajes...</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    let internacionSeleccionada = null;
    let datosInternaciones = [];

    // Cargar internaciones al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        cargarInternaciones();
    });

    async function cargarInternaciones() {
        try {
            const filtro = document.getElementById('filtro-estado').value;
            let url = '/internacion-staff/api/internaciones';
            if (filtro !== 'todos') {
                url += `?estado=${filtro}`;
            }

            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                datosInternaciones = data.internaciones;
                mostrarInternaciones(data.internaciones);
                actualizarStats(data.stats);
            }
        } catch (error) {
            console.error('Error al cargar internaciones:', error);
            document.getElementById('tabla-internaciones').innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                        <p>Error al cargar pacientes</p>
                    </td>
                </tr>
            `;
        }
    }

    function mostrarInternaciones(internaciones) {
        const tbody = document.getElementById('tabla-internaciones');

        if (internaciones.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No hay pacientes en internación</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = internaciones.map(int => {
            const estadoClass = {
                'activo': 'bg-blue-100 text-blue-800',
                'en_observacion': 'bg-yellow-100 text-yellow-800',
                'estable': 'bg-green-100 text-green-800',
                'critico': 'bg-red-100 text-red-800',
                'alta': 'bg-gray-100 text-gray-800',
                'trasladado': 'bg-purple-100 text-purple-800'
            }[int.estado] || 'bg-gray-100 text-gray-800';

            const estadoLabel = {
                'activo': 'Activo',
                'en_observacion': 'En Observación',
                'estable': 'Estable',
                'critico': 'Crítico',
                'alta': 'Alta',
                'trasladado': 'Trasladado'
            }[int.estado] || int.estado;

            return `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-900">${int.codigo}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">${int.paciente_nombre}</div>
                        <div class="text-xs text-gray-500">CI: ${int.paciente_id}</div>
                    </td>
                    <td class="px-6 py-4 capitalize">${int.tipo}</td>
                    <td class="px-6 py-4">${int.servicio || '-'}</td>
                    <td class="px-6 py-4">
                        <span class="${int.habitacion === 'Por asignar' ? 'text-yellow-600' : 'text-gray-900'}">
                            ${int.habitacion}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-900">${int.fecha_ingreso}</div>
                        <div class="text-xs text-gray-500">${int.hora_ingreso}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoClass}">
                            ${estadoLabel}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="abrirModal('${int.id}')" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Acciones
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function actualizarStats(stats) {
        document.getElementById('stat-activos').textContent = stats.activos;
        document.getElementById('stat-espera').textContent = stats.espera;
        document.getElementById('stat-atencion').textContent = stats.atencion;
        document.getElementById('stat-hoy').textContent = stats.hoy;
    }

    function abrirModal(id) {
        internacionSeleccionada = datosInternaciones.find(i => i.id === id);
        if (internacionSeleccionada) {
            document.getElementById('modal-paciente-nombre').textContent = internacionSeleccionada.paciente_nombre;
            document.getElementById('modalAcciones').classList.remove('hidden');

            // Actualizar link de evaluar
            document.getElementById('linkEvaluar').href = `/internacion-staff/evaluar/${internacionSeleccionada.id}`;
        }
    }

    function cerrarModal() {
        document.getElementById('modalAcciones').classList.add('hidden');
        internacionSeleccionada = null;
    }

    async function cambiarEstado(nuevoEstado) {
        if (!internacionSeleccionada) return;

        try {
            const response = await fetch(`/internacion-staff/api/internacion/${internacionSeleccionada.id}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: nuevoEstado })
            });

            const data = await response.json();

            if (data.success) {
                cerrarModal();
                cargarInternaciones();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cambiar estado');
        }
    }

    async function derivarAUti() {
        if (!internacionSeleccionada) return;

        if (!confirm('¿Está seguro de enviar este paciente a UTI?')) return;

        try {
            const response = await fetch(`/internacion-staff/api/internacion/${internacionSeleccionada.id}/derivar-uti`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                alert('Paciente derivado a UTI correctamente. Nro de ingreso: ' + data.admission.nro_ingreso);
                cerrarModal();
                cargarInternaciones();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al derivar a UTI');
        }
    }

    async function darAlta() {
        if (!internacionSeleccionada) return;

        const motivo = prompt('Ingrese el motivo del alta (opcional):');
        if (motivo === null) return; // Usuario canceló

        try {
            const response = await fetch(`/internacion-staff/api/internacion/${internacionSeleccionada.id}/alta`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ motivo_alta: motivo })
            });

            const data = await response.json();

            if (data.success) {
                alert('Paciente dado de alta correctamente');
                cerrarModal();
                cargarInternaciones();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al dar alta');
        }
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalAcciones').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });

</script>
@endsection
