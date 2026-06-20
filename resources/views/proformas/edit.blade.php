@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('proformas.show', $proforma) }}" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Editar Proforma <span class="text-emerald-700">{{ $proforma->numero }}</span></h1>
            <p class="text-sm text-slate-500">Modifica los datos y guarda los cambios.</p>
        </div>
    </div>

    @include('proformas._form', [
        'action'      => route('proformas.update', $proforma),
        'method'      => 'PUT',
        'submitLabel' => 'Guardar cambios',
    ])
</div>
@endsection
