@extends('layouts.app')

@section('content')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestión de Precios de Ingresos') }}
    </h2>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Precios de Ingreso por Recepción
                    </h3>

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.ingreso-precios.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipo de Ingreso
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Precio (Bs.)
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Última Modificación
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($tipos as $tipo => $label)
                                        @php
                                            $precio = $precios->get($tipo);
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $label }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $tipo }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number"
                                                       name="precios[{{ $tipo }}]"
                                                       value="{{ old('precios.' . $tipo, $precio->precio) }}"
                                                       step="0.01"
                                                       min="0"
                                                       class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                       placeholder="0.00">
                                                @error('precios.' . $tipo)
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if ($precio->exists)
                                                    <div>{{ $precio->updated_at->format('d/m/Y H:i') }}</div>
                                                    <div class="text-xs text-gray-400">
                                                        por {{ $precio->user?->name ?? 'Sistema' }}
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">Sin configurar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- SECCIÓN DE BOTONES CORREGIDA -->
                        <div class="mt-6 flex items-center justify-end gap-4">
                            <a href="{{ route('dashboard') }}" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                                Guardar Precios
                            </button>
                        </div>
                        <!-- FIN SECCIÓN DE BOTONES -->
                        
                    </form>
                </div>
            </div>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Información
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Estos precios se aplicarán automáticamente al registrar ingresos en recepción. Si no se configura un precio, el sistema mantendrá el comportamiento actual.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection