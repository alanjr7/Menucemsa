@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Cunas — Neonatología</h1>
            <p class="text-sm text-gray-500">Gestión de cunas del área</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.neonato.dashboard') }}"
                class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Volver</a>
            <a href="{{ route('admin.neonato.cunas.create') }}"
                class="px-4 py-2 bg-pink-600 text-white rounded-xl text-sm hover:bg-pink-700 shadow-sm">+ Nueva cuna</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-100 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm">Total: {{ $cunas->total() }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Precio / hora</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Usos</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($cunas as $cuna)
                        <tr class="hover:bg-gray-50/30">
                            <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $cuna->nombre }}</td>
                            <td class="px-6 py-3 text-sm font-mono text-gray-600">{{ $cuna->codigo }}</td>
                            <td class="px-6 py-3 text-sm text-right text-gray-700">Bs. {{ number_format($cuna->precio_por_hora, 2) }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $cuna->activa ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $cuna->activa ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center text-sm text-gray-500">{{ $cuna->usos_count }}</td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.neonato.cunas.edit', $cuna->id) }}"
                                        class="px-3 py-1 text-xs border rounded-lg text-gray-600 hover:bg-gray-50">Editar</a>
                                    <form method="POST" action="{{ route('admin.neonato.cunas.destroy', $cuna->id) }}"
                                        onsubmit="return confirm('¿Eliminar esta cuna?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1 text-xs border border-red-200 rounded-lg text-red-600 hover:bg-red-50">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                Sin cunas registradas. <a href="{{ route('admin.neonato.cunas.create') }}" class="text-pink-600 hover:underline">Crear una</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cunas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $cunas->links() }}</div>
        @endif
    </div>
</div>
@endsection
