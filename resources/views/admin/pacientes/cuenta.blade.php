@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

        <!-- Page Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Cuenta del Paciente</h1>
                <p class="text-sm text-gray-500">{{ $paciente->nombre }} - CI: {{ $paciente->ci }}</p>
            </div>
            <div>
                <a href="{{ route('admin.pacientes.gestionar') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Gestión
                </a>
            </div>
        </div>

        <!-- Patient Info Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $paciente->nombre }}</h3>
                        <p class="text-sm text-gray-500">CI: {{ $paciente->ci }} | {{ $paciente->seguro->nombre_empresa ?? 'Particular' }}</p>
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
                            <h3 class="text-lg font-semibold text-gray-800">
                                Cuenta #{{ $cuenta->id }}
                                @if($cuenta->referencia)
                                    <span class="text-sm font-normal text-gray-500">
                                        - {{ $cuenta->referencia_type === 'App\Models\Emergency' ? 'Emergencia' : 'Consulta' }}
                                    </span>
                                @endif
                            </h3>
                            <p class="text-sm text-gray-500">
                                Creada: {{ $cuenta->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Estado</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($cuenta->estado === 'pagada') bg-green-100 text-green-800 border border-green-200
                                @elseif($cuenta->estado === 'parcial') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @else bg-red-100 text-red-800 border border-red-200 @endif">
                                {{ ucfirst($cuenta->estado) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Account Details -->
                <div class="p-6">
                    <!-- Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500">Total</p>
                            <p class="text-xl font-bold text-gray-800">Bs. {{ number_format($cuenta->total, 2) }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500">Pagado</p>
                            <p class="text-xl font-bold text-green-600">Bs. {{ number_format($cuenta->pagos->sum('monto'), 2) }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500">Pendiente</p>
                            <p class="text-xl font-bold text-red-600">Bs. {{ number_format($cuenta->total - $cuenta->pagos->sum('monto'), 2) }}</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500">Items</p>
                            <p class="text-xl font-bold text-blue-600">{{ $cuenta->detalles->count() }}</p>
                        </div>
                    </div>

                    <!-- Items Table -->
                    @if($cuenta->detalles->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($cuenta->detalles as $detalle)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $detalle->descripcion }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($detalle->tipo_item === 'medicamento') bg-purple-100 text-purple-800
                                                    @elseif($detalle->tipo_item === 'procedimiento') bg-blue-100 text-blue-800
                                                    @elseif($detalle->tipo_item === 'equipo_medico') bg-green-100 text-green-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $detalle->tipo_item_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $detalle->cantidad }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Bs. {{ number_format($detalle->precio_unitario, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                Bs. {{ number_format($detalle->subtotal, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form action="{{ route('admin.cuentas.eliminar-item', [$cuenta->id, $detalle->id]) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este item? Esta acción no se puede deshacer.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-200 shadow-sm text-xs font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-500">No hay items en esta cuenta</p>
                        </div>
                    @endif

                    <!-- Payments History -->
                    @if($cuenta->pagos->count() > 0)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Historial de Pagos</h4>
                            <div class="space-y-2">
                                @foreach($cuenta->pagos as $pago)
                                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">Pago #{{ $pago->id }}</p>
                                            <p class="text-xs text-gray-500">{{ $pago->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-green-600">Bs. {{ number_format($pago->monto, 2) }}</p>
                                            <p class="text-xs text-gray-500">{{ $pago->metodo_pago }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-600 mb-2">No hay cuentas</h3>
                <p class="text-gray-400">Este paciente no tiene cuentas registradas en el sistema.</p>
            </div>
        @endforelse

    </div>
@endsection
