@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen" x-data="{
    madreCI: '',
    madreNombre: '',
    buscando: false,
    async buscarMadre() {
        if (!this.madreCI || this.madreCI.length < 3) return;
        this.buscando = true;
        try {
            const r = await axios.get('{{ route('neonato.api.buscar-madre') }}', { params: { ci: this.madreCI } });
            this.madreNombre = r.data.found ? r.data.nombre : '';
        } catch(e) {
            this.madreNombre = '';
        } finally {
            this.buscando = false;
        }
    }
}">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Añadir Recién Nacido</h1>
        <a href="{{ route('neonato.index') }}" class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Volver</a>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('neonato.store') }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Datos del RN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3">Datos del recién nacido</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre (si se conoce)</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                            class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Ej: Juan Carlos..." autocomplete="off">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
                        <select name="sexo" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="">Sin definir</option>
                            <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora de nacimiento</label>
                    <input type="datetime-local" name="fecha_hora_nacimiento"
                        value="{{ old('fecha_hora_nacimiento', now()->format('Y-m-d\TH:i')) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de parto</label>
                    <select name="tipo_parto" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="">Seleccionar...</option>
                        <option value="normal"        {{ old('tipo_parto') === 'normal'        ? 'selected' : '' }}>Normal / Eutócico</option>
                        <option value="cesarea"       {{ old('tipo_parto') === 'cesarea'       ? 'selected' : '' }}>Cesárea</option>
                        <option value="instrumentado" {{ old('tipo_parto') === 'instrumentado' ? 'selected' : '' }}>Instrumentado</option>
                    </select>
                </div>
            </div>

            {{-- Antropometría y Apgar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3">Antropometría y Apgar</h3>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peso (g)</label>
                        <input type="number" name="peso" value="{{ old('peso') }}" step="1" min="0"
                            class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="3200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Talla (cm)</label>
                        <input type="number" name="talla" value="{{ old('talla') }}" step="0.1" min="0"
                            class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">P. cefálico (cm)</label>
                        <input type="number" name="perimetro_cefalico" value="{{ old('perimetro_cefalico') }}" step="0.1" min="0"
                            class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="34">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apgar 1 min <span class="text-gray-400">(0–10)</span></label>
                        <input type="number" name="apgar1" value="{{ old('apgar1') }}" min="0" max="10"
                            class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="8">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apgar 5 min <span class="text-gray-400">(0–10)</span></label>
                        <input type="number" name="apgar5" value="{{ old('apgar5') }}" min="0" max="10"
                            class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="9">
                    </div>
                </div>

                {{-- Madre --}}
                <div class="pt-2 border-t border-gray-100">
                    <label class="block text-sm font-medium text-gray-700 mb-1">CI de la madre</label>
                    <div class="flex gap-2">
                        <input type="text" name="madre_ci" x-model="madreCI"
                            value="{{ old('madre_ci') }}"
                            @blur="buscarMadre()"
                            class="flex-1 border rounded-lg px-3 py-2 text-sm" placeholder="Número de CI" autocomplete="off">
                        <button type="button" @click="buscarMadre()"
                            class="px-3 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                            <span x-show="!buscando">Buscar</span>
                            <span x-show="buscando">...</span>
                        </button>
                    </div>
                    <div x-show="madreNombre" class="mt-2 rounded-lg bg-green-50 border border-green-200 px-3 py-2 text-sm text-green-700">
                        Madre: <strong x-text="madreNombre"></strong>
                    </div>
                    <div x-show="madreCI && madreCI.length >= 3 && !madreNombre && !buscando"
                        class="mt-2 rounded-lg bg-yellow-50 border border-yellow-200 px-3 py-2 text-xs text-yellow-700">
                        No se encontró paciente con ese CI. Se guardará el número igual.
                    </div>
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones clínicas</label>
                <textarea name="observaciones" rows="3"
                    class="w-full border rounded-lg px-3 py-2 text-sm resize-none"
                    placeholder="Notas relevantes del nacimiento...">{{ old('observaciones') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('neonato.index') }}" class="px-6 py-2 border rounded-xl text-sm text-gray-600">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-xl text-sm hover:bg-pink-700 font-medium shadow-sm">
                Registrar recién nacido
            </button>
        </div>
    </form>
</div>
@endsection
