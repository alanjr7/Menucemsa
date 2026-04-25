/**
 * HabitacionApp - Ultra simple: todo renderizado server-side
 * Solo maneja filtros y show/hide de paneles pre-renderizados
 */

const HabitacionApp = (function() {
    'use strict';

    const elements = {
        lista: null,
        detalleContainer: null,
        detalleVacio: null,
        filtros: null,
    };

    let habitacionActual = null;
    let filtroActivo = 'todas';

    function init() {
        cachearElementos();
        bindEvents();

        const hash = decodeURIComponent(window.location.hash.replace('#', ''));
        if (hash) {
            seleccionarHabitacion(hash);
        }
    }

    function cachearElementos() {
        elements.lista = document.getElementById('habitaciones-lista');
        elements.detalleContainer = document.getElementById('habitacion-detalle-container');
        elements.detalleVacio = document.getElementById('detalle-vacio');
        elements.filtros = document.querySelectorAll('[data-filtro]');
    }

    function bindEvents() {
        elements.filtros.forEach(btn => {
            btn.addEventListener('click', (e) => {
                filtroActivo = e.currentTarget.dataset.filtro;
                aplicarFiltroUI(e.currentTarget);
                aplicarFiltroDOM();
            });
        });

        window.addEventListener('hashchange', () => {
            const hash = decodeURIComponent(window.location.hash.replace('#', ''));
            if (hash && hash !== habitacionActual) {
                seleccionarHabitacion(hash, true); // true = no actualizar hash
            }
        });
    }

    function aplicarFiltroUI(activo) {
        elements.filtros.forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        activo.classList.remove('bg-gray-200', 'text-gray-700');
        activo.classList.add('bg-indigo-600', 'text-white');
    }

    function aplicarFiltroDOM() {
        const items = elements.lista.querySelectorAll('.habitacion-item');
        items.forEach(item => {
            const estado = item.dataset.estado;
            item.style.display = (filtroActivo === 'todas' || estado === filtroActivo) ? '' : 'none';
        });
    }

    function seleccionarHabitacion(habitacionId, skipHashUpdate = false) {
        habitacionActual = habitacionId;
        actualizarSeleccionVisual(habitacionId);

        // Solo actualizar hash si no viene del evento hashchange
        if (!skipHashUpdate) {
            const newHash = encodeURIComponent(habitacionId);
            if (window.location.hash !== '#' + newHash) {
                window.location.hash = newHash;
            }
        }

        // Ocultar estado vacío y todos los paneles
        elements.detalleVacio.classList.add('hidden');
        elements.detalleContainer.querySelectorAll('.habitacion-detalle-panel').forEach(panel => {
            panel.classList.add('hidden');
        });

        // Mostrar panel seleccionado
        const panel = document.getElementById('detalle-' + habitacionId);
        if (panel) {
            panel.classList.remove('hidden');
        }
    }

    function actualizarSeleccionVisual(habitacionId) {
        elements.lista.querySelectorAll('.habitacion-item').forEach(el => {
            if (el.dataset.id === habitacionId) {
                el.classList.add('bg-blue-50', 'border-l-blue-500');
                el.classList.remove('border-l-transparent');
            } else {
                el.classList.remove('bg-blue-50', 'border-l-blue-500');
                el.classList.add('border-l-transparent');
            }
        });
    }

    async function liberarCama(camaId) {
        if (!confirm('¿Está seguro de liberar esta cama?')) return;

        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

            const response = await fetch('/internacion-staff/camas/' + camaId + '/liberar', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            }).then(r => r.json());

            if (response.success) {
                window.location.reload();
            } else {
                alert(response.error || 'Error al liberar cama');
            }
        } catch (error) {
            alert('Error de conexión');
        }
    }

    function mostrarModalAsignar(camaId, habitacionId) {
        const container = elements.detalleContainer;
        const pacientes = JSON.parse(container.dataset.pacientes || '[]');
        HabitacionModal.asignarPaciente(camaId, pacientes, async (formData) => {
            try {
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

                const response = await fetch('/internacion-staff/habitaciones/' + habitacionId + '/asignar-paciente', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                }).then(r => r.json());

                if (response.success) {
                    window.location.reload();
                } else {
                    alert(response.error || 'Error al asignar paciente');
                }
            } catch (error) {
                alert('Error de conexión');
            }
        });
    }

    async function toggleMantenimiento(habitacionId) {
        const panel = document.getElementById('detalle-' + habitacionId);
        const estadoActual = panel?.dataset.estado;
        const mensaje = estadoActual === 'mantenimiento'
            ? '¿Activar esta habitación?'
            : '¿Marcar habitación en mantenimiento?';

        if (!confirm(mensaje)) return;

        try {
            const response = await fetch('/internacion-staff/habitaciones/' + habitacionId, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            }).then(r => r.json());

            if (response.success) {
                window.location.reload();
            } else {
                alert(response.error || 'Error al cambiar estado');
            }
        } catch (error) {
            alert('Error de conexión');
        }
    }

    return {
        init,
        seleccionarHabitacion,
        liberarCama,
        mostrarModalAsignar,
        toggleMantenimiento,
    };
})();

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('habitaciones-lista')) {
        window.habitacionApp = HabitacionApp;
        HabitacionApp.init();
    }
});
