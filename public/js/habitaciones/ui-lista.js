/**
 * HabitacionListaUI - Solo gestión visual de selección
 * (La lista se renderiza server-side en Blade)
 */

const HabitacionListaUI = (function() {
    'use strict';

    return {
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
