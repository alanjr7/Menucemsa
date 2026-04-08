@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Emergencias</h1>
            <p class="text-sm text-gray-500">Admin - Monitoreo Global (Solo Lectura)</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
            <button onclick="cargarDatos()" class="flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar
            </button>
        </div>
    </div>

   


    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-xs font-medium mb-1">Total Ingresos</span>
            <span class="text-2xl font-bold text-gray-800" id="stat-total">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-xs font-medium mb-1">Pacientes Activos</span>
            <span class="text-2xl font-bold text-red-600" id="stat-activos">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-xs font-medium mb-1">En UTI</span>
            <span class="text-2xl font-bold text-orange-600" id="stat-uti">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-xs font-medium mb-1">En Cirugía</span>
            <span class="text-2xl font-bold text-purple-600" id="stat-cirugia">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-xs font-medium mb-1">Dados de Alta</span>
            <span class="text-2xl font-bold text-green-600" id="stat-alta">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-xs font-medium mb-1">Deuda Total</span>
            <span class="text-xl font-bold text-red-600" id="stat-deuda">Bs. 0</span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" id="filtro-buscar" placeholder="Código, paciente..." class="border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 w-64">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ingreso</label>
                <select id="filtro-tipo" class="border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="soat">SOAT</option>
                    <option value="parto">Parto</option>
                    <option value="general">General</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                <select id="filtro-ubicacion" class="border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:border-blue-500">
                    <option value="">Todas</option>
                    <option value="emergencia">Emergencia</option>
                    <option value="cirugia">Cirugía</option>
                    <option value="uti">UTI</option>
                    <option value="hospitalizacion">Hospitalización</option>
                    <option value="observacion">Observación</option>
                    <option value="alta">Alta</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                <input type="date" id="filtro-fecha-desde" class="border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                <input type="date" id="filtro-fecha-hasta" class="border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:border-blue-500">
            </div>
            <button onclick="aplicarFiltros()" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors text-sm">
                Filtrar
            </button>
            <button onclick="limpiarFiltros()" class="px-6 py-2 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors text-sm">
                Limpiar
            </button>
        </div>
    </div>

    <!-- Tabla de Emergencias -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Historial de Emergencias</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Código</th>
                        <th scope="col" class="px-6 py-3">Paciente</th>
                        <th scope="col" class="px-6 py-3">Tipo Ingreso</th>
                        <th scope="col" class="px-6 py-3">Ubicación Actual</th>
                        <th scope="col" class="px-6 py-3">Flujo</th>
                        <th scope="col" class="px-6 py-3">Estado Financiero</th>
                        <th scope="col" class="px-6 py-3">Ingreso</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-emergencias">
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p>Cargando datos...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Mostrando <span id="mostrando-cantidad">0</span> resultados</span>
                <div class="flex gap-2" id="paginacion">
                    <!-- Paginación dinámica -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle -->
<div id="modalDetalle" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Detalle de Emergencia</h3>
                    <p class="text-blue-100 text-sm mt-1" id="modal-codigo"></p>
                </div>
                <button onclick="cerrarModal()" class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6 space-y-6" id="modal-contenido">
            <!-- Contenido dinámico -->
        </div>
    </div>
</div>

