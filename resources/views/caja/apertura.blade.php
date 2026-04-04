@extends('layouts.app')

@section('content')
<div class="p-8 bg-[#f8fafc] min-h-screen font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Apertura de Caja</h1>
                <p class="text-gray-500 text-sm">Inicio de sesión de caja operativa</p>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="max-w-xl mx-auto">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Bienvenido a Caja</h3>
                        <p class="text-gray-600 mt-2">Para comenzar a operar, debe abrir una nueva sesión de caja.</p>
                    </div>

                        <form id="formApertura" class="space-y-6">
                            @csrf

                            <div>
                                <label for="monto_inicial" class="block text-sm font-medium text-gray-700 mb-2">
                                    Monto Inicial (S/)
                                </label>
                                <input type="number" 
                                       id="monto_inicial" 
                                       name="monto_inicial" 
                                       step="0.01" 
                                       min="0"
                                       required
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg"
                                       placeholder="0.00">
                                <p class="text-sm text-gray-500 mt-1">Ingrese el monto con el que inicia su caja</p>
                            </div>

                            <div>
                                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                    Observaciones (opcional)
                                </label>
                                <textarea id="observaciones" 
                                          name="observaciones" 
                                          rows="3"
                                          maxlength="500"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Notas adicionales sobre la apertura..."></textarea>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mt-0.5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800">Importante</h4>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            Una vez abierta la caja, quedará registrado todos sus movimientos. 
                                            Asegúrese de contar correctamente el monto inicial.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-center space-x-4">
                                <button type="submit" 
                                        id="btnAbrirCaja"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Abrir Caja
                                </button>
                            </div>
                        </form>

                        <div id="mensajeError" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-red-700" id="textoError"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
        document.getElementById('formApertura').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btnAbrir = document.getElementById('btnAbrirCaja');
            const mensajeError = document.getElementById('mensajeError');
            const textoError = document.getElementById('textoError');
            
            // Ocultar error previo
            mensajeError.classList.add('hidden');
            
            // Deshabilitar botón
            btnAbrir.disabled = true;
            btnAbrir.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Abriendo...';
            
            try {
                const response = await fetch('{{ route("caja.operativa.abrir") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        monto_inicial: document.getElementById('monto_inicial').value,
                        observaciones: document.getElementById('observaciones').value
                    })
                });
                
                // Verificar si la respuesta es exitosa antes de parsear JSON
                if (!response.ok) {
                    if (response.status === 403) {
                        throw new Error('No tienes permisos para abrir caja. Verifica tu rol de usuario.');
                    } else if (response.status === 419) {
                        throw new Error('Sesión expirada. Recarga la página.');
                    } else {
                        throw new Error('Error del servidor: ' + response.status);
                    }
                }
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = '{{ route("caja.operativa.index") }}';
                } else {
                    textoError.textContent = data.message;
                    mensajeError.classList.remove('hidden');
                    btnAbrir.disabled = false;
                    btnAbrir.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Abrir Caja';
                }
            } catch (error) {
                textoError.textContent = error.message || 'Error de conexión. Intente nuevamente.';
                mensajeError.classList.remove('hidden');
                btnAbrir.disabled = false;
                btnAbrir.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Abrir Caja';
            }
        });
    </script>
@endpush
@endsection
