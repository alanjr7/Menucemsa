@extends('layouts.app')

@section('content')
<div class="w-full p-4 sm:p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Precios y Duración de Cirugías</h1>
            <p class="text-sm text-gray-500">Configurar el precio y la duración por defecto de cada tipo de cirugía</p>
        </div>
        <a href="{{ route('quirofano.index') }}" class="flex-1 sm:flex-none justify-center flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors whitespace-nowrap">
            <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Cirugías
        </a>
    </div>

    <!-- Aviso -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-blue-800">
            Estos son los valores <strong>por defecto</strong> que se precargan al programar una cirugía.
            El precio se puede seguir ajustando en cada cirugía, y <strong>cambiar el valor por defecto no modifica las cirugías ya programadas o cobradas</strong>.
        </p>
    </div>

    <!-- Lista -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Tipos de Cirugía</h2>
        </div>

        <!-- Tabla (md+) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración por defecto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio por defecto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tipos as $tipo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 capitalize">{{ $tipo->nombre }}</div>
                                @if($tipo->descripcion)
                                    <div class="text-xs text-gray-500">{{ $tipo->descripcion }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tipo->duracion_formateada }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Bs. {{ number_format($tipo->costo_base, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tipo->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $tipo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('tipos-cirugia.edit', $tipo) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tarjetas (móvil) -->
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($tipos as $tipo)
                <div class="p-4">
                    <div class="flex justify-between items-start gap-3 mb-3">
                        <div>
                            <p class="text-base font-bold text-gray-900 capitalize">{{ $tipo->nombre }}</p>
                            @if($tipo->descripcion)
                                <p class="text-xs text-gray-500">{{ $tipo->descripcion }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shrink-0 {{ $tipo->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $tipo->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="flex gap-4 text-sm text-gray-500 mb-3">
                        <span>Duración: <span class="font-medium text-gray-700">{{ $tipo->duracion_formateada }}</span></span>
                        <span>Precio: <span class="font-medium text-gray-700">Bs. {{ number_format($tipo->costo_base, 2) }}</span></span>
                    </div>
                    <div class="pt-3 border-t border-gray-100">
                        <a href="{{ route('tipos-cirugia.edit', $tipo) }}" class="block text-center text-indigo-600 bg-indigo-50 hover:bg-indigo-100 py-2 rounded-lg text-sm font-medium transition">
                            Editar
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400">No hay tipos de cirugía registrados</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
