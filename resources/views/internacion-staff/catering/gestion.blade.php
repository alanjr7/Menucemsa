@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestionar Catering</h1>
            <p class="text-sm text-gray-500">Configurar precios de alimentación para pacientes</p>
        </div>
        <a href="{{ route('internacion-staff.dashboard') }}"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
            ← Volver al panel
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-orange-100/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Precios de Catering</h3>
                        <p class="text-sm text-gray-500">Estos precios se aplicarán automáticamente al registrar catering</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form id="formPrecios" class="space-y-6">
                    @csrf

                    {{-- Desayuno --}}
                    <div class="flex items-center gap-4 p-4 bg-yellow-50 rounded-xl border border-yellow-100">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Desayuno</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Bs.</span>
                                <input type="number" name="desayuno" id="desayuno" step="0.01" min="0"
                                    value="{{ $precios['desayuno'] ?? 0 }}"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    {{-- Almuerzo --}}
                    <div class="flex items-center gap-4 p-4 bg-green-50 rounded-xl border border-green-100">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Almuerzo</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Bs.</span>
                                <input type="number" name="almuerzo" id="almuerzo" step="0.01" min="0"
                                    value="{{ $precios['almuerzo'] ?? 0 }}"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    {{-- Merienda --}}
                    <div class="flex items-center gap-4 p-4 bg-purple-50 rounded-xl border border-purple-100">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Merienda</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Bs.</span>
                                <input type="number" name="merienda" id="merienda" step="0.01" min="0"
                                    value="{{ $precios['merienda'] ?? 0 }}"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    {{-- Cena --}}
                    <div class="flex items-center gap-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cena</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Bs.</span>
                                <input type="number" name="cena" id="cena" step="0.01" min="0"
                                    value="{{ $precios['cena'] ?? 0 }}"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="guardarPrecios()"
                            class="flex-1 px-4 py-3 bg-orange-600 text-white font-medium rounded-xl hover:bg-orange-700 transition-colors shadow-sm">
                            Guardar Precios
                        </button>
                        <a href="{{ route('internacion-staff.catering.index') }}"
                            class="px-4 py-3 border border-gray-200 text-gray-600 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                            Ver Catering
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info adicional --}}
        <div class="mt-6 bg-blue-50 rounded-xl p-4 border border-blue-100">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Información</p>
                    <p>Los precios configurados aquí se aplicarán automáticamente cuando se registre catering para cualquier paciente del sistema. Los cambios son inmediatos.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function guardarPrecios() {
        const desayuno = document.getElementById('desayuno').value;
        const almuerzo = document.getElementById('almuerzo').value;
        const merienda = document.getElementById('merienda').value;
        const cena = document.getElementById('cena').value;

        if (desayuno < 0 || almuerzo < 0 || merienda < 0 || cena < 0) {
            alert('Los precios no pueden ser negativos');
            return;
        }

        try {
            const response = await fetch('{{ route('internacion-staff.api.catering-precios.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    desayuno: parseFloat(desayuno),
                    almuerzo: parseFloat(almuerzo),
                    merienda: parseFloat(merienda),
                    cena: parseFloat(cena)
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Precios actualizados correctamente');
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los precios');
        }
    }
</script>
@endsection
