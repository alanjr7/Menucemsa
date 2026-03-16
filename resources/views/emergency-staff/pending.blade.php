@extends('layouts.app')

@section('title', 'Emergencias Pendientes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Emergencias Pendientes de Asignar</h1>
                <a href="{{ route('emergency-staff.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
            </div>

            @if($emergencies->count() > 0)
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Paciente</th>
                                    <th>Síntomas</th>
                                    <th>Fecha/Hora</th>
                                    <th>Costo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emergencies as $emergency)
                                <tr class="{{ $emergency->created_at->diffInMinutes(now()) < 30 ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>{{ $emergency->code }}</strong>
                                        @if($emergency->created_at->diffInMinutes(now()) < 30)
                                        <span class="badge badge-danger ml-2">Crítico</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $emergency->patient->name }}</strong><br>
                                        <small class="text-muted">DNI: {{ $emergency->patient->dni }}</small>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                            {{ $emergency->symptoms }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $emergency->created_at->format('d/m/Y') }}</strong><br>
                                        <small class="text-muted">{{ $emergency->created_at->format('H:i') }}</small>
                                        <br>
                                        <small class="text-info">
                                            Hace {{ $emergency->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="text-success font-weight-bold">
                                            ${{ number_format($emergency->cost, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm">
                                            <form action="{{ route('emergency-staff.assign-to-me', $emergency) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success" 
                                                        onclick="return confirm('¿Asignarte esta emergencia?')">
                                                    <i class="fas fa-user-plus"></i> Asignarme
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-info" 
                                                    onclick="showDetails({{ $emergency->id }})">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4 class="text-success">¡Todo en orden!</h4>
                    <p class="text-muted">No hay emergencias pendientes de asignar en este momento.</p>
                    <a href="{{ route('emergency-staff.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Ir al Panel Principal
                    </a>
                </div>
            </div>
            @endif

            <!-- Alerta de emergencias críticas -->
            @if($emergencies->contains(function($emergency) { return $emergency->created_at->diffInMinutes(now()) < 30; }))
            <div class="alert alert-danger mt-3">
                <h5><i class="fas fa-exclamation-triangle"></i> ¡Alerta!</h5>
                <p>Hay emergencias críticas (menos de 30 minutos) que requieren atención inmediata.</p>
            </div>
            @endif
</div>

<!-- Modal de detalles -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de Emergencia</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Cargando... -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="assignFromModal">
                    <i class="fas fa-user-plus"></i> Asignarme
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentEmergencyId = null;

function showDetails(emergencyId) {
    currentEmergencyId = emergencyId;
    
    // Aquí podrías hacer una llamada AJAX para obtener los detalles
    // Por ahora mostraremos información básica
    const emergency = @json($emergencies->toArray());
    const selectedEmergency = emergency.find(e => e.id === emergencyId);
    
    if (selectedEmergency) {
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Información General</h6>
                    <p><strong>Código:</strong> ${selectedEmergency.code}</p>
                    <p><strong>Estado:</strong> <span class="badge badge-warning">${selectedEmergency.status}</span></p>
                    <p><strong>Fecha:</strong> ${selectedEmergency.created_at}</p>
                    <p><strong>Costo:</strong> $${parseFloat(selectedEmergency.cost).toFixed(2)}</p>
                </div>
                <div class="col-md-6">
                    <h6>Paciente</h6>
                    <p><strong>Nombre:</strong> ${selectedEmergency.patient.name}</p>
                    <p><strong>DNI:</strong> ${selectedEmergency.patient.dni}</p>
                    <p><strong>Teléfono:</strong> ${selectedEmergency.patient.phone || 'No registrado'}</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Síntomas</h6>
                    <p>${selectedEmergency.symptoms}</p>
                </div>
            </div>
        `;
        
        document.getElementById('detailsContent').innerHTML = html;
        document.getElementById('assignFromModal').onclick = function() {
            window.location.href = `/emergency-staff/${emergencyId}/assign-to-me`;
        };
        
        $('#detailsModal').modal('show');
    }
}

// Auto-refresh cada 15 segundos para emergencias pendientes
setTimeout(function() {
    location.reload();
}, 15000);
</script>
@endsection
