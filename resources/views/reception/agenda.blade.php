@extends('layouts.app')
@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen" x-data="agendaRecepcion()">

    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Agenda de Citas</h1>
            <p class="text-sm text-gray-500">Gestión de citas médicas del día</p>
        </div>
        <div class="flex gap-3">
            <input type="date" x-model="fecha" @change="cargarAgenda()"
                   class="border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white">
            <button @click="mostrarModalNuevaCita = true"
                    class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition text-sm">
                + Nueva Cita
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total</p>
            <p class="text-2xl font-bold text-gray-800" x-text="citas.length"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Confirmadas</p>
            <p class="text-2xl font-bold text-green-600" x-text="citas.filter(c=>c.confirmado).length"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Pendientes</p>
            <p class="text-2xl font-bold text-yellow-600" x-text="citas.filter(c=>!c.confirmado && c.estado !== 'cancelado').length"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Atendidas</p>
            <p class="text-2xl font-bold text-blue-600" x-text="citas.filter(c=>c.estado==='atendido').length"></p>
        </div>
    </div>

    {{-- Lista de citas --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex gap-3">
            <input type="text" x-model="busqueda" placeholder="Buscar paciente o médico..."
                   class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm bg-gray-50">
        </div>

        <div x-show="cargando" class="p-8 text-center text-gray-400">Cargando...</div>

        <table class="w-full text-sm" x-show="!cargando">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Especialidad</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <template x-for="cita in citasFiltradas" :key="cita.id">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-mono font-medium" x-text="cita.hora_formateada"></td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900" x-text="cita.paciente?.nombre ?? 'N/A'"></p>
                            <p class="text-xs text-gray-400" x-text="'CI: ' + (cita.paciente?.ci ?? 'N/A')"></p>
                        </td>
                        <td class="px-4 py-3" x-text="cita.medico?.nombre_completo ?? 'N/A'"></td>
                        <td class="px-4 py-3" x-text="cita.especialidad?.nombre ?? 'N/A'"></td>
                        <td class="px-4 py-3">
                            <span :class="{
                                'bg-green-100 text-green-700': cita.confirmado && cita.estado !== 'cancelado',
                                'bg-yellow-100 text-yellow-700': !cita.confirmado && cita.estado === 'programado',
                                'bg-blue-100 text-blue-700': cita.estado === 'en_atencion',
                                'bg-gray-100 text-gray-500': cita.estado === 'cancelado',
                                'bg-teal-100 text-teal-700': cita.estado === 'atendido'
                            }" class="px-2 py-0.5 rounded-full text-xs font-medium"
                            x-text="cita.estado_label"></span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button x-show="!cita.confirmado && cita.estado === 'programado'"
                                        @click="confirmar(cita.id)"
                                        class="px-2 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition">
                                    Confirmar
                                </button>
                                <button x-show="cita.estado !== 'cancelado' && cita.estado !== 'atendido'"
                                        @click="cancelar(cita.id)"
                                        class="px-2 py-1 border border-red-200 text-red-600 rounded text-xs hover:bg-red-50 transition">
                                    Cancelar
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="citasFiltradas.length === 0 && !cargando">
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        No hay citas para este día
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Modal nueva cita --}}
    <div x-show="mostrarModalNuevaCita" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" x-cloak>
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg mx-4">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Nueva Cita</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CI Paciente *</label>
                    <input type="text" x-model="nuevaCita.ci_paciente" placeholder="Número de CI"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CI Médico *</label>
                    <input type="text" x-model="nuevaCita.ci_medico" placeholder="CI del médico"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date" x-model="nuevaCita.fecha"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                        <input type="time" x-model="nuevaCita.hora"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                    <textarea x-model="nuevaCita.motivo" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm"
                              placeholder="Motivo de la cita..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button @click="mostrarModalNuevaCita = false"
                        class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button @click="crearCita()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                    Crear Cita
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function agendaRecepcion() {
    return {
        fecha: new Date().toISOString().split('T')[0],
        citas: [],
        cargando: false,
        busqueda: '',
        mostrarModalNuevaCita: false,
        nuevaCita: { ci_paciente: '', ci_medico: '', fecha: '', hora: '', motivo: '' },

        get citasFiltradas() {
            if (!this.busqueda) return this.citas;
            const b = this.busqueda.toLowerCase();
            return this.citas.filter(c =>
                (c.paciente?.nombre ?? '').toLowerCase().includes(b) ||
                (c.medico?.nombre_completo ?? '').toLowerCase().includes(b)
            );
        },

        async cargarAgenda() {
            this.cargando = true;
            try {
                const r = await fetch(`{{ route('reception.agenda-dia') }}?fecha=${this.fecha}`);
                const d = await r.json();
                this.citas = d.citas ?? [];
            } catch(e) { console.error(e); }
            finally { this.cargando = false; }
        },

        async confirmar(id) {
            if (!confirm('¿Confirmar esta cita?')) return;
            const r = await fetch(`/api/cita/${id}/confirmar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            const d = await r.json();
            if (d.success) this.cargarAgenda();
            else alert(d.message);
        },

        async cancelar(id) {
            if (!confirm('¿Cancelar esta cita?')) return;
            const r = await fetch(`/api/cita/${id}/cancelar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ motivo: 'Cancelado desde recepción' })
            });
            const d = await r.json();
            if (d.success) this.cargarAgenda();
            else alert(d.message);
        },

        async crearCita() {
            const r = await fetch('{{ route("reception.nueva-cita") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.nuevaCita)
            });
            const d = await r.json();
            if (d.success) {
                this.mostrarModalNuevaCita = false;
                this.nuevaCita = { ci_paciente: '', ci_medico: '', fecha: '', hora: '', motivo: '' };
                this.cargarAgenda();
            } else {
                alert(d.message);
            }
        },

        init() { this.cargarAgenda(); }
    }
}
</script>
@endsection
