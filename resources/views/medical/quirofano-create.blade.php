@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-slate-50 min-h-screen font-sans">
    <div class="max-w-3xl mx-auto bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Nueva cita de quirófano</h1>
            <p class="text-sm text-slate-500">Completa los datos para programar la cirugía.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc ml-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('quirofano.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Paciente</label>
                <input name="patient_name" value="{{ old('patient_name') }}" class="w-full rounded-xl border-slate-300" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Procedimiento</label>
                <input name="procedure_name" value="{{ old('procedure_name') }}" class="w-full rounded-xl border-slate-300" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Cirujano</label>
                <input name="surgeon_name" value="{{ old('surgeon_name') }}" class="w-full rounded-xl border-slate-300" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha y hora</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full rounded-xl border-slate-300" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Sala</label>
                    <input name="operating_room" value="{{ old('operating_room') }}" class="w-full rounded-xl border-slate-300" placeholder="QX-01" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Notas</label>
                <textarea name="notes" rows="3" class="w-full rounded-xl border-slate-300">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('quirofano.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">Guardar cita</button>
            </div>
        </form>
    </div>
</div>
@endsection
