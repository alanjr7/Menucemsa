@extends('layouts.app')
@section('content')
    <div class="p-6 bg-gray-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Procesar Pago</h1>
                        <p class="text-sm text-gray-500 mt-1">Consulta Externa - Confirmación de pago</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">N° de Registro</p>
                        <p class="font-mono text-sm font-bold text-blue-600">{{ $caja->id }}</p>
                    </div>
                </div>
            </div>

            <!-- Información de la Consulta -->
            <div class="bg-blue-50 rounded-2xl p-6 mb-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Detalles de la Consulta
                </h3>
                
                @if($caja->consulta)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Paciente</p>
                            <p class="font-semibold text-gray-900">{{ $caja->consulta->paciente->nombre ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">CI: {{ $caja->consulta->paciente->ci ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Médico</p>
                            <p class="font-semibold text-gray-900">{{ $caja->consulta->medico->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $caja->consulta->especialidad->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Fecha y Hora</p>
                            <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($caja->consulta->fecha)->format('d/m/Y') ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $caja->consulta->hora ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Motivo</p>
                            <p class="font-semibold text-gray-900">{{ $caja->consulta->motivo ?? 'N/A' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No se encontraron detalles de la consulta.</p>
                @endif
            </div>

            <!-- Información de Pago -->
            <div class="bg-yellow-50 rounded-2xl p-6 mb-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Detalles del Pago
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Monto a Pagar</p>
                        <p class="text-2xl font-bold text-green-600">Bs. {{ number_format($caja->monto_pagado, 2) }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Estado</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if($caja->estado === 'pagado') bg-green-100 text-green-800
                            @elseif($caja->estado === 'pendiente') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($caja->estado) }}
                        </span>
                    </div>
                    <div class="bg-white rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Tipo</p>
                        <p class="font-semibold text-gray-900">{{ $caja->tipo }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">N° Factura</p>
                        <p class="font-semibold text-gray-900 font-mono">{{ $caja->nro_factura }}</p>
                    </div>
                </div>
            </div>

            <!-- Formulario de Procesamiento -->
            @if($caja->estado === 'pendiente')
                <form id="formProcesarPago" onsubmit="procesarPago(event, '{{ $caja->id }}')">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <h3 class="font-bold text-gray-800 mb-4">Confirmar Pago</h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                            <select name="metodo_pago" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all" required>
                                <option value="">Seleccione método de pago...</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="seguro">Seguro Médico</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="confirmacion" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50" required>
                                <span class="ml-2 text-sm text-gray-700">Confirmo que el pago ha sido recibido correctamente</span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('reception') }}" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                                Cancelar
                            </a>
                            <button type="submit" id="btnProcesarPago" class="px-6 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors flex items-center text-sm shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Procesar Pago
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="bg-green-50 rounded-2xl p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="font-bold text-green-800">Pago Procesado</h4>
                            <p class="text-green-600 text-sm">El pago ya ha sido procesado exitosamente.</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('reception.confirmacion', $caja->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                            Ver Confirmación
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function procesarPago(event, cajaId) {
            event.preventDefault();
            
            const form = document.getElementById('formProcesarPago');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Mostrar loading
            const submitBtn = document.getElementById('btnProcesarPago');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Procesando...';
            submitBtn.disabled = true;

            fetch(`/reception/procesar-pago/${cajaId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pago procesado exitosamente');
                    window.location.href = data.redirect_url;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar el pago');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
    </script>
@endsection
