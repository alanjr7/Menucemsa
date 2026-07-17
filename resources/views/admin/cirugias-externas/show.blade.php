@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen" x-data="{ rechazando: false }">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.cirugias-externas.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Volver a la lista</a>
                <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $cirugia->codigo }}</h1>
            </div>
            @php
                $badge = match($cirugia->estado) {
                    'pagado' => 'bg-green-100 text-green-800',
                    'rechazado' => 'bg-red-100 text-red-700',
                    default => 'bg-amber-100 text-amber-800',
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badge }} capitalize">{{ $cirugia->estado }}</span>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Detalle --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-100 p-5">
                    <h2 class="font-semibold text-gray-800 mb-3">Datos de la reserva</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div><dt class="text-gray-500">Cirujano</dt><dd class="font-medium">{{ $cirugia->cirujano_nombre }}</dd></div>
                        <div><dt class="text-gray-500">Teléfono</dt><dd class="font-medium">{{ $cirugia->cirujano_telefono }}</dd></div>
                        <div><dt class="text-gray-500">Correo</dt><dd class="font-medium">{{ $cirugia->cirujano_email }}</dd></div>
                        <div><dt class="text-gray-500">Paciente</dt><dd class="font-medium">{{ $cirugia->paciente_nombre }}</dd></div>
                        <div><dt class="text-gray-500">Tipo de cirugía</dt><dd class="font-medium">{{ $cirugia->tipo->nombre ?? '—' }}</dd></div>
                        @if($cirugia->cirugia_nombre)
                            <div><dt class="text-gray-500">Cirugía específica</dt><dd class="font-medium">{{ $cirugia->cirugia_nombre }}</dd></div>
                        @endif
                        <div><dt class="text-gray-500">Quirófano</dt><dd class="font-medium">{{ $cirugia->quirofano->nombre ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Fecha</dt><dd class="font-medium">{{ $cirugia->fecha->format('d/m/Y') }}</dd></div>
                        <div><dt class="text-gray-500">Horario</dt><dd class="font-medium">{{ \Illuminate\Support\Str::of($cirugia->hora_inicio)->substr(0,5) }} – {{ \Illuminate\Support\Str::of($cirugia->hora_fin)->substr(0,5) }}</dd></div>
                    </dl>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 p-5">
                    <h2 class="font-semibold text-gray-800 mb-3">Cobro</h2>
                    <dl class="text-sm divide-y">
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Precio base</dt><dd>Bs {{ $cirugia->precio_base }}</dd></div>
                        @if($cirugia->es_nocturno)
                            <div class="flex justify-between py-2"><dt class="text-gray-500">Descuento nocturno (10%)</dt><dd class="text-green-600">- Bs {{ $cirugia->descuento }}</dd></div>
                        @endif
                        <div class="flex justify-between py-2 font-semibold"><dt>Total</dt><dd class="text-cemsa">Bs {{ $cirugia->precio_final }}</dd></div>
                    </dl>
                    @if($cirugia->verificado_at)
                        <p class="text-xs text-gray-400 mt-3">Verificado por {{ $cirugia->verificador?->name ?? '—' }} el {{ $cirugia->verificado_at->format('d/m/Y H:i') }}.</p>
                    @endif
                    @if($cirugia->estado === 'rechazado' && $cirugia->motivo_rechazo)
                        <p class="text-sm text-red-700 mt-3"><span class="font-medium">Motivo del rechazo:</span> {{ $cirugia->motivo_rechazo }}</p>
                    @endif
                </div>
            </div>

            {{-- Recibo + acciones --}}
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-100 p-5">
                    <h2 class="font-semibold text-gray-800 mb-3">Recibo de pago</h2>
                    @if($cirugia->recibo_url)
                        <a href="{{ $cirugia->recibo_url }}" target="_blank" rel="noopener">
                            <img src="{{ $cirugia->recibo_url }}" alt="Recibo" class="w-full rounded-lg border border-gray-200 hover:opacity-90">
                        </a>
                        <a href="{{ $cirugia->recibo_url }}" target="_blank" rel="noopener" class="mt-2 inline-block text-sm text-cemsa hover:underline">Abrir en tamaño completo ↗</a>
                    @else
                        <p class="text-sm text-gray-400">Sin recibo adjunto.</p>
                    @endif
                </div>

                @if($cirugia->estado === 'pendiente')
                    <div class="bg-white rounded-xl border border-gray-100 p-5 space-y-3">
                        <h2 class="font-semibold text-gray-800">Acciones</h2>
                        <form method="POST" action="{{ route('admin.cirugias-externas.verificar-pago', $cirugia) }}"
                              onsubmit="return confirm('¿Confirmar que el pago fue verificado?')">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2 text-white text-sm font-medium hover:bg-green-700">Verificar pago</button>
                        </form>

                        <button type="button" @click="rechazando = !rechazando" class="w-full rounded-lg border border-red-300 px-4 py-2 text-red-600 text-sm font-medium hover:bg-red-50">Rechazar</button>

                        <form x-show="rechazando" x-cloak method="POST" action="{{ route('admin.cirugias-externas.rechazar', $cirugia) }}" class="space-y-2">
                            @csrf
                            <textarea name="motivo_rechazo" required rows="3" placeholder="Motivo del rechazo…"
                                      class="w-full rounded-lg border-gray-300 text-sm focus:border-red-400 focus:ring-red-400"></textarea>
                            <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2 text-white text-sm font-medium hover:bg-red-700">Confirmar rechazo</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
