@extends('layouts.app')

@section('title', 'Editar Emergencia')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Editar Emergencia {{ $emergency->code }}</h1>
                <a href="{{ route('admin.emergencies.show', $emergency) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.emergencies.update', $emergency) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Paciente *</label>
                                    <select name="patient_id" id="patient_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('patient_id') border-red-500 @enderror" required>
                                        <option value="">Seleccione un paciente</option>
                                        @foreach($pacientes as $paciente)
                                        <option value="{{ $paciente->id }}" {{ old('patient_id', $emergency->patient_id) == $paciente->id ? 'selected' : '' }}">
                                            {{ $paciente->name }} - {{ $paciente->dni }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Personal de Emergencia *</label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">Seleccione personal</option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $emergency->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Estado *</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="recibido" {{ old('status', $emergency->status) == 'recibido' ? 'selected' : '' }}>Recibido</option>
                                        <option value="en_evaluacion" {{ old('status', $emergency->status) == 'en_evaluacion' ? 'selected' : '' }}>En Evaluación</option>
                                        <option value="estabilizado" {{ old('status', $emergency->status) == 'estabilizado' ? 'selected' : '' }}>Estabilizado</option>
                                        <option value="uti" {{ old('status', $emergency->status) == 'uti' ? 'selected' : '' }}>UTI</option>
                                        <option value="cirugia" {{ old('status', $emergency->status) == 'cirugia' ? 'selected' : '' }}>Cirugía</option>
                                        <option value="alta" {{ old('status', $emergency->status) == 'alta' ? 'selected' : '' }}>Alta</option>
                                        <option value="fallecido" {{ old('status', $emergency->status) == 'fallecido' ? 'selected' : '' }}>Fallecido</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="destination">Destino</label>
                                    <select name="destination" id="destination" class="form-control @error('destination') is-invalid @enderror">
                                        <option value="">Seleccione destino</option>
                                        <option value="observacion" {{ old('destination', $emergency->destination) == 'observacion' ? 'selected' : '' }}>Observación</option>
                                        <option value="uti" {{ old('destination', $emergency->destination) == 'uti' ? 'selected' : '' }}>UTI</option>
                                        <option value="cirugia" {{ old('destination', $emergency->destination) == 'cirugia' ? 'selected' : '' }}>Cirugía</option>
                                        <option value="consulta_externa" {{ old('destination', $emergency->destination) == 'consulta_externa' ? 'selected' : '' }}>Consulta Externa</option>
                                        <option value="alta" {{ old('destination', $emergency->destination) == 'alta' ? 'selected' : '' }}>Alta</option>
                                    </select>
                                    @error('destination')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="symptoms">Síntomas *</label>
                                    <textarea name="symptoms" id="symptoms" rows="3" 
                                              class="form-control @error('symptoms') is-invalid @enderror" required>{{ old('symptoms', $emergency->symptoms) }}</textarea>
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
                                              class="form-control @error('initial_assessment') is-invalid @enderror">{{ old('initial_assessment', $emergency->initial_assessment) }}</textarea>
                                    @error('initial_assessment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vital_signs">Signos Vitales</label>
                                    <textarea name="vital_signs" id="vital_signs" rows="3" 
                                              class="form-control @error('vital_signs') is-invalid @enderror">{{ old('vital_signs', $emergency->vital_signs) }}</textarea>
                                    @error('vital_signs')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="treatment">Tratamiento</label>
                                    <textarea name="treatment" id="treatment" rows="3" 
                                              class="form-control @error('treatment') is-invalid @enderror">{{ old('treatment', $emergency->treatment) }}</textarea>
                                    @error('treatment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="observations">Observaciones</label>
                                    <textarea name="observations" id="observations" rows="3" 
                                              class="form-control @error('observations') is-invalid @enderror">{{ old('observations', $emergency->observations) }}</textarea>
                                    @error('observations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cost">Costo ($) *</label>
                                    <input type="number" name="cost" id="cost" step="0.01" min="0" 
                                           class="form-control @error('cost') is-invalid @enderror" 
                                           value="{{ old('cost', $emergency->cost) }}" required>
                                    @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="paid" id="paid" 
                                               {{ old('paid', $emergency->paid) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="paid">
                                            Pagado
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar Emergencia
                                </button>
                                <a href="{{ route('admin.emergencies.show', $emergency) }}" class="btn btn-secondary">
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
@endsection
