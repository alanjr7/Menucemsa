/**
 * HabitacionApi - Módulo para llamadas HTTP
 * Encapsula todas las peticiones a la API de habitaciones
 */

const HabitacionApi = (function() {
    'use strict';

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const BASE_URL = '/internacion-staff';

    async function request(url, options = {}) {
        const config = {
            method: options.method || 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
                ...options.headers,
            },
            ...(options.signal ? { signal: options.signal } : {}),
        };

        if (options.body && !(options.body instanceof FormData)) {
            config.body = JSON.stringify(options.body);
        } else if (options.body) {
            config.body = options.body;
        }

        const response = await fetch(url, config);

        if (!response.ok) {
            const error = await response.text();
            throw new Error(error || `HTTP ${response.status}`);
        }

        return response.json();
    }

    function getCsrfHeaders() {
        return { 'X-CSRF-TOKEN': CSRF_TOKEN };
    }

    return {
        async detalle(habitacionId, signal) {
            return request(`${BASE_URL}/api/habitaciones/${habitacionId}`, { signal });
        },

        async pacientesSinHabitacion(signal) {
            return request(`${BASE_URL}/api/pacientes-sin-habitacion`, { signal });
        },

        async asignarPaciente(habitacionId, formData) {
            formData.append('_token', CSRF_TOKEN);

            return request(`${BASE_URL}/habitaciones/${habitacionId}/asignar-paciente`, {
                method: 'POST',
                body: formData,
            });
        },

        async liberarCama(camaId) {
            const formData = new FormData();
            formData.append('_token', CSRF_TOKEN);

            return request(`${BASE_URL}/camas/${camaId}/liberar`, {
                method: 'POST',
                body: formData,
            });
        },

        async toggleMantenimiento(habitacionId) {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', CSRF_TOKEN);

            return request(`${BASE_URL}/habitaciones/${habitacionId}`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData,
            });
        },
    };
})();
