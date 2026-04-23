@extends('layouts.app')
@section('content')
    <div x-data="{ 
        tab: 'servicios', 
        searchTerm: '',
        servicios: @json($servicios),
        procedimientos: @json($procedimientos),
        cirugias: @json($cirugias),
        stats: @json($stats),
        showAddModal: false,
        editingTarifa: null,
        init() {
            this.editingTarifa = {
                id: null,
                codigo: '',
                descripcion: '',
                categoria: 'SERVICIO',
                precio_particular: 0,
                precio_sis: 0,
                precio_eps: 0,
                tipo_convenio_sis: '',
                tipo_convenio_eps: ''
            };
        }
    }" class="p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

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
                        <template x-for="tarifa in servicios.filter(t => !searchTerm || t.codigo.toLowerCase().includes(searchTerm.toLowerCase()) || t.descripcion.toLowerCase().includes(searchTerm.toLowerCase()))" :key="tarifa.id">
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

        <!-- Modal: Nuevo Servicio -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.away="showAddModal = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4" @click.stop>
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800">Nuevo Servicio/Tarifa</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="formNuevaTarifa" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Código</label>
                            <input type="text" name="codigo" required class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
                            <select name="categoria" required class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="SERVICIO">Servicio/Consulta</option>
                                <option value="PROCEDIMIENTO">Procedimiento</option>
                                <option value="CIRUGIA">Cirugía</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                        <input type="text" name="descripcion" required class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Particular (S/)</label>
                            <input type="number" name="precio_particular" step="0.01" min="0" required class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">SIS (S/)</label>
                            <input type="number" name="precio_sis" step="0.01" min="0" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">EPS (S/)</label>
                            <input type="number" name="precio_eps" step="0.01" min="0" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo SIS</label>
                            <select name="tipo_convenio_sis" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                <option value="">Tarifario</option>
                                <option value="CONVENIO">Convenio</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo EPS</label>
                            <select name="tipo_convenio_eps" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                <option value="">Tarifario</option>
                                <option value="CONVENIO">Convenio</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50">Cancelar</button>
                        <button type="button" @click="guardarTarifa()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Editar Servicio -->
        <div x-show="editingTarifa && editingTarifa.id" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.away="editingTarifa = null">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4" @click.stop>
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800">Editar Servicio/Tarifa</h3>
                    <button @click="editingTarifa = null" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form x-show="editingTarifa" id="formEditarTarifa" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Código</label>
                            <input type="text" name="codigo" x-model="editingTarifa.codigo" required class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
                            <select name="categoria" x-model="editingTarifa.categoria" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                <option value="SERVICIO">Servicio/Consulta</option>
                                <option value="PROCEDIMIENTO">Procedimiento</option>
                                <option value="CIRUGIA">Cirugía</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                        <input type="text" name="descripcion" x-model="editingTarifa.descripcion" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Particular (S/)</label>
                            <input type="number" name="precio_particular" x-model="editingTarifa.precio_particular" step="0.01" min="0" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">SIS (S/)</label>
                            <input type="number" name="precio_sis" x-model="editingTarifa.precio_sis" step="0.01" min="0" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">EPS (S/)</label>
                            <input type="number" name="precio_eps" x-model="editingTarifa.precio_eps" step="0.01" min="0" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo SIS</label>
                            <select name="tipo_convenio_sis" x-model="editingTarifa.tipo_convenio_sis" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                <option value="">Tarifario</option>
                                <option value="CONVENIO">Convenio</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo EPS</label>
                            <select name="tipo_convenio_eps" x-model="editingTarifa.tipo_convenio_eps" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                                <option value="">Tarifario</option>
                                <option value="CONVENIO">Convenio</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="editingTarifa = null" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50">Cancelar</button>
                        <button type="button" @click="actualizarTarifa()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function guardarTarifa() {
            const form = document.getElementById('formNuevaTarifa');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('{{ route('admin.tarifarios.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message || 'Error al guardar');
                }
            } catch (error) {
                alert('Error de conexión');
            }
        }

        async function actualizarTarifa() {
            const form = document.getElementById('formEditarTarifa');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            const tarifaId = Alpine.$data(document.querySelector('[x-data]')).editingTarifa.id;

            try {
                const response = await fetch(`{{ url('admin/tarifarios') }}/${tarifaId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message || 'Error al actualizar');
                }
            } catch (error) {
                alert('Error de conexión');
            }
        }

        async function deleteTarifa(tarifa) {
            if (!confirm('¿Está seguro de eliminar esta tarifa?')) return;

            try {
                const response = await fetch(`{{ url('admin/tarifarios') }}/${tarifa.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message || 'Error al eliminar');
                }
            } catch (error) {
                alert('Error de conexión');
            }
        }
    </script>
    @endpush
@endsection
