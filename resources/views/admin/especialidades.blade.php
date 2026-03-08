<x-app-layout>
    <div class="p-6 bg-gray-50/50 min-h-screen">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Especialidades</h1>
                <p class="text-sm text-gray-500">Administrar especialidades médicas del sistema</p>
            </div>
            <div class="flex gap-3">
                <button onclick="abrirModalNuevaEspecialidad()" class="flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Especialidad
                </button>
                <button onclick="cargarEspecialidades()" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualizar
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">Total Especialidades</span>
                <span class="text-3xl font-bold text-gray-800" id="stat-total">0</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">Con Médicos</span>
                <span class="text-3xl font-bold text-green-600" id="stat-con-medicos">0</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">Sin Médicos</span>
                <span class="text-3xl font-bold text-orange-500" id="stat-sin-medicos">0</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">Más Usadas</span>
                <div class="text-sm text-blue-600 font-medium" id="stat-mas-usadas">-</div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="busqueda" placeholder="Buscar especialidad..." 
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                           oninput="filtrarEspecialidades()">
                </div>
                <select id="filtro" onchange="filtrarEspecialidades()" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">Todas</option>
                    <option value="con-medicos">Con Médicos</option>
                    <option value="sin-medicos">Sin Médicos</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Médicos</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Consultas</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-especialidades" class="bg-white divide-y divide-gray-200">
                        <!-- Las especialidades se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
            
            <div id="no-resultados" class="hidden p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>No se encontraron especialidades</p>
            </div>
        </div>

    </div>

    <!-- Modal Nueva Especialidad -->
    <div id="modalNuevaEspecialidad" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">Nueva Especialidad</h3>
                        <p class="text-blue-100 text-sm mt-1">Agregar nueva especialidad médica</p>
                    </div>
                    <button onclick="cerrarModalNuevaEspecialidad()" class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="formNuevaEspecialidad" onsubmit="guardarNuevaEspecialidad(); return false;" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                        <input type="text" name="codigo" placeholder="Ej: CARDIO" maxlength="15" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                        <input type="text" name="nombre" placeholder="Ej: Cardiología" maxlength="80" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                        <textarea name="descripcion" placeholder="Descripción de la especialidad" maxlength="80" rows="3" required
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="cerrarModalNuevaEspecialidad()" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center text-sm shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Especialidad -->
    <div id="modalEditarEspecialidad" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">Editar Especialidad</h3>
                        <p class="text-green-100 text-sm mt-1">Modificar especialidad existente</p>
                    </div>
                    <button onclick="cerrarModalEditarEspecialidad()" class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="formEditarEspecialidad" onsubmit="actualizarEspecialidad(); return false;" class="p-6">
                <input type="hidden" name="codigo">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                        <input type="text" name="codigo_display" readonly
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-100 text-gray-600">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                        <input type="text" name="nombre" placeholder="Ej: Cardiología" maxlength="80" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                        <textarea name="descripcion" placeholder="Descripción de la especialidad" maxlength="80" rows="3" required
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="cerrarModalEditarEspecialidad()" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors flex items-center text-sm shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        let especialidadesData = [];

        // Cargar datos al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarEspecialidades();
            cargarStats();
        });

        // Función para cargar especialidades
        async function cargarEspecialidades() {
            try {
                const response = await fetch('/api/especialidades');
                const especialidades = await response.json();
                
                especialidadesData = especialidades;
                mostrarEspecialidades(especialidades);
            } catch (error) {
                console.error('Error al cargar especialidades:', error);
                mostrarError('Error al cargar especialidades');
            }
        }

        // Función para cargar estadísticas
        async function cargarStats() {
            try {
                const response = await fetch('/api/admin/especialidades/stats');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('stat-total').textContent = data.stats.total;
                    document.getElementById('stat-con-medicos').textContent = data.stats.con_medicos;
                    document.getElementById('stat-sin-medicos').textContent = data.stats.sin_medicos;
                    
                    const masUsadas = data.stats.mas_usadas.slice(0, 3).map(e => e.nombre).join(', ');
                    document.getElementById('stat-mas-usadas').textContent = masUsadas || '-';
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        // Función para mostrar especialidades en la tabla
        function mostrarEspecialidades(especialidades) {
            const tbody = document.getElementById('tabla-especialidades');
            const noResultados = document.getElementById('no-resultados');
            
            if (especialidades.length === 0) {
                tbody.innerHTML = '';
                noResultados.classList.remove('hidden');
                return;
            }
            
            noResultados.classList.add('hidden');
            
            tbody.innerHTML = especialidades.map(esp => `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                            ${esp.codigo}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${esp.nombre}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500 max-w-xs truncate">${esp.descripcion}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${esp.medicos_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                            ${esp.medicos_count || 0}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-sm text-gray-900">${esp.consultas_count || 0}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${esp.medicos_count > 0 ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'}">
                            ${esp.medicos_count > 0 ? 'Activa' : 'Sin Médicos'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex justify-center space-x-2">
                            <button onclick="editarEspecialidad('${esp.codigo}')" class="text-green-600 hover:text-green-900 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="eliminarEspecialidad('${esp.codigo}')" class="text-red-600 hover:text-red-900 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Función para filtrar especialidades
        function filtrarEspecialidades() {
            const busqueda = document.getElementById('busqueda').value.toLowerCase();
            const filtro = document.getElementById('filtro').value;
            
            let filtradas = especialidadesData;
            
            // Filtrar por búsqueda
            if (busqueda) {
                filtradas = filtradas.filter(esp => 
                    esp.codigo.toLowerCase().includes(busqueda) ||
                    esp.nombre.toLowerCase().includes(busqueda) ||
                    esp.descripcion.toLowerCase().includes(busqueda)
                );
            }
            
            // Filtrar por categoría
            if (filtro === 'con-medicos') {
                filtradas = filtradas.filter(esp => esp.medicos_count > 0);
            } else if (filtro === 'sin-medicos') {
                filtradas = filtradas.filter(esp => esp.medicos_count === 0);
            }
            
            mostrarEspecialidades(filtradas);
        }

        // Funciones para modales
        function abrirModalNuevaEspecialidad() {
            document.getElementById('modalNuevaEspecialidad').classList.remove('hidden');
            document.getElementById('formNuevaEspecialidad').reset();
        }

        function cerrarModalNuevaEspecialidad() {
            document.getElementById('modalNuevaEspecialidad').classList.add('hidden');
        }

        function abrirModalEditarEspecialidad() {
            document.getElementById('modalEditarEspecialidad').classList.remove('hidden');
        }

        function cerrarModalEditarEspecialidad() {
            document.getElementById('modalEditarEspecialidad').classList.add('hidden');
        }

        // Función para guardar nueva especialidad
        async function guardarNuevaEspecialidad() {
            event.preventDefault();
            
            const form = document.getElementById('formNuevaEspecialidad');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/admin/especialidades', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Especialidad creada exitosamente');
                    cerrarModalNuevaEspecialidad();
                    cargarEspecialidades();
                    cargarStats();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar especialidad');
            }
        }

        // Función para editar especialidad
        async function editarEspecialidad(codigo) {
            try {
                const response = await fetch(`/api/admin/especialidades/${codigo}`);
                const result = await response.json();
                
                if (result.success) {
                    const esp = result.especialidad;
                    const form = document.getElementById('formEditarEspecialidad');
                    
                    form.codigo.value = esp.codigo;
                    form.codigo_display.value = esp.codigo;
                    form.nombre.value = esp.nombre;
                    form.descripcion.value = esp.descripcion;
                    
                    abrirModalEditarEspecialidad();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar especialidad');
            }
        }

        // Función para actualizar especialidad
        async function actualizarEspecialidad() {
            event.preventDefault();
            
            const form = document.getElementById('formEditarEspecialidad');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const codigo = data.codigo;
            
            try {
                const response = await fetch(`/api/admin/especialidades/${codigo}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        nombre: data.nombre,
                        descripcion: data.descripcion
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Especialidad actualizada exitosamente');
                    cerrarModalEditarEspecialidad();
                    cargarEspecialidades();
                    cargarStats();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al actualizar especialidad');
            }
        }

        // Función para eliminar especialidad
        async function eliminarEspecialidad(codigo) {
            if (!confirm('¿Está seguro de eliminar esta especialidad? Esta acción no se puede deshacer.')) {
                return;
            }
            
            try {
                const response = await fetch(`/api/admin/especialidades/${codigo}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Especialidad eliminada exitosamente');
                    cargarEspecialidades();
                    cargarStats();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar especialidad');
            }
        }

        function mostrarError(mensaje) {
            const tbody = document.getElementById('tabla-especialidades');
            const noResultados = document.getElementById('no-resultados');
            
            tbody.innerHTML = '';
            noResultados.innerHTML = `
                <div class="p-8 text-center text-red-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p>${mensaje}</p>
                </div>
            `;
            noResultados.classList.remove('hidden');
        }
    </script>
</x-app-layout>
