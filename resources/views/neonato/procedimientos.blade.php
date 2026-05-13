@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Procedimientos — Neonatología</h1>
            <p class="text-sm text-gray-500">Procedimientos disponibles para el área · Solo lectura</p>
        </div>
        @if(in_array(auth()->user()->role, ['admin','administrador']))
            <a href="{{ route('admin.procedimientos.create') }}"
                class="px-4 py-2 bg-pink-600 text-white rounded-xl text-sm hover:bg-pink-700 shadow-sm">
                + Nuevo procedimiento
            </a>
        @else
            <a href="{{ route('neonato.index') }}"
                class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">
                ← Volver
            </a>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm">{{ $procedimientos->total() }} procedimiento(s)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Descripción</th>
                        @if(in_array(auth()->user()->role, ['admin','administrador']))
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Precio</th>
                        @endif
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estado</th>
                        @if(in_array(auth()->user()->role, ['admin','administrador']))
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($procedimientos as $proc)
                        <tr class="hover:bg-gray-50/30">
                            <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $proc->nombre }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500">{{ $proc->descripcion ?? '—' }}</td>
                            @if(in_array(auth()->user()->role, ['admin','administrador']))
                            <td class="px-6 py-3 text-sm text-right font-semibold text-gray-800">
                                Bs. {{ number_format($proc->precio, 2) }}
                            </td>
                            @endif
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $proc->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $proc->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            @if(in_array(auth()->user()->role, ['admin','administrador']))
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.procedimientos.edit', $proc->id) }}"
                                        class="px-3 py-1 text-xs border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">
                                        Editar
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                Sin procedimientos registrados para neonatología.
                                @if(in_array(auth()->user()->role, ['admin','administrador']))
                                    <a href="{{ route('admin.procedimientos.create') }}" class="text-pink-600 hover:underline ml-1">Crear uno</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($procedimientos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $procedimientos->links() }}</div>
        @endif
    </div>
</div>
@endsection
