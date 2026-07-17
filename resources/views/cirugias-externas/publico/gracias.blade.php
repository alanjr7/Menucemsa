@extends('layouts.publico')
@section('title', 'Reserva registrada')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 text-center">
        <div class="mx-auto w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-xl font-semibold">¡Registro exitoso!</h1>
        <p class="text-sm text-gray-500 mt-1">El pago será verificado por administración. Recibirá la confirmación una vez validado.</p>

        <div class="mt-5 inline-flex items-center gap-2 rounded-lg bg-blue-50 border border-blue-200 px-4 py-2">
            <span class="text-xs text-gray-500">Código de reserva</span>
            <span class="font-mono font-semibold text-cemsa">{{ $cirugia->codigo }}</span>
        </div>

        <div class="mt-6 rounded-xl border border-gray-200 divide-y text-sm text-left">
            <div class="flex justify-between px-4 py-2"><span class="text-gray-500">Cirujano</span><span>{{ $cirugia->cirujano_nombre }}</span></div>
            <div class="flex justify-between px-4 py-2"><span class="text-gray-500">Paciente</span><span>{{ $cirugia->paciente_nombre }}</span></div>
            <div class="flex justify-between px-4 py-2"><span class="text-gray-500">Cirugía</span><span>{{ $cirugia->tipo->nombre }}</span></div>
            <div class="flex justify-between px-4 py-2"><span class="text-gray-500">Quirófano</span><span>{{ $cirugia->quirofano->nombre }}</span></div>
            <div class="flex justify-between px-4 py-2"><span class="text-gray-500">Fecha y hora</span><span>{{ $cirugia->fecha->format('d/m/Y') }} · {{ \Illuminate\Support\Str::of($cirugia->hora_inicio)->substr(0,5) }} – {{ \Illuminate\Support\Str::of($cirugia->hora_fin)->substr(0,5) }}</span></div>
            <div class="flex justify-between px-4 py-3 bg-gray-50 font-semibold"><span>Total pagado</span><span class="text-cemsa">Bs {{ $cirugia->precio_final }}</span></div>
        </div>

        <a href="{{ route('cirugias-externas.public.create') }}" class="mt-6 inline-block rounded-lg bg-cemsa px-5 py-2 text-sm font-medium text-white hover:opacity-90">Registrar otra cirugía</a>
    </div>
</div>
@endsection
