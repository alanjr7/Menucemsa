/**
 * HabitacionNotificaciones - Módulo para notificaciones toast
 */

const HabitacionNotificaciones = (function() {
    'use strict';

    const CONFIG = {
        duracion: 3000,
        posicion: 'top-4 right-4',
    };

    function crearNotificacion(mensaje, tipo) {
        const colores = {
            success: 'bg-green-100 border-green-400 text-green-700',
            error: 'bg-red-100 border-red-400 text-red-700',
            warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
            info: 'bg-blue-100 border-blue-400 text-blue-700',
        };

        const notif = document.createElement('div');
        notif.className = `fixed ${CONFIG.posicion} px-6 py-3 rounded-lg shadow-lg z-50 border ${colores[tipo] || colores.info}`;
        notif.textContent = mensaje;

        document.body.appendChild(notif);

        setTimeout(() => {
            notif.remove();
        }, CONFIG.duracion);
    }

    return {
        success(mensaje) {
            crearNotificacion(mensaje, 'success');
        },

        error(mensaje) {
            crearNotificacion(mensaje, 'error');
        },

        warning(mensaje) {
            crearNotificacion(mensaje, 'warning');
        },

        info(mensaje) {
            crearNotificacion(mensaje, 'info');
        },
    };
})();
