@extends('layouts.app')

@section('content')
<div class="w-full p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-[28px] font-black text-slate-800 tracking-tight">Dashboard del Médico</h1>
            <p class="text-slate-500 text-[15px] font-medium">Gestión de consultas y pacientes</p>
        </div>
        <div class="flex gap-3">
            <a href="/dashboard" class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm hover:bg-slate-50 transition-all">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-bold mb-2">Pacientes Pendientes</p>
                    <p class="text-[#f39c12] text-3xl font-black tracking-tighter">{{ $stats['pendientes'] }}</p>
                    <p class="text-slate-400 text-[11px] font-bold mt-1">Por atender</p>
                </div>
                <div class="p-3 bg-amber-100 rounded-xl">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-bold mb-2">Atendidos Hoy</p>
                    <p class="text-[#00a65a] text-3xl font-black tracking-tighter">{{ $stats['atendidos_hoy'] }}</p>
                    <p class="text-slate-400 text-[11px] font-bold mt-1">Consultas completadas</p>
                </div>
                <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-bold mb-2">Total Pacientes</p>
                    <p class="text-[#1c7ed6] text-3xl font-black tracking-tighter">{{ $stats['total_pacientes'] }}</p>
                    <p class="text-slate-400 text-[11px] font-bold mt-1">En historial</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pacientes Asignados -->
    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <h3 class="font-bold text-slate-700 text-lg">Pacientes Asignados - Orden de Llegada</h3>
            <p class="text-slate-500 text-sm mt-1">Pacientes pagados pendientes de atención</p>
        </div>
        
        <div class="overflow-x-auto">
            @forelse($pacientesAsignados as $index => $consulta)
                <div class="p-6 border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <!-- Número de orden -->
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full">
                                <span class="text-blue-600 font-bold text-lg">{{ $index + 1 }}</span>
                            </div>
                            
                            <!-- Información del paciente -->
                            <div>
                                <h4 class="font-semibold text-slate-800 text-lg">{{ $consulta->paciente->nombre }}</h4>
                                <p class="text-slate-500 text-sm">CI: {{ $consulta->paciente->ci }}</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-xs text-slate-400">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        {{ $consulta->fecha }} {{ $consulta->hora }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                        {{ $consulta->motivo }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botón de acción -->
                        <div class="flex items-center gap-3">
                            <span class="bg-[#e6fcf5] text-[#0ca678] px-3 py-1 rounded-full text-[11px] font-bold flex items-center gap-1 border border-[#c3fae8]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3"/></svg>
                                Pagado
                            </span>
                            
                            <button onclick="atenderPaciente('{{ $consulta->nro }}')" 
                                    class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-lg shadow-blue-100 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/>
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/>
                                </svg>
                                Atender
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="text-lg font-medium text-slate-600 mb-2">No hay pacientes pendientes</h3>
                    <p class="text-slate-400">Todos los pacientes asignados han sido atendidos</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function atenderPaciente(consultaNro) {
    if (confirm('¿Está seguro de comenzar a atender a este paciente?')) {
        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Procesando...';
        
        fetch('/medico/atender-paciente', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                consulta_nro: consultaNro
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Paciente en atención. Redirigiendo...');
                window.location.href = data.redirect_url || '/medico/dashboard';
            } else {
                alert(data.message || 'Error al atender paciente');
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/></svg> Atender';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al atender paciente: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/></svg> Atender';
        });
    }
}
</script>
@endsection
