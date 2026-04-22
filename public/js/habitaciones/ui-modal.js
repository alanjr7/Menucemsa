/**
 * HabitacionModal - Módulo para modales
 */

const HabitacionModal = (function() {
    'use strict';

    function crearModalHTML(titulo, contenido, onSubmit) {
        const modal = document.createElement('div');
        modal.id = 'modal-habitacion';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
                <div class="bg-indigo-600 text-white p-4 rounded-t-xl">
                    <h3 class="text-lg font-bold">${titulo}</h3>
                </div>
                <form id="form-modal" class="p-6">
                    ${contenido}
                    <div class="flex gap-3 mt-6">
                        <button type="button" id="btn-cancelar"
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        `;

        document.body.appendChild(modal);

        // Eventos
        document.getElementById('btn-cancelar').addEventListener('click', () => cerrarModal(modal));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) cerrarModal(modal);
        });

        if (onSubmit) {
            document.getElementById('form-modal').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                console.log('FormData hospitalizacion_id:', formData.get('hospitalizacion_id'));
                onSubmit(formData);
                cerrarModal(modal);
            });
        }

        return modal;
    }

    function cerrarModal(modal) {
        modal.remove();
    }

    function generarOptionsPacientes(pacientes) {
        return pacientes.map(p => `
            <option value="${p.id}">${p.paciente?.nombre || 'N/A'} (CI: ${p.paciente?.ci || 'N/A'})</option>
        `).join('');
    }

    return {
        asignarPaciente(camaId, pacientes, onConfirmar) {
            console.log('Pacientes recibidos:', pacientes);
            if (!pacientes || pacientes.length === 0) {
                HabitacionNotificaciones.warning('No hay pacientes pendientes por asignar');
                return null;
            }

            const contenido = `
                <input type="hidden" name="cama_id" value="${camaId}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Paciente</label>
                    <select name="hospitalizacion_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione un paciente...</option>
                        ${generarOptionsPacientes(pacientes)}
                    </select>
                </div>
            `;

            return crearModalHTML('Asignar Paciente a Cama', contenido, onConfirmar);
        },

        confirmar(mensaje, onConfirmar) {
            return new Promise((resolve) => {
                const modal = document.createElement('div');
                modal.id = 'modal-confirmar';
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
                modal.innerHTML = `
                    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Confirmar</h3>
                            <p class="text-sm text-gray-600 mt-2">${mensaje}</p>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" id="btn-no" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Cancelar
                            </button>
                            <button type="button" id="btn-si" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Confirmar
                            </button>
                        </div>
                    </div>
                `;

                document.body.appendChild(modal);

                document.getElementById('btn-no').addEventListener('click', () => {
                    modal.remove();
                    resolve(false);
                });

                document.getElementById('btn-si').addEventListener('click', () => {
                    modal.remove();
                    resolve(true);
                });

                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.remove();
                        resolve(false);
                    }
                });
            });
        },

        cerrar() {
            const modal = document.getElementById('modal-habitacion');
            if (modal) modal.remove();
        },
    };
})();
