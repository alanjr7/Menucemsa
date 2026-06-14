@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Correcciones de Cuenta</h1>
            <p class="text-sm text-gray-500">{{ $paciente->nombre }} — CI: {{ $paciente->ci ?? $paciente->temp_code }}</p>
        </div>
        <a href="{{ route('admin.ajustes-pacientes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Patient Info Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $paciente->nombre }}</h3>
                    <p class="text-sm text-gray-500">CI: {{ $paciente->ci ?? $paciente->temp_code }} | {{ $paciente->seguro->nombre_empresa ?? 'Particular' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Total de Cuentas</p>
                <p class="text-2xl font-bold text-gray-800">{{ $cuentas->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Accounts List -->
    @forelse($cuentas as $cuenta)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <!-- Account Header -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Cuenta #{{ $cuenta->id }}</h3>
                        <p class="text-sm text-gray-500">Creada: {{ $cuenta->created_at->format('d/m/Y H:i') }} · {{ $cuenta->tipo_atencion_label }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($cuenta->estado === 'pagado') bg-green-100 text-green-800 border border-green-200
                        @elseif($cuenta->estado === 'parcial') bg-yellow-100 text-yellow-800 border border-yellow-200
                        @else bg-red-100 text-red-800 border border-red-200 @endif">
                        {{ $cuenta->estado_label }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <!-- Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="text-xl font-bold text-gray-800">Bs. {{ number_format($cuenta->total_calculado, 2) }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500">Pagado</p>
                        <p class="text-xl font-bold text-green-600">Bs. {{ number_format($cuenta->pagos->sum('monto'), 2) }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500">Pendiente</p>
                        <p class="text-xl font-bold text-red-600">Bs. {{ number_format($cuenta->saldo_pendiente, 2) }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500">Cargos activos</p>
                        <p class="text-xl font-bold text-blue-600">{{ $cuenta->detalles->whereNull('deshabilitado_en')->count() }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Unit.</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($cuenta->detalles as $detalle)
                                @php $off = $detalle->deshabilitado_en !== null; @endphp
                                <tr class="{{ $off ? 'bg-gray-100 text-gray-400' : 'hover:bg-gray-50' }}">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm {{ $off ? 'text-gray-400' : 'text-gray-500' }}">
                                        {{ $detalle->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm {{ $off ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                        {{ $detalle->descripcion }}
                                        @if($off)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-200 text-gray-600 no-underline">Deshabilitado</span>
                                            @if($detalle->motivo_deshabilitacion)
                                                <span class="block text-[11px] text-gray-400 mt-0.5 no-underline">Motivo: {{ $detalle->motivo_deshabilitacion }}</span>
                                            @endif
                                        @elseif($detalle->liquidado_en)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-700">Pagado</span>
                                        @else
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-700">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm {{ $off ? 'text-gray-400' : 'text-gray-500' }}">{{ rtrim(rtrim(number_format($detalle->cantidad, 2), '0'), '.') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm {{ $off ? 'text-gray-400' : 'text-gray-500' }}">Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium {{ $off ? 'text-gray-400' : 'text-gray-900' }}">Bs. {{ number_format($detalle->subtotal, 2) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                        @if($off)
                                            <form action="{{ route('admin.ajustes-pacientes.detalles.restaurar', $detalle->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-green-200 shadow-sm text-xs font-medium rounded-lg text-green-700 bg-green-50 hover:bg-green-100 transition-all">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                    Restaurar
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.ajustes-pacientes.detalles.deshabilitar', $detalle->id) }}" method="POST"
                                                  onsubmit="this.motivo.value = prompt('Motivo (opcional):') || ''; return true;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="motivo">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-amber-200 shadow-sm text-xs font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition-all">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                    Deshabilitar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">No hay cargos en esta cuenta.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Add Charge Form -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Agregar cargo</h4>
                    <form action="{{ route('admin.ajustes-pacientes.cargos.store', $cuenta->id) }}" method="POST"
                          class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        @csrf
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Fecha</label>
                            <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" required
                                   class="block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Concepto</label>
                            <input type="text" name="descripcion" required maxlength="255" placeholder="Ej. Servicio adicional"
                                   class="block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cantidad</label>
                            <input type="number" name="cantidad" value="1" step="0.01" min="0.01" required
                                   class="block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Monto (Bs.)</label>
                            <input type="number" name="monto" step="0.01" min="0" required placeholder="0.00"
                                   class="block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition-colors shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-600 mb-2">No hay cuentas</h3>
            <p class="text-gray-400">Este paciente no tiene cuentas registradas; no es posible agregar cargos.</p>
        </div>
    @endforelse

</div>
@endsection
