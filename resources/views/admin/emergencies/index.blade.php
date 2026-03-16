@extends('layouts.app')

@section('title', 'Gestión de Emergencias')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestión de Emergencias</h1>
                <a href="{{ route('admin.emergencies.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Emergencia
                </a>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total</h5>
                            <h3>{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Activas</h5>
                            <h3>{{ $stats['active'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">UTI</h5>
                            <h3>{{ $stats['uti'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-purple text-white">
                        <div class="card-body">
                            <h5 class="card-title">Cirugía</h5>
                            <h3>{{ $stats['surgery'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Alta</h5>
                            <h3>{{ $stats['discharged'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.emergencies.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Estado</label>
                                <select name="status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="recibido" {{ request('status') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                                    <option value="en_evaluacion" {{ request('status') == 'en_evaluacion' ? 'selected' : '' }}>En Evaluación</option>
                                    <option value="estabilizado" {{ request('status') == 'estabilizado' ? 'selected' : '' }}>Estabilizado</option>
                                    <option value="uti" {{ request('status') == 'uti' ? 'selected' : '' }}>UTI</option>
                                    <option value="cirugia" {{ request('status') == 'cirugia' ? 'selected' : '' }}>Cirugía</option>
                                    <option value="alta" {{ request('status') == 'alta' ? 'selected' : '' }}>Alta</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Fecha</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Pagado</label>
                                <select name="paid" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('paid') == '1' ? 'selected' : '' }}>Pagado</option>
                                    <option value="0" {{ request('paid') == '0' ? 'selected' : '' }}>No Pagado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Filtrar</button>
                                    <a href="{{ route('admin.emergencies.index') }}" class="btn btn-secondary">Limpiar</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de emergencias -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Paciente</th>
                                    <th>Estado</th>
                                    <th>Personal</th>
                                    <th>Costo</th>
                                    <th>Pagado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emergencies as $emergency)
                                <tr>
                                    <td><strong>{{ $emergency->code }}</strong></td>
                                    <td>{{ $emergency->patient->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $emergency->status_color }}">
                                            {{ ucfirst($emergency->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $emergency->user->name ?? '-' }}</td>
                                    <td>${{ number_format($emergency->cost, 2) }}</td>
                                    <td>
                                        @if($emergency->paid)
                                            <span class="badge badge-success">Pagado</span>
                                        @else
                                            <span class="badge badge-danger">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>{{ $emergency->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.emergencies.show', $emergency) }}" 
                                               class="btn btn-sm btn-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.emergencies.edit', $emergency) }}" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!$emergency->paid)
                                                <form action="{{ route('admin.emergencies.mark-paid', $emergency) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            title="Marcar como pagado"
                                                            onclick="return confirm('¿Marcar como pagado?')">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.emergencies.destroy', $emergency) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Eliminar esta emergencia?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No hay emergencias registradas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $emergencies->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
