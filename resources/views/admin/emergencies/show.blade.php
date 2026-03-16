@extends('layouts.app')

@section('title', 'Detalles de Emergencia')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Emergencia {{ $emergency->code }}</h1>
                <div>
                    <a href="{{ route('admin.emergencies.edit', $emergency) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('admin.emergencies.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Información del Paciente -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Información del Paciente</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nombre:</strong> {{ $emergency->patient->name }}</p>
                                    <p><strong>DNI:</strong> {{ $emergency->patient->dni }}</p>
                                    <p><strong>Teléfono:</strong> {{ $emergency->patient->phone ?? 'No registrado' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Edad:</strong> {{ $emergency->patient->age ?? 'No registrado' }}</p>
                                    <p><strong>Género:</strong> {{ $emergency->patient->gender ?? 'No registrado' }}</p>
                                    <p><strong>Dirección:</strong> {{ $emergency->patient->address ?? 'No registrado' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Médica -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Información Médica</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Estado Actual:</strong> 
                                        <span class="badge badge-{{ $emergency->status_color }}">
                                            {{ ucfirst($emergency->status) }}
                                        </span>
                                    </p>
                                    <p><strong>Personal a Cargo:</strong> {{ $emergency->user->name ?? 'No asignado' }}</p>
                                    <p><strong>Fecha Ingreso:</strong> {{ $emergency->admission_date ? $emergency->admission_date->format('d/m/Y H:i') : '-' }}</p>
                                    @if($emergency->discharge_date)
                                    <p><strong>Fecha Alta:</strong> {{ $emergency->discharge_date->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Destino:</strong> {{ ucfirst($emergency->destination ?? 'No definido') }}</p>
                                    <p><strong>Costo:</strong> ${{ number_format($emergency->cost, 2) }}</p>
                                    <p><strong>Estado de Pago:</strong> 
                                        @if($emergency->paid)
                                            <span class="badge badge-success">Pagado</span>
                                        @else
                                            <span class="badge badge-danger">Pendiente</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles Clínicos -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Detalles Clínicos</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h6>Síntomas</h6>
                                    <p>{{ $emergency->symptoms }}</p>
                                </div>
                                
                                @if($emergency->initial_assessment)
                                <div class="col-12 mb-3">
                                    <h6>Valoración Inicial</h6>
                                    <p>{{ $emergency->initial_assessment }}</p>
                                </div>
                                @endif
                                
                                @if($emergency->vital_signs)
                                <div class="col-12 mb-3">
                                    <h6>Signos Vitales</h6>
                                    <p>{{ $emergency->vital_signs }}</p>
                                </div>
                                @endif
                                
                                @if($emergency->treatment)
                                <div class="col-12 mb-3">
                                    <h6>Tratamiento Aplicado</h6>
                                    <p>{{ $emergency->treatment }}</p>
                                </div>
                                @endif
                                
                                @if($emergency->observations)
                                <div class="col-12 mb-3">
                                    <h6>Observaciones</h6>
                                    <p>{{ $emergency->observations }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Acciones Rápidas -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Acciones Rápidas</h5>
                        </div>
                        <div class="card-body">
                            @if(!$emergency->paid)
                            <form action="{{ route('admin.emergencies.mark-paid', $emergency) }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block" 
                                        onclick="return confirm('¿Marcar como pagado?')">
                                    <i class="fas fa-dollar-sign"></i> Marcar como Pagado
                                </button>
                            </form>
                            @endif
                            
                            <div class="btn-group-vertical btn-block">
                                <a href="{{ route('admin.emergencies.edit', $emergency) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar Información
                                </a>
                                
                                @if($emergency->status !== 'alta')
                                <button type="button" class="btn btn-info" onclick="updateStatus('alta')">
                                    <i class="fas fa-check"></i> Dar de Alta
                                </button>
                                @endif
                                
                                <form action="{{ route('admin.emergencies.destroy', $emergency) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block" 
                                            onclick="return confirm('¿Eliminar esta emergencia?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline de Estados -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <small class="text-muted">{{ $emergency->created_at->format('d/m/Y H:i') }}</small>
                                    <p class="mb-0">Emergencia creada</p>
                                </div>
                                
                                @if($emergency->admission_date)
                                <div class="timeline-item">
                                    <small class="text-muted">{{ $emergency->admission_date->format('d/m/Y H:i') }}</small>
                                    <p class="mb-0">Paciente admitido</p>
                                </div>
                                @endif
                                
                                @if($emergency->discharge_date)
                                <div class="timeline-item">
                                    <small class="text-muted">{{ $emergency->discharge_date->format('d/m/Y H:i') }}</small>
                                    <p class="mb-0">Paciente dado de alta</p>
                                </div>
                                @endif
                                
                                <div class="timeline-item">
                                    <small class="text-muted">{{ $emergency->updated_at->format('d/m/Y H:i') }}</small>
                                    <p class="mb-0">Última actualización</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(newStatus) {
    if(confirm('¿Cambiar estado a "' + newStatus + '"?')) {
        // Aquí podrías implementar una llamada AJAX para actualizar el estado
        window.location.href = '{{ route("admin.emergencies.edit", $emergency) }}';
    }
}
</script>

<style>
.timeline-item {
    border-left: 3px solid #007bff;
    padding-left: 15px;
    margin-bottom: 15px;
    position: relative;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -8px;
    top: 5px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: #007bff;
}
</style>
@endsection
