@if(!isset($consulta) || !$consulta)
<div class="bg-gray-50 rounded-xl border border-dashed border-gray-200 p-8 text-center">
    <p class="text-gray-500">No hay paciente en atención actualmente</p>
</div>
@else
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" 
     x-data="atencionActual({{ $consulta->id }}, '{{ $consulta->codigo }}')">

    {{-- Header --}}
    <div class="flex justify-between items-start border-b pb-4">
        <div>
            <h3 class="text-blue-900 font-bold text-lg">Atención en Consulta</h3>
            <p class="text-gray-500 text-sm">
                {{ $consulta->paciente?->nombre ?? 'Paciente' }} — 
                CI: {{ $consulta->paciente?->ci ?? 'N/A' }}
                @if($consulta->paciente?->fecha_nacimiento)
                    — {{ \Carbon\Carbon::parse($consulta->paciente->fecha_nacimiento)->age }} años
                @endif
            </p>
        </div>
        <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full border border-orange-200">
            ● En Proceso
        </span>
    </div>

    {{-- Formulario de atención --}}
    <div class="space-y-4">
        <div>
            <label class="block font-bold text-gray-700 text-sm mb-1">Motivo de Consulta</label>
            <textarea x-model="form.motivo" class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="2" placeholder="Motivo referido por el paciente...">{{ $consulta->motivo }}</textarea>
        </div>
        <div>
            <label class="block font-bold text-gray-700 text-sm mb-1">Diagnóstico / Observaciones</label>
            <textarea x-model="form.observaciones" class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Diagnóstico, hallazgos, indicaciones...">{{ $consulta->observaciones }}</textarea>
        </div>
    </div>

    {{-- Receta --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-bold text-gray-700 text-sm">Receta Médica</h4>
            <button @click="agregarMedicamento()" type="button"
                    class="flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-medium hover:bg-blue-100 transition">
                + Agregar medicamento
            </button>
        </div>

        <div class="space-y-2" x-show="form.medicamentos.length > 0">
            <template x-for="(med, i) in form.medicamentos" :key="i">
                <div class="flex gap-2 items-center bg-gray-50 rounded-lg p-2 border border-gray-100">
                    <select x-model="med.codigo" class="flex-1 border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">— Seleccionar medicamento —</option>
                        @foreach($medicamentos as $m)
                        <option value="{{ $m->codigo }}">{{ $m->descripcion }}</option>
                        @endforeach
                    </select>
                    <input x-model="med.dosis" type="text" placeholder="Dosis (ej: 500mg c/8h)"
                           class="w-44 border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                    <input x-model="med.cantidad" type="number" min="1" value="1" placeholder="Cant."
                           class="w-16 border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white text-center">
                    <button @click="form.medicamentos.splice(i,1)" type="button"
                            class="text-red-400 hover:text-red-600 transition p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        <div x-show="form.medicamentos.length === 0" class="text-sm text-gray-400 italic py-2">
            Sin medicamentos prescritos aún
        </div>

        <div x-show="form.medicamentos.length > 0" class="mt-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Indicaciones generales</label>
            <input x-model="form.indicaciones" type="text" placeholder="Ej: Tomar con alimentos, reposo relativo..."
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50">
        </div>
    </div>

    {{-- Acciones --}}
    <div class="flex justify-between items-center pt-4 border-t">
        <div>
            <span x-show="mensaje" x-text="mensaje" class="text-sm text-green-600 font-medium"></span>
            <span x-show="error" x-text="error" class="text-sm text-red-500 font-medium"></span>
        </div>
        <div class="flex gap-3">
            <button @click="finalizarConsulta()" type="button"
                    :disabled="cargando"
                    class="bg-emerald-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-emerald-700 transition flex items-center gap-2 disabled:opacity-50">
                <span x-show="!cargando">Finalizar Consulta</span>
                <span x-show="cargando">Guardando...</span>
            </button>
        </div>
    </div>
</div>

<script>
function atencionActual(consultaId, consultaCodigo) {
    return {
        consultaId,
        consultaCodigo,
        cargando: false,
        mensaje: '',
        error: '',
        form: {
            motivo: '',
            observaciones: '',
            indicaciones: '',
            medicamentos: []
        },
        agregarMedicamento() {
            this.form.medicamentos.push({ codigo: '', dosis: '', cantidad: 1 });
        },
        async finalizarConsulta() {
            this.cargando = true;
            this.mensaje = '';
            this.error = '';
            try {
                const res = await fetch(`/consulta-externa/completar/${this.consultaCodigo}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        observaciones: this.form.observaciones,
                        indicaciones: this.form.indicaciones,
                        medicamentos: this.form.medicamentos.filter(m => m.codigo)
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.mensaje = 'Consulta finalizada correctamente';
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.error = data.message || 'Error al finalizar';
                }
            } catch(e) {
                this.error = 'Error de conexión';
            } finally {
                this.cargando = false;
            }
        }
    }
}
</script>
@endif
