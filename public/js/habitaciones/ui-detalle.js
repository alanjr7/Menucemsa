/**
 * HabitacionDetalleUI - Renderizado simplificado de detalle de habitación
 * Solo muestra camas con botones y acciones básicas
 */

const HabitacionDetalleUI = (function() {
    'use strict';

    const ESTADO_COLORES = {
        disponible: 'green',
        ocupada: 'yellow',
        mantenimiento: 'red',
    };

    function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function generarCamaHTML(cama, habitacionId) {
        if (cama.disponibilidad === 'ocupada' && cama.hospitalizacion_activa) {
            return generarCamaOcupada(cama);
        }
        if (cama.disponibilidad === 'disponible') {
            return generarCamaDisponible(cama, habitacionId);
        }
        return generarCamaMantenimiento(cama);
    }

    function generarCamaOcupada(cama) {
        const paciente = cama.hospitalizacion_activa?.paciente;
        return `
            <div class="border-2 border-red-200 bg-red-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-semibold text-gray-900">Cama ${cama.nro}</span>
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Ocupada</span>
                </div>
                <div class="bg-white rounded p-3 mb-3">
                    <p class="text-sm font-medium text-gray-900">${paciente?.nombre || 'N/A'}</p>
                    <p class="text-xs text-gray-500">CI: ${paciente?.ci || 'N/A'}</p>
                </div>
                <button onclick="window.habitacionApp.liberarCama('${cama.id}')"
                        class="w-full px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                    Liberar Cama
                </button>
            </div>
        `;
    }

    function generarCamaDisponible(cama, habitacionId) {
        return `
            <div class="border-2 border-green-200 bg-green-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-semibold text-gray-900">Cama ${cama.nro}</span>
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Disponible</span>
                </div>
                <button onclick="window.habitacionApp.mostrarModalAsignar(${cama.id}, '${habitacionId}')"
                        class="w-full px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition">
                    Asignar Paciente
                </button>
            </div>
        `;
    }

    function generarCamaMantenimiento(cama) {
        return `
            <div class="border-2 border-gray-200 bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-gray-900">Cama ${cama.nro}</span>
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Mantenimiento</span>
                </div>
            </div>
        `;
    }

    function generarAccionesHTML(habitacion) {
        const isMantenimiento = habitacion.estado === 'mantenimiento';
        return `
            <div class="flex gap-3 mt-6 pt-6 border-t">
                <a href="/internacion-staff/habitaciones/${habitacion.id}/edit"
                   class="flex-1 flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                    Editar Habitación
                </a>
                <button onclick="window.habitacionApp.toggleMantenimiento('${habitacion.id}')"
                        class="flex-1 flex items-center justify-center px-4 py-2 ${isMantenimiento ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'} text-white rounded-lg transition text-sm">
                    ${isMantenimiento ? 'Activar' : 'Mantenimiento'}
                </button>
            </div>
        `;
    }

    return {
        render(container, habitacion, pacientesSinHabitacion = []) {
            const color = ESTADO_COLORES[habitacion.estado] || 'gray';
            const camasHtml = habitacion.camas.map(c => generarCamaHTML(c, habitacion.id)).join('');

            container.innerHTML = `
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4 pb-4 border-b">
                        <h2 class="text-xl font-bold text-gray-900">Habitación ${habitacion.id}</h2>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-${color}-100 text-${color}-800">
                            ${capitalize(habitacion.estado)}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${camasHtml}
                    </div>

                    ${generarAccionesHTML(habitacion)}
                </div>
            `;

            container.dataset.habitacionId = habitacion.id;
            container.dataset.pacientes = JSON.stringify(pacientesSinHabitacion);
            container.dataset.estado = habitacion.estado;
        },

        renderLoading(container) {
            container.innerHTML = `
                <div class="p-6">
                    <div class="animate-pulse">
                        <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="h-24 bg-gray-200 rounded"></div>
                            <div class="h-24 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>
            `;
        },

        renderEmpty(container) {
            container.innerHTML = `
                <div class="h-full min-h-[400px] flex items-center justify-center bg-slate-50">
                    <div class="text-center p-8">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-700 mb-2">Selecciona una habitación</h3>
                        <p class="text-sm text-slate-500 max-w-xs">Haz clic en una habitación de la lista para ver sus detalles.</p>
                    </div>
                </div>
            `;
        },

        getPacientesData(container) {
            try {
                return JSON.parse(container.dataset.pacientes || '[]');
            } catch {
                return [];
            }
        },
    };
})();
