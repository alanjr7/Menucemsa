/**
 * HabitacionApp - Aplicación principal para gestión de habitaciones
 * Coordina API, Cache y UI
 */

const HabitacionApp = (function() {
    'use strict';

    const elements = {
        lista: null,
        detalle: null,
        filtros: null,
    };

    let habitacionActual = null;
    let habitacionesData = [];
    let filtroActivo = 'todas';
    let abortController = null;

    async function init() {
        cachearElementos();
        bindEvents();

        const hash = window.location.hash.replace('#', '');
        if (hash && hash.startsWith('h')) {
            await Promise.all([cargarLista(), seleccionarHabitacion(hash)]);
        } else {
            await cargarLista(true);
        }
    }

    function cachearElementos() {
        elements.lista = document.getElementById('habitaciones-lista');
        elements.detalle = document.getElementById('habitacion-detalle');
        elements.filtros = document.querySelectorAll('[data-filtro]');
    }

    function bindEvents() {
        elements.filtros.forEach(btn => {
            btn.addEventListener('click', (e) => {
                filtroActivo = e.currentTarget.dataset.filtro;
                aplicarFiltroUI(e.currentTarget);
                renderizarListaFiltrada();
            });
        });

        window.addEventListener('hashchange', () => {
            const hash = window.location.hash.replace('#', '');
            if (hash && hash.startsWith('h')) {
                seleccionarHabitacion(hash);
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

    async function cargarLista(seleccionarPrimera = false) {
        const cacheKey = 'habitaciones:lista';

        if (HabitacionCache.has(cacheKey)) {
            habitacionesData = HabitacionCache.get(cacheKey);
            renderizarListaFiltrada();
            if (seleccionarPrimera && !window.location.hash && habitacionesData.length > 0) {
                await seleccionarHabitacion(habitacionesData[0].id);
            }
            return;
        }

        try {
            const response = await HabitacionApi.listar();
            if (response.success) {
                habitacionesData = response.habitaciones;
                HabitacionCache.set(cacheKey, habitacionesData);
                renderizarListaFiltrada();

                if (seleccionarPrimera && !window.location.hash && habitacionesData.length > 0) {
                    await seleccionarHabitacion(habitacionesData[0].id);
                }
            }
        } catch (error) {
            console.error('Error cargando habitaciones:', error);
            HabitacionNotificaciones.error('Error al cargar la lista de habitaciones');
        }
    }

    function renderizarListaFiltrada() {
        const filtradas = HabitacionListaUI.aplicarFiltro(habitacionesData, filtroActivo);
        HabitacionListaUI.render(elements.lista, filtradas, habitacionActual);
    }

    async function seleccionarHabitacion(habitacionId) {
        habitacionActual = habitacionId;
        HabitacionListaUI.actualizarSeleccionVisual(elements.lista, habitacionId);
        window.location.hash = habitacionId;

        // Cancelar petición anterior si existe
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        HabitacionDetalleUI.renderLoading(elements.detalle);
        await cargarDetalle(habitacionId, abortController.signal);
    }

    async function cargarDetalle(habitacionId, signal) {
        const cacheKey = HabitacionCache.makeKey('habitacion', habitacionId);

        if (HabitacionCache.has(cacheKey)) {
            const cached = HabitacionCache.get(cacheKey);
            // Solo renderizar si seguimos en la misma habitación
            if (habitacionActual === habitacionId) {
                HabitacionDetalleUI.render(elements.detalle, cached.habitacion, cached.pacientes);
            }
            actualizarEnSegundoPlano(habitacionId, cacheKey, signal);
            return;
        }

        try {
            const [habitacionResponse, pacientesResponse] = await Promise.all([
                HabitacionApi.detalle(habitacionId, signal),
                HabitacionApi.pacientesSinHabitacion(signal),
            ]);

            // Verificar que seguimos en la misma habitación antes de renderizar
            if (habitacionActual !== habitacionId) {
                return;
            }

            if (habitacionResponse.success) {
                const data = {
                    habitacion: habitacionResponse.habitacion,
                    pacientes: pacientesResponse.success ? pacientesResponse.pacientes : [],
                };
                HabitacionCache.set(cacheKey, data, HabitacionCache.DETAIL_TTL);
                HabitacionDetalleUI.render(elements.detalle, data.habitacion, data.pacientes);
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return; // Petición cancelada, no es un error real
            }
            console.error('Error cargando detalle:', error);
            HabitacionNotificaciones.error('Error al cargar el detalle de la habitación');
        }
    }

    function actualizarEnSegundoPlano(habitacionId, cacheKey, signal) {
        Promise.all([
            HabitacionApi.detalle(habitacionId, signal),
            HabitacionApi.pacientesSinHabitacion(signal),
        ]).then(([habitacionResponse, pacientesResponse]) => {
            // Solo actualizar si seguimos en la misma habitación
            if (habitacionActual !== habitacionId) {
                return;
            }
            if (habitacionResponse.success) {
                const data = {
                    habitacion: habitacionResponse.habitacion,
                    pacientes: pacientesResponse.success ? pacientesResponse.pacientes : [],
                };
                HabitacionCache.set(cacheKey, data, HabitacionCache.DETAIL_TTL);
                HabitacionDetalleUI.render(elements.detalle, data.habitacion, data.pacientes);
            }
        }).catch((error) => {
            if (error.name === 'AbortError') {
                return; // Petición cancelada, ignorar
            }
        });
    }

    async function recargarDatos() {
        HabitacionCache.invalidate('habitaciones:lista');
        if (habitacionActual) {
            HabitacionCache.invalidate(HabitacionCache.makeKey('habitacion', habitacionActual));
        }
        await Promise.all([cargarLista(), cargarDetalle(habitacionActual)]);
    }

    async function liberarCama(camaId) {
        const confirmar = await HabitacionModal.confirmar('¿Está seguro de liberar esta cama?');
        if (!confirmar) return;

        try {
            const response = await HabitacionApi.liberarCama(camaId);
            if (response.success) {
                HabitacionNotificaciones.success(response.message);
                await recargarDatos();
            } else {
                HabitacionNotificaciones.error(response.error || 'Error al liberar cama');
            }
        } catch (error) {
            console.error('Error:', error);
            HabitacionNotificaciones.error('Error de conexión');
        }
    }

    function mostrarModalAsignar(camaId, habitacionId) {
        const pacientes = HabitacionDetalleUI.getPacientesData(elements.detalle);
        HabitacionModal.asignarPaciente(camaId, pacientes, async (formData) => {
            try {
                const response = await HabitacionApi.asignarPaciente(habitacionId, formData);
                if (response.success) {
                    HabitacionNotificaciones.success(response.message);
                    await recargarDatos();
                } else {
                    HabitacionNotificaciones.error(response.error || 'Error al asignar paciente');
                }
            } catch (error) {
                console.error('Error:', error);
                HabitacionNotificaciones.error('Error de conexión');
            }
        });
    }

    async function toggleMantenimiento(habitacionId) {
        const estadoActual = elements.detalle.dataset.estado;
        const mensaje = estadoActual === 'mantenimiento'
            ? '¿Activar esta habitación?'
            : '¿Marcar habitación en mantenimiento?';

        const confirmar = await HabitacionModal.confirmar(mensaje);
        if (!confirmar) return;

        try {
            const response = await HabitacionApi.toggleMantenimiento(habitacionId);
            if (response.success) {
                HabitacionNotificaciones.success(response.message || 'Estado actualizado');
                HabitacionCache.invalidateAll();
                await recargarDatos();
            } else {
                HabitacionNotificaciones.error(response.error || 'Error al cambiar estado');
            }
        } catch (error) {
            console.error('Error:', error);
            HabitacionNotificaciones.error('Error de conexión');
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

// Exponer globalmente para los eventos onclick
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('habitaciones-lista')) {
        window.habitacionApp = HabitacionApp;
        HabitacionApp.init();
    }
});
