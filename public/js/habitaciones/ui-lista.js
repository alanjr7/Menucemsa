/**
 * HabitacionListaUI - Renderizado de lista de habitaciones
 */

const HabitacionListaUI = (function() {
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

    function generarItemHTML(habitacion, habitacionActual) {
        const libres = habitacion.camas_disponibles || 0;
        const total = habitacion.camas_count || 0;
        const ocupadas = total - libres;
        const color = ESTADO_COLORES[habitacion.estado] || 'gray';
        const isSelected = habitacionActual === habitacion.id;

        return `
            <div class="habitacion-item cursor-pointer border-b border-gray-100 hover:bg-gray-50 transition ${isSelected ? 'bg-blue-50 border-l-4 border-l-blue-500' : 'border-l-4 border-l-transparent'}"
                 onclick="window.habitacionApp.seleccionarHabitacion('${habitacion.id}')"
                 data-id="${habitacion.id}">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-gray-900">Habitación ${habitacion.id}</h4>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-${color}-100 text-${color}-800">
                            ${capitalize(habitacion.estado)}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 truncate">${habitacion.detalle || 'Sin detalle'}</p>
                    <div class="mt-2 flex items-center text-xs text-gray-500 gap-3">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01"/>
                            </svg>
                            ${total} camas
                        </span>
                        ${ocupadas > 0 
                            ? `<span class="text-amber-600 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>${ocupadas} ocupadas</span>` 
                            : `<span class="text-emerald-600 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Disponible</span>`
                        }
                    </div>
                </div>
            </div>
        `;
    }

    function generarEmptyState() {
        return `
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p>No hay habitaciones registradas</p>
            </div>
        `;
    }

    return {
        render(container, habitaciones, habitacionActual = null) {
            if (!habitaciones || habitaciones.length === 0) {
                container.innerHTML = generarEmptyState();
                return;
            }

            container.innerHTML = habitaciones.map(h => generarItemHTML(h, habitacionActual)).join('');
        },

        aplicarFiltro(habitaciones, filtro) {
            if (filtro === 'todas') return habitaciones;
            return habitaciones.filter(h => h.estado === filtro);
        },

        actualizarSeleccionVisual(container, habitacionId) {
            container.querySelectorAll('.habitacion-item').forEach(el => {
                if (el.dataset.id === habitacionId) {
                    el.classList.add('bg-blue-50', 'border-l-blue-500');
                    el.classList.remove('border-l-transparent');
                } else {
                    el.classList.remove('bg-blue-50', 'border-l-blue-500');
                    el.classList.add('border-l-transparent');
                }
            });
        },
    };
})();
