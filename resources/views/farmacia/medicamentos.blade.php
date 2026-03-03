<x-app-layout>
    <div class="p-8 bg-[#f8fafc] min-h-screen font-sans" x-data="medicamentosManager()">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Medicamentos</h1>
                <p class="text-gray-500 text-sm" x-text="medicamentos.length + ' medicamentos registrados'"></p>
            </div>
            <button @click="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-blue-100 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo Medicamento
            </button>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" x-model="search"
                    class="block w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-gray-400 text-sm"
                    placeholder="Buscar medicamento o código...">
            </div>
            <div x-show="loading" class="flex items-center px-4">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                <span class="ml-2 text-sm text-gray-600">Cargando...</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Precio</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="medicamento in filteredMedicamentos" :key="medicamento.CODIGO">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded" x-text="medicamento.CODIGO"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800" x-text="medicamento.DESCRIPCION"></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-green-600" x-text="'$' + medicamento.PRECIO.toFixed(2)"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="editMedicamento(medicamento)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="deleteMedicamento(medicamento)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            
            <div x-show="medicamentos.length === 0 && !loading" class="text-center py-12">
                <p class="text-gray-500 font-medium">No hay medicamentos registrados</p>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="showEditModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showEditModal = false">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800" x-text="isEdit ? 'Editar Medicamento' : 'Nuevo Medicamento'"></h3>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Código *</label>
                            <input type="text" x-model="editingMedicamento.CODIGO" :disabled="isEdit" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500" :class="isEdit ? 'bg-gray-100' : ''">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Precio *</label>
                            <input type="number" step="0.01" x-model="editingMedicamento.PRECIO" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción *</label>
                        <input type="text" x-model="editingMedicamento.DESCRIPCION" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="px-8 py-6 bg-gray-50 flex justify-end gap-3">
                    <button @click="showEditModal = false" class="px-6 py-2.5 text-gray-600 font-semibold hover:bg-gray-100 rounded-xl transition-all">Cancelar</button>
                    <button @click="saveMedicamento()" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">
                        <span x-text="isEdit ? 'Guardar Cambios' : 'Crear Medicamento'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
function medicamentosManager() {
    return {
        search: '',
        showEditModal: false,
        isEdit: false,
        editingMedicamento: {},
        medicamentos: [],
        loading: false,
        
        async init() {
            await this.loadMedicamentos();
        },
        
        async loadMedicamentos() {
            this.loading = true;
            try {
                const response = await fetch('/farmacia/api/medicamentos');
                if (response.ok) {
                    this.medicamentos = await response.json();
                } else {
                    this.showNotification('Error cargando medicamentos', 'error');
                }
            } catch (error) {
                console.error('Error cargando medicamentos:', error);
                this.showNotification('Error cargando medicamentos', 'error');
            } finally {
                this.loading = false;
            }
        },

        get filteredMedicamentos() {
            return this.medicamentos.filter(m => 
                m.DESCRIPCION.toLowerCase().includes(this.search.toLowerCase()) ||
                m.CODIGO.includes(this.search)
            );
        },

        openCreateModal() {
            this.isEdit = false;
            this.editingMedicamento = {
                CODIGO: '',
                DESCRIPCION: '',
                PRECIO: 0
            };
            this.showEditModal = true;
        },

        editMedicamento(medicamento) {
            this.isEdit = true;
            this.editingMedicamento = JSON.parse(JSON.stringify(medicamento));
            this.showEditModal = true;
        },

        async saveMedicamento() {
            if (!this.editingMedicamento.CODIGO || !this.editingMedicamento.DESCRIPCION) {
                this.showNotification('Código y descripción son obligatorios', 'error');
                return;
            }

            try {
                const method = this.isEdit ? 'PUT' : 'POST';
                const url = this.isEdit 
                    ? `/farmacia/api/medicamentos/${this.editingMedicamento.CODIGO}`
                    : '/farmacia/api/medicamentos';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.editingMedicamento)
                });

                if (response.ok) {
                    this.showNotification(this.isEdit ? 'Medicamento actualizado' : 'Medicamento creado', 'success');
                    this.showEditModal = false;
                    await this.loadMedicamentos();
                } else {
                    const error = await response.json();
                    this.showNotification(error.message || 'Error guardando medicamento', 'error');
                }
            } catch (error) {
                console.error('Error guardando medicamento:', error);
                this.showNotification('Error guardando medicamento', 'error');
            }
        },

        async deleteMedicamento(medicamento) {
            if (!confirm(`¿Estás seguro de eliminar ${medicamento.DESCRIPCION}?`)) {
                return;
            }

            try {
                const response = await fetch(`/farmacia/api/medicamentos/${medicamento.CODIGO}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    this.showNotification('Medicamento eliminado', 'success');
                    await this.loadMedicamentos();
                } else {
                    this.showNotification('Error eliminando medicamento', 'error');
                }
            } catch (error) {
                console.error('Error eliminando medicamento:', error);
                this.showNotification('Error eliminando medicamento', 'error');
            }
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    };
}
</script>
</x-app-layout>
