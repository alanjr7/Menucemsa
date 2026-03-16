@extends('layouts.app')

@section('title', 'Nueva Emergencia')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Registrar Nueva Emergencia</h1>
                <a href="{{ route('emergency-staff.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('emergency-staff.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    Estás registrando una nueva emergencia. Esta será automáticamente asignada a ti.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Paciente *</label>
                                    <select name="patient_id" id="patient_id" class="form-control @error('patient_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un paciente</option>
                                        @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }} - DNI: {{ $patient->dni }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cost">Costo Estimado ($) *</label>
                                    <input type="number" name="cost" id="cost" step="0.01" min="0" 
                                           class="form-control @error('cost') is-invalid @enderror" 
                                           value="{{ old('cost', 50) }}" required>
                                    <small class="form-text text-muted">Costo estimado de la atención de emergencia</small>
                                    @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="symptoms">Síntomas y Motivo de Consulta *</label>
                                    <textarea name="symptoms" id="symptoms" rows="4" 
                                              class="form-control @error('symptoms') is-invalid @enderror" 
                                              placeholder="Describa los síntomas del paciente y el motivo de la consulta de emergencia..."
                                              required>{{ old('symptoms') }}</textarea>
                                    @error('symptoms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="initial_assessment">Valoración Inicial</label>
                                    <textarea name="initial_assessment" id="initial_assessment" rows="3" 
                                              class="form-control @error('initial_assessment') is-invalid @enderror" 
                                              placeholder="Valoración inicial del estado del paciente...">{{ old('initial_assessment') }}</textarea>
                                    @error('initial_assessment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vital_signs">Signos Vitales</label>
                                    <textarea name="vital_signs" id="vital_signs" rows="3" 
                                              class="form-control @error('vital_signs') is-invalid @enderror" 
                                              placeholder="Presión arterial, frecuencia cardíaca, temperatura, saturación de oxígeno...">{{ old('vital_signs') }}</textarea>
                                    @error('vital_signs')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="triage_level">Nivel de Triage</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="triage_level" id="triage1" value="1">
                                                <label class="form-check-label" for="triage1">
                                                    <span class="badge badge-danger">Rojo</span> Emergencia Vital
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="triage_level" id="triage2" value="2" checked>
                                                <label class="form-check-label" for="triage2">
                                                    <span class="badge badge-warning">Naranja</span> Emergencia Urgente
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="triage_level" id="triage3" value="3">
                                                <label class="form-check-label" for="triage3">
                                                    <span class="badge badge-info">Amarillo</span> Urgencia
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="triage_level" id="triage4" value="4">
                                                <label class="form-check-label" for="triage4">
                                                    <span class="badge badge-success">Verde</span> No Urgente
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="observations">Observaciones Adicionales</label>
                                    <textarea name="observations" id="observations" rows="2" 
                                              class="form-control @error('observations') is-invalid @enderror" 
                                              placeholder="Cualquier observación adicional relevante...">{{ old('observations') }}</textarea>
                                    @error('observations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Importante:</strong> Al registrar esta emergencia, se generará automáticamente un código único y se asignará a tu usuario para su atención.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-ambulance"></i> Registrar Emergencia
                                </button>
                                <a href="{{ route('emergency-staff.dashboard') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('patient_id').addEventListener('change', function() {
    // Aquí podrías cargar información del paciente vía AJAX si es necesario
});

// Auto-guardar borrador cada 30 segundos
setInterval(function() {
    const formData = new FormData(document.querySelector('form'));
    localStorage.setItem('emergency_draft', JSON.stringify(Object.fromEntries(formData)));
}, 30000);

// Recuperar borrador si existe
window.addEventListener('load', function() {
    const draft = localStorage.getItem('emergency_draft');
    if (draft) {
        const data = JSON.parse(draft);
        // Poblar formulario con datos guardados
        Object.keys(data).forEach(key => {
            const field = document.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = data[key];
            }
        });
    }
});
</script>
@endsection
