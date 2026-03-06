<x-app-layout>
    <div x-data="tarifariosApp()" class="p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-[26px] font-black text-slate-800 tracking-tight">Tarifarios y Precios</h1>
                <p class="text-slate-500 text-[15px] font-medium mt-1">Gestión de precios por servicio y tipo de seguro</p>
            </div>
            <button @click="showAddModal = true" class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-md transition-all">
                <span class="text-lg">+</span> Nuevo Servicio
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-2">Total Servicios</p>
                <p class="text-slate-800 text-[32px] font-black tracking-tighter" x-text="stats.total"></p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-2">Consultas</p>
                <p class="text-[#1c7ed6] text-[32px] font-black tracking-tighter" x-text="stats.servicios"></p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-2">Procedimientos</p>
                <p class="text-[#0ca678] text-[32px] font-black tracking-tighter" x-text="stats.procedimientos"></p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-2">Cirugías</p>
                <p class="text-[#be4bdb] text-[32px] font-black tracking-tighter" x-text="stats.cirugias"></p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-[20px] border border-slate-100 shadow-sm mb-6 flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" x-model="searchTerm" placeholder="Buscar por código o descripción..." class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none transition-all">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            </div>
            <button class="bg-white border border-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50">Exportar</button>
        </div>

        <div class="bg-[#e9ecef] p-1.5 rounded-2xl flex w-full mb-8">
            <button @click="tab = 'servicios'" :class="tab === 'servicios' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600'" class="flex-1 py-3 rounded-[14px] text-sm font-bold transition-all">Servicios y Consultas</button>
            <button @click="tab = 'procedimientos'" :class="tab === 'procedimientos' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600'" class="flex-1 py-3 rounded-[14px] text-sm font-bold transition-all">Procedimientos</button>
            <button @click="tab = 'cirugias'" :class="tab === 'cirugias' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600'" class="flex-1 py-3 rounded-[14px] text-sm font-bold transition-all">Cirugías</button>
        </div>

        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden">

            <div x-show="tab === 'servicios'">
                <div class="p-8 border-b border-slate-50"><h3 class="font-bold text-slate-800 text-lg">Tarifario de Servicios y Consultas</h3></div>
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold border-b border-slate-50">
                        <tr><th class="px-10 py-5">Código</th><th class="px-10 py-5">Descripción</th><th class="px-10 py-5">Particular (S/)</th><th class="px-10 py-5">SIS (S/)</th><th class="px-10 py-5">EPS (S/)</th><th class="px-10 py-5 text-right">Acciones</th></tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        <template x-for="tarifa in tarifarios.filter(t => !searchTerm || t.codigo.toLowerCase().includes(searchTerm.toLowerCase()) || t.descripcion.toLowerCase().includes(searchTerm.toLowerCase()))" :key="tarifa.id">
                            <tr>
                                <td class="px-10 py-6 font-bold" x-text="tarifa.codigo"></td>
                                <td class="px-10 py-6" x-text="tarifa.descripcion"></td>
                                <td class="px-10 py-6" x-text="tarifa.precio_particular"></td>
                                <td class="px-10 py-6">
                                    <span x-show="tarifa.tipo_convenio_sis === 'CONVENIO'" class="text-slate-400 italic">Convenio</span>
                                    <span x-show="tarifa.tipo_convenio_sis !== 'CONVENIO'" x-text="tarifa.precio_sis || '-'"></span>
                                </td>
                                <td class="px-10 py-6">
                                    <span x-show="tarifa.tipo_convenio_eps === 'CONVENIO'" class="text-slate-400 italic">Convenio</span>
                                    <span x-show="tarifa.tipo_convenio_eps !== 'CONVENIO'" x-text="tarifa.precio_eps || '-'"></span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button @click="editingTarifa = tarifa" class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold mr-2">Editar</button>
                                    <button @click="deleteTarifa(tarifa)" class="border border-red-200 text-red-600 px-4 py-2 rounded-xl text-[12px] font-bold hover:bg-red-50">Eliminar</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="tab === 'procedimientos'" x-cloak>
                <div class="p-8 border-b border-slate-50"><h3 class="font-bold text-slate-800 text-lg">Tarifario de Procedimientos</h3></div>
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold border-b border-slate-50">
                        <tr><th class="px-10 py-5">Código</th><th class="px-10 py-5">Descripción</th><th class="px-10 py-5">Particular (S/)</th><th class="px-10 py-5">SIS (S/)</th><th class="px-10 py-5">EPS (S/)</th><th class="px-10 py-5 text-right">Acciones</th></tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        <template x-for="tarifa in procedimientos.filter(t => !searchTerm || t.codigo.toLowerCase().includes(searchTerm.toLowerCase()) || t.descripcion.toLowerCase().includes(searchTerm.toLowerCase()))" :key="tarifa.id">
                            <tr>
                                <td class="px-10 py-6 font-bold" x-text="tarifa.codigo"></td>
                                <td class="px-10 py-6" x-text="tarifa.descripcion"></td>
                                <td class="px-10 py-6" x-text="tarifa.precio_particular"></td>
                                <td class="px-10 py-6">
                                    <span x-show="tarifa.tipo_convenio_sis === 'CONVENIO'" class="text-slate-400 italic">Convenio</span>
                                    <span x-show="tarifa.tipo_convenio_sis !== 'CONVENIO'" x-text="tarifa.precio_sis || '-'"></span>
                                </td>
                                <td class="px-10 py-6">
                                    <span x-show="tarifa.tipo_convenio_eps === 'CONVENIO'" class="text-slate-400 italic">Convenio</span>
                                    <span x-show="tarifa.tipo_convenio_eps !== 'CONVENIO'" x-text="tarifa.precio_eps || '-'"></span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button @click="editingTarifa = tarifa" class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold mr-2">Editar</button>
                                    <button @click="deleteTarifa(tarifa)" class="border border-red-200 text-red-600 px-4 py-2 rounded-xl text-[12px] font-bold hover:bg-red-50">Eliminar</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="tab === 'cirugias'" x-cloak>
                <div class="p-8 border-b border-slate-50"><h3 class="font-bold text-slate-800 text-lg">Tarifario de Cirugías</h3></div>
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold border-b border-slate-50">
                        <tr><th class="px-10 py-5">Código</th><th class="px-10 py-5">Descripción</th><th class="px-10 py-5">Particular (S/)</th><th class="px-10 py-5">SIS (S/)</th><th class="px-10 py-5">EPS (S/)</th><th class="px-10 py-5 text-right">Acciones</th></tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        <template x-for="tarifa in cirugias.filter(t => !searchTerm || t.codigo.toLowerCase().includes(searchTerm.toLowerCase()) || t.descripcion.toLowerCase().includes(searchTerm.toLowerCase()))" :key="tarifa.id">
                            <tr>
                                <td class="px-10 py-6 font-bold" x-text="tarifa.codigo"></td>
                                <td class="px-10 py-6" x-text="tarifa.descripcion"></td>
                                <td class="px-10 py-6" x-text="tarifa.precio_particular"></td>
                                <td class="px-10 py-6">
                                    <span x-show="tarifa.tipo_convenio_sis === 'CONVENIO'" class="text-slate-400 italic">Convenio</span>
                                    <span x-show="tarifa.tipo_convenio_sis !== 'CONVENIO'" x-text="tarifa.precio_sis || '-'"></span>
                                </td>
                                <td class="px-10 py-6">
                                    <span x-show="tarifa.tipo_convenio_eps === 'CONVENIO'" class="text-slate-400 italic">Convenio</span>
                                    <span x-show="tarifa.tipo_convenio_eps !== 'CONVENIO'" x-text="tarifa.precio_eps || '-'"></span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button @click="editingTarifa = tarifa" class="border border-slate-200 px-4 py-2 rounded-xl text-[12px] font-bold mr-2">Editar</button>
                                    <button @click="deleteTarifa(tarifa)" class="border border-red-200 text-red-600 px-4 py-2 rounded-xl text-[12px] font-bold hover:bg-red-50">Eliminar</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar tarifa -->
    <div x-show="showAddModal || editingTarifa" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showAddModal = false; editingTarifa = null">
        <div class="bg-white rounded-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold text-slate-800 mb-6" x-text="editingTarifa ? 'Editar Tarifa' : 'Nueva Tarifa'"></h2>
            
            <form @submit.prevent="submitTarifa">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Código</label>
                        <input type="text" 
                               x-model="formData.codigo"
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-50 outline-none"
                               :readonly="editingTarifa"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Categoría</label>
                        <select x-model="formData.categoria"
                                class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-50 outline-none"
                                required>
                            <option value="">Seleccionar</option>
                            <option value="SERVICIO">Servicio</option>
                            <option value="PROCEDIMIENTO">Procedimiento</option>
                            <option value="CIRUGIA">Cirugía</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
                    <textarea x-model="formData.descripcion"
                              rows="3"
                              class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-50 outline-none"
                              required></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Precio Particular (S/)</label>
                        <input type="number" 
                               step="0.01"
                               x-model="formData.precio_particular"
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-50 outline-none"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Precio SIS (S/)</label>
                        <input type="number" 
                               step="0.01"
                               x-model="formData.precio_sis"
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-50 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Precio EPS (S/)</label>
                        <input type="number" 
                               step="0.01"
                               x-model="formData.precio_eps"
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-50 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Convenio SIS</label>
                        <select x-model="formData.tipo_convenio_sis"
                                class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-50 outline-none">
                            <option value="">Seleccionar</option>
                            <option value="CONVENIO">Convenio</option>
                            <option value="TARIFARIO">Tarifario</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Convenio EPS</label>
                        <select x-model="formData.tipo_convenio_eps"
                                class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-50 outline-none">
                            <option value="">Seleccionar</option>
                            <option value="CONVENIO">Convenio</option>
                            <option value="TARIFARIO">Tarifario</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="button" 
                            @click="showAddModal = false; editingTarifa = null"
                            class="px-6 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-[#0061df] hover:bg-blue-700 text-white rounded-xl font-bold">
                        <span x-text="editingTarifa ? 'Actualizar' : 'Guardar'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tarifariosApp', () => ({
                tab: 'servicios',
                tarifarios: @json($servicios),
                procedimientos: @json($procedimientos),
                cirugias: @json($cirugias),
                stats: @json($stats),
                searchTerm: '',
                showAddModal: false,
                editingTarifa: null,
                formData: {
                    codigo: '',
                    descripcion: '',
                    categoria: '',
                    precio_particular: '',
                    precio_sis: '',
                    precio_eps: '',
                    tipo_convenio_sis: '',
                    tipo_convenio_eps: ''
                },

                deleteTarifa(tarifa) {
                    if (confirm('¿Está seguro de eliminar esta tarifa?')) {
                        const formData = new FormData();
                        formData.append('_method', 'DELETE');
                        
                        fetch(`/admin/tarifarios/${tarifa.id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert('Error al eliminar la tarifa: ' + (data.message || 'Error desconocido'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al eliminar la tarifa');
                        });
                    }
                },

                submitTarifa() {
                    console.log('Enviando formulario:', this.formData);
                    
                    const url = this.editingTarifa ? `/admin/tarifarios/${this.editingTarifa.id}` : '/admin/tarifarios';
                    const method = this.editingTarifa ? 'POST' : 'POST'; // Laravel uses POST with _method for PUT/DELETE
                    
                    const formData = new FormData();
                    Object.keys(this.formData).forEach(key => {
                        formData.append(key, this.formData[key]);
                    });
                    
                    if (this.editingTarifa) {
                        formData.append('_method', 'PUT');
                    }
                    
                    console.log('URL:', url);
                    console.log('FormData:', Array.from(formData.entries()));
                    
                    fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Error al guardar la tarifa: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al guardar la tarifa');
                    });
                },

                init() {
                    // Debug: verificar que los datos se carguen correctamente
                    console.log('Datos iniciales:', {
                        tarifarios: this.tarifarios,
                        procedimientos: this.procedimientos,
                        cirugias: this.cirugias,
                        stats: this.stats
                    });
                    
                    this.$watch('editingTarifa', (value) => {
                        if (value) {
                            this.formData = { ...value };
                            console.log('Editando tarifa:', value);
                        } else {
                            this.resetForm();
                        }
                    });
                },

                resetForm() {
                    this.formData = {
                        codigo: '',
                        descripcion: '',
                        categoria: '',
                        precio_particular: '',
                        precio_sis: '',
                        precio_eps: '',
                        tipo_convenio_sis: '',
                        tipo_convenio_eps: ''
                    };
                }
            }));
        });
    </script>
</x-app-layout>
