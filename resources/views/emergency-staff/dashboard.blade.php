@extends('layouts.app')

@section('title', 'Panel de Emergencias')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Panel de Emergencias</h1>
                <div>
                    <a href="{{ route('emergency-staff.pending') }}" class="btn btn-warning">
                        <i class="fas fa-exclamation-triangle"></i> Emergencias Pendientes ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('emergency-staff.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Emergencia
                    </a>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pendientes de Asignar</h5>
                            <h3>{{ $pendingCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Mis Activas</h5>
                            <h3>{{ $myActiveCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Hoy</h5>
                            <h3>{{ $emergencies->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mis Emergencias -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mis Emergencias Asignadas</h5>
                </div>
                <div class="card-body">
                    @if($emergencies->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Paciente</th>
                                    <th>Estado</th>
                                    <th>Síntomas</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emergencies as $emergency)
                                <tr>
                                    <td><strong>{{ $emergency->code }}</strong></td>
                                    <td>{{ $emergency->patient->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $emergency->status_color }}">
                                            {{ ucfirst($emergency->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                            {{ Str::limit($emergency->symptoms, 50) }}
                                        </span>
                                    </td>
                                    <td>{{ $emergency->created_at->format('d/m H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('emergency-staff.show', $emergency) }}" 
                                               class="btn btn-sm btn-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('emergency-staff.edit', $emergency) }}" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    title="Actualizar Estado"
                                                    onclick="showStatusModal({{ $emergency->id }}, '{{ $emergency->status }}')">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $emergencies->links() }}
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-ambulance fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No tienes emergencias asignadas</h5>
                        <p class="text-muted">Revisa las emergencias pendientes para asignarte una.</p>
                        <a href="{{ route('emergency-staff.pending') }}" class="btn btn-primary">
                            Ver Emergencias Pendientes
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar estado -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Estado</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="statusForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Nuevo Estado</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="recibido">Recibido</option>
                            <option value="en_evaluacion">En Evaluación</option>
                            <option value="estabilizado">Estabilizado</option>
                            <option value="uti">UTI</option>
                            <option value="cirugia">Cirugía</option>
                            <option value="alta">Alta</option>
                            <option value="fallecido">Fallecido</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showStatusModal(emergencyId, currentStatus) {
    document.getElementById('statusForm').action = `/emergency-staff/${emergencyId}`;
    document.getElementById('status').value = currentStatus;
    $('#statusModal').modal('show');
}

// Auto-refresh cada 30 segundos para emergencias críticas
setTimeout(function() {
    location.reload();
}, 30000);
</script>
@endsection
