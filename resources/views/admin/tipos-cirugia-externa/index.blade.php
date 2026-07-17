@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Precios de Cirugías Externas</h1>
            <p class="text-sm text-gray-500">Tarifa de uso de quirófano para cirujanos externos. Independiente del tarifario clínico interno.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Formularios (asociados por atributo form= para no romper la tabla) --}}
        @foreach($tipos as $tipo)
            <form id="tf{{ $tipo->id }}" method="POST" action="{{ route('admin.tipos-cirugia-externa.update', $tipo) }}" class="hidden">
                @csrf
                @method('PUT')
            </form>
        @endforeach

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Clave</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Precio (Bs)</th>
                        <th class="px-4 py-3 text-left">Duración (min)</th>
                        <th class="px-4 py-3 text-center">Activo</th>
                        <th class="px-4 py-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tipos as $tipo)
                        <tr class="border-t">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $tipo->clave }}</td>
                            <td class="px-4 py-3">
                                <input form="tf{{ $tipo->id }}" type="text" name="nombre" value="{{ $tipo->nombre }}"
                                       class="w-48 rounded-lg border-gray-300 text-sm">
                            </td>
                            <td class="px-4 py-3">
                                <input form="tf{{ $tipo->id }}" type="text" inputmode="decimal" data-decimal name="precio" value="{{ $tipo->precio }}"
                                       class="w-28 rounded-lg border-gray-300 text-sm">
                            </td>
                            <td class="px-4 py-3">
                                <input form="tf{{ $tipo->id }}" type="number" min="1" max="1440" name="duracion_minutos" value="{{ $tipo->duracion_minutos }}"
                                       class="w-24 rounded-lg border-gray-300 text-sm">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input form="tf{{ $tipo->id }}" type="checkbox" name="activo" value="1" @checked($tipo->activo) class="rounded border-gray-300 text-blue-600">
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button form="tf{{ $tipo->id }}" type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Guardar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-3 text-xs text-gray-400">Nota: cambiar la duración afecta el cálculo de horario/solape de futuras reservas, no las ya registradas.</p>
    </div>
@endsection