<script>
    let paginaActual = 1;
    let datosCompletos = [];

    document.addEventListener('DOMContentLoaded', function() {
        cargarDatos();
        // Auto-refresh cada 60 segundos
        setInterval(cargarDatos, 60000);
    });

    async function cargarDatos() {
        try {
            const response = await fetch('/admin/api/emergencias');
            const data = await response.json();
            
            if (data.success) {
                datosCompletos = data.emergencias;
                actualizarEstadisticas(data.stats);
                aplicarFiltros();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function actualizarEstadisticas(stats) {
        document.getElementById('stat-total').textContent = stats.total || 0;
        document.getElementById('stat-activos').textContent = stats.activos || 0;
        document.getElementById('stat-uti').textContent = stats.uti || 0;
        document.getElementById('stat-cirugia').textContent = stats.cirugia || 0;
        document.getElementById('stat-alta').textContent = stats.alta || 0;
        document.getElementById('stat-deuda').textContent = 'Bs. ' + (stats.deuda_total || 0).toLocaleString();
    }

    function aplicarFiltros() {
        const buscar = document.getElementById('filtro-buscar').value.toLowerCase();
        const tipo = document.getElementById('filtro-tipo').value;
        const ubicacion = document.getElementById('filtro-ubicacion').value;
        const fechaDesde = document.getElementById('filtro-fecha-desde').value;
        const fechaHasta = document.getElementById('filtro-fecha-hasta').value;

        let filtrados = datosCompletos.filter(emp => {
            const matchBuscar = !buscar || 
                emp.code.toLowerCase().includes(buscar) || 
                emp.paciente_nombre.toLowerCase().includes(buscar) ||
                emp.patient_id.toLowerCase().includes(buscar);
            
            const matchTipo = !tipo || emp.tipo_ingreso === tipo;
            const matchUbicacion = !ubicacion || emp.ubicacion_actual === ubicacion;
            
            let matchFecha = true;
            if (fechaDesde) {
                matchFecha = matchFecha && new Date(emp.admission_date) >= new Date(fechaDesde);
            }
            if (fechaHasta) {
                matchFecha = matchFecha && new Date(emp.admission_date) <= new Date(fechaHasta + 'T23:59:59');
            }

            return matchBuscar && matchTipo && matchUbicacion && matchFecha;
        });

        mostrarEmergencias(filtrados);
    }

    function limpiarFiltros() {
        document.getElementById('filtro-buscar').value = '';
        document.getElementById('filtro-tipo').value = '';
        document.getElementById('filtro-ubicacion').value = '';
        document.getElementById('filtro-fecha-desde').value = '';
        document.getElementById('filtro-fecha-hasta').value = '';
        aplicarFiltros();
    }

    function mostrarEmergencias(emergencias) {
        const tbody = document.getElementById('tabla-emergencias');
        document.getElementById('mostrando-cantidad').textContent = emergencias.length;
        
        if (emergencias.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No se encontraron emergencias</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = emergencias.map(emp => `
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-900">${emp.code}</td>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${emp.paciente_nombre}</div>
                    <div class="text-xs text-gray-500">${emp.is_temp_id ? 'ID Temporal' : 'CI: ' + emp.patient_id}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${getTipoIngresoClass(emp.tipo_ingreso)}">
                        ${emp.tipo_ingreso_label}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${getUbicacionClass(emp.ubicacion_actual)}">
                        ${emp.ubicacion_label}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-1">
                        ${generarFlujoVisual(emp.flujo_historial)}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm">
                        <div>Costo: <strong>Bs. ${(emp.cost || 0).toLocaleString()}</strong></div>
                        <div class="text-xs ${emp.deuda > 0 ? 'text-red-600' : 'text-green-600'}">
                            ${emp.deuda > 0 ? 'Deuda: Bs. ' + emp.deuda.toLocaleString() : 'Pagado'}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-xs">
                    ${emp.admission_date ? new Date(emp.admission_date).toLocaleDateString() : '-'}
                </td>
                <td class="px-6 py-4">
                    <button onclick="verDetalle(${emp.id})" class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                        Ver Detalle
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function getTipoIngresoClass(tipo) {
        const classes = {
            'soat': 'bg-orange-100 text-orange-800',
            'parto': 'bg-pink-100 text-pink-800',
            'general': 'bg-blue-100 text-blue-800'
        };
        return classes[tipo] || 'bg-gray-100 text-gray-800';
    }

    function getUbicacionClass(ubicacion) {
        const classes = {
            'emergencia': 'bg-red-100 text-red-800',
            'cirugia': 'bg-purple-100 text-purple-800',
            'uti': 'bg-orange-100 text-orange-800',
            'hospitalizacion': 'bg-indigo-100 text-indigo-800',
            'observacion': 'bg-yellow-100 text-yellow-800',
            'alta': 'bg-green-100 text-green-800'
        };
        return classes[ubicacion] || 'bg-gray-100 text-gray-800';
    }

    function generarFlujoVisual(flujo) {
        if (!flujo || flujo.length === 0) {
            return '<span class="text-gray-400">Sin movimientos</span>';
        }
        
        const iconos = {
            'recepcion': '<span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs">R</span>',
            'emergencia': '<span class="w-6 h-6 rounded-full bg-red-200 flex items-center justify-center text-xs text-red-700">E</span>',
            'cirugia': '<span class="w-6 h-6 rounded-full bg-purple-200 flex items-center justify-center text-xs text-purple-700">Q</span>',
            'uti': '<span class="w-6 h-6 rounded-full bg-orange-200 flex items-center justify-center text-xs text-orange-700">U</span>',
            'hospitalizacion': '<span class="w-6 h-6 rounded-full bg-indigo-200 flex items-center justify-center text-xs text-indigo-700">H</span>',
            'alta': '<span class="w-6 h-6 rounded-full bg-green-200 flex items-center justify-center text-xs text-green-700">A</span>'
        };

        const pasos = ['recepcion', 'emergencia'];
        flujo.forEach(mov => {
            if (mov.hasta && !pasos.includes(mov.hasta)) {
                pasos.push(mov.hasta);
            }
        });

        return pasos.map((paso, index) => `
            <div class="flex items-center">
                ${iconos[paso] || '<span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs">?</span>'}
                ${index < pasos.length - 1 ? '<span class="mx-1 text-gray-400">→</span>' : ''}
            </div>
        `).join('');
    }

    async function verDetalle(id) {
        try {
            const response = await fetch(`/admin/api/emergencias/${id}`);
            const data = await response.json();
            
            if (data.success) {
                mostrarModalDetalle(data.emergencia);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function mostrarModalDetalle(emp) {
        document.getElementById('modal-codigo').textContent = emp.code;
        
        const contenido = document.getElementById('modal-contenido');
        contenido.innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Información del Paciente</h4>
                    <p><strong>Nombre:</strong> ${emp.paciente_nombre}</p>
                    <p><strong>ID:</strong> ${emp.patient_id}</p>
                    <p><strong>Tipo:</strong> ${emp.is_temp_id ? 'ID Temporal' : 'Paciente Registrado'}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Información de Ingreso</h4>
                    <p><strong>Tipo:</strong> ${emp.tipo_ingreso_label}</p>
                    <p><strong>Fecha:</strong> ${emp.admission_date ? new Date(emp.admission_date).toLocaleString() : '-'}</p>
                    <p><strong>Destino Inicial:</strong> ${emp.destino_inicial || '-'}</p>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Ubicación y Estado</h4>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-sm font-medium ${getUbicacionClass(emp.ubicacion_actual)}">
                        ${emp.ubicacion_label}
                    </span>
                    <span class="text-gray-400">|</span>
                    <span class="text-sm">Estado: ${emp.status}</span>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Flujo del Paciente</h4>
                <div class="bg-gray-50 rounded-xl p-4">
                    ${emp.flujo_historial && emp.flujo_historial.length > 0 ? 
                        emp.flujo_historial.map(mov => `
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-0">
                                <div>
                                    <span class="text-sm font-medium">${mov.desde} → ${mov.hasta}</span>
                                    <p class="text-xs text-gray-500">${mov.notas || ''}</p>
                                </div>
                                <span class="text-xs text-gray-400">${new Date(mov.fecha).toLocaleString()}</span>
                            </div>
                        `).join('') : 
                        '<p class="text-gray-400 text-sm">Sin movimientos registrados</p>'
                    }
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Estado Financiero</h4>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <div class="text-xs text-gray-500">Costo Total</div>
                        <div class="font-bold text-lg">Bs. ${(emp.cost || 0).toLocaleString()}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <div class="text-xs text-gray-500">Pagado</div>
                        <div class="font-bold text-lg text-green-600">Bs. ${(emp.total_pagado || 0).toLocaleString()}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <div class="text-xs text-gray-500">Deuda</div>
                        <div class="font-bold text-lg ${emp.deuda > 0 ? 'text-red-600' : 'text-gray-600'}">Bs. ${(emp.deuda || 0).toLocaleString()}</div>
                    </div>
                </div>
            </div>
            
            ${emp.sintomas ? `
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Síntomas y Observaciones</h4>
                    <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">${emp.sintomas}</p>
                </div>
            ` : ''}
            
            ${emp.es_parto ? `
                <div class="bg-pink-50 border border-pink-200 rounded-xl p-4">
                    <h4 class="font-semibold text-pink-700 mb-2">Información de Parto</h4>
                    <p class="text-sm text-pink-600">Este paciente fue ingresado por emergencia obstétrica</p>
                </div>
            ` : ''}
            
            ${emp.nro_cirugia || emp.nro_hospitalizacion || emp.nro_uti ? `
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Referencias a Otros Módulos</h4>
                    ${emp.nro_cirugia ? `<p class="text-sm"><strong>Cirugía:</strong> ${emp.nro_cirugia}</p>` : ''}
                    ${emp.nro_hospitalizacion ? `<p class="text-sm"><strong>Hospitalización:</strong> ${emp.nro_hospitalizacion}</p>` : ''}
                    ${emp.nro_uti ? `<p class="text-sm"><strong>UTI:</strong> ${emp.nro_uti}</p>` : ''}
                </div>
            ` : ''}
        `;
        
        document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function cerrarModal() {
        document.getElementById('modalDetalle').classList.add('hidden');
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('modalDetalle').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
</script>
@endsection
