<x-app-layout>
    <div class="p-8 bg-[#f8fafc] min-h-screen font-sans" x-data="clientManager()">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Clientes</h1>
                <p class="text-gray-500 text-sm" x-text="clientes.length + ' clientes registrados'"></p>
            </div>
            <button @click="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-blue-100 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo Cliente
            </button>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-8">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" x-model="search"
                    class="block w-full pl-12 pr-4 py-3 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-gray-400 text-sm"
                    placeholder="Buscar por nombre, teléfono o email...">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="cliente in filteredClients" :key="cliente.id">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative group">
                    <div class="flex items-start gap-4">
                        <div class="bg-blue-50 p-3 rounded-2xl text-blue-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>

                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-lg" x-text="cliente.nombre"></h3>
                                    <p class="text-[11px] text-gray-400" x-text="'Desde ' + cliente.fecha"></p>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="editClient(cliente)" class="text-blue-500 hover:bg-blue-50 p-1.5 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="deleteClient(cliente.id)" class="text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4 space-y-2">
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span x-text="cliente.telefono"></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 012-2V7a2 2 0 01-2-2H5a2 2 0 01-2 2v10a2 2 0 012 2z"/></svg>
                                    <span x-text="cliente.email"></span>
                                </div>
                                <div class="flex items-start gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span x-text="cliente.direccion"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="showModal = false">

                    <div class="flex justify-between items-center px-8 py-6 border-b border-gray-50">
                        <h3 class="text-xl font-bold text-gray-800" x-text="isEdit ? 'Editar Cliente' : 'Nuevo Cliente'"></h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre Completo *</label>
                            <input type="text" x-model="editingClient.nombre"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Teléfono *</label>
                            <input type="text" x-model="editingClient.telefono"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                            <input type="email" x-model="editingClient.email"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Dirección</label>
                            <textarea x-model="editingClient.direccion" rows="3"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none"></textarea>
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-gray-50/50 flex justify-end items-center gap-4">
                        <button @click="showModal = false" class="text-gray-500 font-semibold hover:text-gray-700 transition-colors">Cancelar</button>
                        <button @click="saveClient()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-blue-100 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clientManager() {
            return {
                search: '',
                showModal: false,
                isEdit: false,
                editingClient: {},
                clientes: [
                    { id: 1, nombre: 'hols', fecha: '25/2/2026', telefono: 'sss', email: 'ss', direccion: 'ss' }
                ],

                get filteredClients() {
                    if (this.search === '') return this.clientes;
                    return this.clientes.filter(c =>
                        c.nombre.toLowerCase().includes(this.search.toLowerCase()) ||
                        c.telefono.includes(this.search) ||
                        c.email.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.editingClient = { id: null, nombre: '', telefono: '', email: '', direccion: '', fecha: new Date().toLocaleDateString() };
                    this.showModal = true;
                },

                editClient(cliente) {
                    this.isEdit = true;
                    this.editingClient = { ...cliente };
                    this.showModal = true;
                },

                saveClient() {
                    if (!this.editingClient.nombre || !this.editingClient.telefono) {
                        alert('Nombre y Teléfono son requeridos');
                        return;
                    }

                    if (this.isEdit) {
                        const index = this.clientes.findIndex(c => c.id === this.editingClient.id);
                        this.clientes[index] = this.editingClient;
                    } else {
                        this.editingClient.id = Date.now();
                        this.clientes.unshift(this.editingClient);
                    }
                    this.showModal = false;
                },

                deleteClient(id) {
                    if (confirm('¿Eliminar este cliente?')) {
                        this.clientes = this.clientes.filter(c => c.id !== id);
                    }
                }
            }
        }
    </script>
</x-app-layout>
