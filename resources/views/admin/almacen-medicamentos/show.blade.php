@extends('layouts.app')

@section('title', 'Detalles del Medicamento/Insumo')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detalles del Medicamento/Insumo</h1>
        <div>
            <a href="{{ route('admin.almacen-medicamentos.edit', $almacenMedicamento) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            <button type="button" class="btn btn-primary me-2" onclick="actualizarStock()">
                <i class="fas fa-boxes"></i> Actualizar Stock
            </button>
            <a href="{{ route('admin.almacen-medicamentos.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nombre:</strong></td>
                                    <td>{{ $almacenMedicamento->nombre }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $almacenMedicamento->tipo == 'medicamento' ? 'info' : 'secondary' }}">
                                            {{ $almacenMedicamento->tipo_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Área:</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $almacenMedicamento->area_label }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Unidad de Medida:</strong></td>
                                    <td>{{ ucfirst($almacenMedicamento->unidad_medida) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cantidad Actual:</strong></td>
                                    <td>
                                        @if($almacenMedicamento->cantidad == 0)
                                            <span class="badge bg-danger">0</span>
                                        @elseif($almacenMedicamento->estaBajoStock())
                                            <span class="badge bg-warning">{{ $almacenMedicamento->cantidad }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $almacenMedicamento->cantidad }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Stock Mínimo:</strong></td>
                                    <td>{{ $almacenMedicamento->stock_minimo }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Precio Unitario:</strong></td>
                                    <td>
                                        @if($almacenMedicamento->precio)
                                            ${{ number_format($almacenMedicamento->precio, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Número de Lote:</strong></td>
                                    <td>{{ $almacenMedicamento->lote ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Vencimiento:</strong></td>
                                    <td>
                                        @if($almacenMedicamento->fecha_vencimiento)
                                            {{ $almacenMedicamento->fecha_vencimiento->format('d/m/Y') }}
                                            @if($almacenMedicamento->dias_para_vencer !== null)
                                                @if($almacenMedicamento->dias_para_vencer < 0)
                                                    <br><span class="badge bg-danger">Vencido</span>
                                                @elseif($almacenMedicamento->dias_para_vencer <= 30)
                                                    <br><span class="badge bg-warning">{{ $almacenMedicamento->dias_para_vencer }} días</span>
                                                @else
                                                    <br><span class="badge bg-success">Vigente</span>
                                                @endif
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Stock:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $almacenMedicamento->estado_stock == 'normal' ? 'success' : ($almacenMedicamento->estado_stock == 'bajo' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($almacenMedicamento->estado_stock) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Vencimiento:</strong></td>
                                    <td>
                                        @if($almacenMedicamento->fecha_vencimiento)
                                            @if($almacenMedicamento->estaVencido())
                                                <span class="badge bg-danger">Vencido</span>
                                            @elseif($almacenMedicamento->estaPorVencer())
                                                <span class="badge bg-warning">Por vencer</span>
                                            @else
                                                <span class="badge bg-success">Vigente</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Sin fecha</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($almacenMedicamento->descripcion || $almacenMedicamento->observaciones)
                        <hr>
                        <div class="row">
                            @if($almacenMedicamento->descripcion)
                                <div class="col-md-6">
                                    <h6>Descripción:</h6>
                                    <p>{{ $almacenMedicamento->descripcion }}</p>
                                </div>
                            @endif
                            @if($almacenMedicamento->observaciones)
                                <div class="col-md-6">
                                    <h6>Observaciones:</h6>
                                    <p>{{ $almacenMedicamento->observaciones }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">
                                <strong>Creado:</strong> {{ $almacenMedicamento->created_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-0">
                                <strong>Actualizado:</strong> {{ $almacenMedicamento->updated_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Alertas y Estado</h6>
                </div>
                <div class="card-body">
                    @if($almacenMedicamento->estaVencido())
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>¡MEDICAMENTO VENCIDO!</strong><br>
                                <small>Este medicamento ha vencido y no debería usarse</small>
                            </div>
                        </div>
                    @endif

                    @if($almacenMedicamento->estaPorVencer())
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-clock me-2"></i>
                            <div>
                                <strong>Por vencer en {{ $almacenMedicamento->dias_para_vencer }} días</strong><br>
                                <small>Considere usar o reemplazar pronto</small>
                            </div>
                        </div>
                    @endif

                    @if($almacenMedicamento->estaBajoStock())
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-boxes me-2"></i>
                            <div>
                                <strong>Stock bajo</strong><br>
                                <small>Actual: {{ $almacenMedicamento->cantidad }}, Mínimo: {{ $almacenMedicamento->stock_minimo }}</small>
                            </div>
                        </div>
                    @endif

                    @if($almacenMedicamento->cantidad == 0)
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-times-circle me-2"></i>
                            <div>
                                <strong>¡AGOTADO!</strong><br>
                                <small>No hay unidades disponibles</small>
                            </div>
                        </div>
                    @endif

                    @if(!$almacenMedicamento->estaVencido() && !$almacenMedicamento->estaPorVencer() && !$almacenMedicamento->estaBajoStock() && $almacenMedicamento->cantidad > 0)
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>
                                <strong>Todo en orden</strong><br>
                                <small>Stock adecuado y vigente</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" onclick="actualizarStock()">
                            <i class="fas fa-boxes"></i> Actualizar Stock
                        </button>
                        <a href="{{ route('admin.almacen-medicamentos.edit', $almacenMedicamento) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Información
                        </a>
                        @if($almacenMedicamento->area)
                            <a href="{{ route('admin.almacen-medicamentos.por-area', $almacenMedicamento->area) }}" class="btn btn-outline-info">
                                <i class="fas fa-filter"></i> Ver otros de {{ $almacenMedicamento->area_label }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar stock -->
<div class="modal fade" id="modalActualizarStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.almacen-medicamentos.actualizar-stock', $almacenMedicamento) }}">
                @csrf
                <div class="modal-body">
                    <p><strong>Item:</strong> {{ $almacenMedicamento->nombre }}</p>
                    <p><strong>Stock actual:</strong> {{ $almacenMedicamento->cantidad }}</p>
                    
                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Nueva Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo del cambio</label>
                        <textarea name="motivo" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function actualizarStock() {
    var modal = new bootstrap.Modal(document.getElementById('modalActualizarStock'));
    modal.show();
}
</script>
@endsection
