<x-app-layout>
    <div class="p-8 bg-[#f8fafc] min-h-screen font-sans" x-data="inventorySystem()">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Inventario</h1>
                <p class="text-gray-500 text-sm" x-text="productos.length + ' productos en total'"></p>
            </div>
            <button @click="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-blue-100 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo Producto
            </button>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" x-model="search"
                    class="block w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-gray-400 text-sm"
                    placeholder="Buscar producto o código de barras...">
            </div>
            <select x-model="selectedCategory"
                class="border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-4 bg-white">
                <option value="Todas">Todas</option>
                <option value="Medicamento">Medicamento</option>
                <option value="Receta">Receta</option>
                <option value="Cuidado Personal">Cuidado Personal</option>
                <option value="Vitaminas">Vitaminas</option>
                <option value="Primeros Auxilios">Primeros Auxilios</option>
                <option value="Otros">Otros</option>
            </select>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Precio</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Stock</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Proveedor</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="p in filteredProducts" :key="p.id">
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800 text-sm" x-text="p.nombre"></p>
                                <template x-if="p.receta">
                                    <span class="text-[10px] bg-red-50 text-red-500 px-1.5 py-0.5 rounded font-medium">Requiere Receta</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="p.categoria"></td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-800 text-center" x-text="'$' + p.precio.toFixed(2)"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold"
                                      :class="p.stock < p.stockMinimo ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-700'"
                                      x-text="p.stock"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400 font-mono" x-text="p.codigo"></td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="p.proveedor"></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="editProduct(p)" class="p-1.5 text-blue-500 hover:bg-blue-100 rounded-md border border-blue-100 shadow-sm transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="deleteProduct(p.id)" class="p-1.5 text-red-400 hover:bg-red-50 rounded-md border border-red-50 shadow-sm transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden" @click.away="showEditModal = false">

                    <div class="flex justify-between items-center px-8 py-6 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800" x-text="isEdit ? 'Editar Producto' : 'Nuevo Producto'"></h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Producto *</label>
                            <input type="text" x-model="editingProduct.nombre" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Categoría</label>
                                <select x-model="editingProduct.categoria" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                                    <option value="Medicamento">Medicamento</option>
                                    <option value="Receta">Receta</option>
                                    <option value="Cuidado Personal">Cuidado Personal</option>
                                    <option value="Vitaminas">Vitaminas</option>
                                    <option value="Primeros Auxilios">Primeros Auxilios</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Precio</label>
                                <input type="number" step="0.01" x-model="editingProduct.precio" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Stock Actual</label>
                                <input type="number" x-model="editingProduct.stock" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Stock Mínimo</label>
                                <input type="number" x-model="editingProduct.stockMinimo" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Código de Barras *</label>
                                <input type="text" x-model="editingProduct.codigo" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Proveedor</label>
                                <input type="text" x-model="editingProduct.proveedor" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">📅 Fecha de Vencimiento</label>
                                <input type="text" x-model="editingProduct.vencimiento" placeholder="dd/mm/aaaa" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Número de Lote</label>
                                <input type="text" x-model="editingProduct.lote" placeholder="Ej: PAR-2024-001" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                            <textarea x-model="editingProduct.descripcion" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-gray-50 flex justify-end gap-3">
                        <button @click="showEditModal = false" class="px-6 py-2.5 text-gray-600 font-semibold hover:bg-gray-100 rounded-xl transition-all">Cancelar</button>
                        <button @click="saveProduct()" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">
                            <span x-text="isEdit ? 'Guardar Cambios' : 'Crear Producto'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    function inventorySystem() {
        return {
            search: '',
            selectedCategory: 'Todas',
            showEditModal: false,
            isEdit: false,
            editingProduct: {},
     productos: [
                { id: 1, nombre: 'Paracetamol 500mg', precio: 5.50, stock: 150, categoria: 'Medicamento', codigo: '7501234567890', proveedor: 'FarmaLab', receta: false },
                { id: 2, nombre: 'Ibuprofeno 400mg', precio: 8.00, stock: 120, categoria: 'Medicamento', codigo: '7501234567891', proveedor: 'FarmaLab', receta: false },
                { id: 3, nombre: 'Amoxicilina 500mg', precio: 45.00, stock: 80, categoria: 'Receta', codigo: '7501234567892', proveedor: 'MediPharma', receta: true },
                { id: 4, nombre: 'Omeprazol 20mg', precio: 12.50, stock: 90, categoria: 'Medicamento', codigo: '7501234567893', proveedor: 'FarmaLab', receta: false },
                { id: 5, nombre: 'Loratadina 10mg', precio: 15.00, stock: 100, categoria: 'Medicamento', codigo: '7501234567894', proveedor: 'AllergyPharma', receta: false },
                { id: 6, nombre: 'Vitamina C 1000mg', precio: 25.00, stock: 60, categoria: 'Vitaminas', codigo: '7501234567895', proveedor: 'VitaPlus', receta: false },
                { id: 7, nombre: 'Alcohol en Gel 250ml', precio: 18.00, stock: 200, categoria: 'Cuidado Personal', codigo: '7501234567896', proveedor: 'HygieneCare', receta: false },
                { id: 8, nombre: 'Vendas Elásticas', precio: 12.00, stock: 45, categoria: 'Primeros Auxilios', codigo: '7501234567897', proveedor: 'MediSupply', receta: false },
                { id: 9, nombre: 'Enalapril 10mg', precio: 35.00, stock: 65, categoria: 'Receta', codigo: '7501234567898', proveedor: 'CardioMed', receta: true },
                { id: 10, nombre: 'Metformina 850mg', precio: 28.00, stock: 70, categoria: 'Receta', codigo: '7501234567899', proveedor: 'DiabetesCare', receta: true },
                { id: 11, nombre: 'Termómetro Digital', precio: 85.00, stock: 25, categoria: 'Primeros Auxilios', codigo: '7501234567800', proveedor: 'MediSupply', receta: false },
                { id: 12, nombre: 'Complejo B', precio: 32.00, stock: 55, categoria: 'Vitaminas', codigo: '7501234567801', proveedor: 'VitaPlus', receta: false },
                { id: 13, nombre: 'Suero Oral', precio: 8.50, stock: 110, categoria: 'Medicamento', codigo: '7501234567802', proveedor: 'HydratePlus', receta: false },
                { id: 14, nombre: 'Diclofenaco Gel', precio: 42.00, stock: 40, categoria: 'Medicamento', codigo: '7501234567803', proveedor: 'FarmaLab', receta: false },
                { id: 15, nombre: 'Mascarillas Quirúrgicas x50', precio: 95.00, stock: 15, categoria: 'Cuidado Personal', codigo: '7501234567804', proveedor: 'HygieneCare', receta: false }
            ],

            get filteredProducts() {
                return this.productos.filter(p => {
                    const matchSearch = p.nombre.toLowerCase().includes(this.search.toLowerCase()) ||
                                      p.codigo.includes(this.search);
                    const matchCategory = this.selectedCategory === 'Todas' ||
                                        p.categoria === this.selectedCategory;
                    return matchSearch && matchCategory;
                });
            },

            openCreateModal() {
                this.isEdit = false;
                this.editingProduct = {
                    id: Date.now(),
                    nombre: '',
                    categoria: 'Medicamento',
                    precio: 0,
                    stock: 0,
                    stockMinimo: 10,
                    codigo: '',
                    proveedor: '',
                    vencimiento: '',
                    lote: '',
                    descripcion: '',
                    receta: false
                };
                this.showEditModal = true;
            },

            editProduct(product) {
                this.isEdit = true;
                this.editingProduct = JSON.parse(JSON.stringify(product));
                this.showEditModal = true;
            },

            saveProduct() {
                // Validación básica
                if(!this.editingProduct.nombre || !this.editingProduct.codigo) {
                    alert('Nombre y Código son obligatorios');
                    return;
                }

                this.editingProduct.receta = (this.editingProduct.categoria === 'Receta');

                if (this.isEdit) {
                    const index = this.productos.findIndex(p => p.id === this.editingProduct.id);
                    if (index !== -1) {
                        this.productos[index] = this.editingProduct;
                    }
                } else {
                    this.productos.unshift(this.editingProduct);
                }
                this.showEditModal = false;
            },

            deleteProduct(id) {
                if(confirm('¿Estás seguro de eliminar este producto?')) {
                    this.productos = this.productos.filter(p => p.id !== id);
                }
            }
        }
    }
</script>
</x-app-layout>
