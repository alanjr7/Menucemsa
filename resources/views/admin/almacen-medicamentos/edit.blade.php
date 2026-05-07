@extends('layouts.app')

@section('title', 'Editar: ' . $catalogo->nombre)

@prepend('head')
<style>
/* =====================================================
   PALETA DE COLORES MEDICA PROFESIONAL
   Sistema de salud - Menucemsa
   ===================================================== */
:root {
  /* Primarios médicos */
  --color-primary: #0d4f6b;
  --color-primary-hover: #083649;
  --color-primary-light: #e6f2f5;
  --color-secondary: #2d5a4a;
  --color-accent: #0891b2;
  
  /* Estados */
  --color-success: #059669;
  --color-success-light: #d1fae5;
  --color-warning: #d97706;
  --color-warning-light: #fef3c7;
  --color-danger: #dc2626;
  --color-danger-light: #fee2e2;
  --color-info: #0284c7;
  --color-info-light: #e0f2fe;
  
  /* Neutros médicos */
  --color-gray-25: #f8fafc;
  --color-gray-50: #f1f5f9;
  --color-gray-100: #e2e8f0;
  --color-gray-200: #cbd5e1;
  --color-gray-300: #94a3b8;
  --color-gray-400: #64748b;
  --color-gray-500: #475569;
  --color-gray-600: #334155;
  --color-gray-700: #1e293b;
  --color-gray-800: #0f172a;
  
  /* Sombras profesionales */
  --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.04);
  --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.08), 0 1px 2px -1px rgb(0 0 0 / 0.08);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.08), 0 2px 4px -2px rgb(0 0 0 / 0.08);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.08);
  --shadow-focus: 0 0 0 3px rgba(13, 79, 107, 0.15);
  
  /* Bordes */
  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  
  /* Transiciones */
  --transition-fast: 150ms ease-out;
  --transition-normal: 200ms ease-out;
}

/* Estilos base corporativos */
body {
  background: var(--color-gray-25);
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  line-height: 1.6;
  color: var(--color-gray-900);
}

/* Header corporativo médico */
.page-header {
  background: white;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
  padding: 1.5rem 2rem;
  margin-bottom: 1.5rem;
  border: 1px solid var(--color-gray-200);
}

.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-gray-800);
  margin-bottom: 0.25rem;
  letter-spacing: -0.02em;
}

.page-subtitle {
  color: var(--color-gray-500);
  font-size: 0.875rem;
  font-weight: 400;
  margin: 0;
}

/* Breadcrumb y navegación */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: var(--color-gray-400);
  margin-bottom: 0.75rem;
}

.breadcrumb a {
  color: var(--color-gray-500);
  text-decoration: none;
  transition: var(--transition-fast);
}

.breadcrumb a:hover {
  color: var(--color-primary);
}

/* Badges médicos */
.badge {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.badge-medicamento {
  background: var(--color-info-light);
  color: var(--color-info);
}

.badge-insumo {
  background: var(--color-gray-100);
  color: var(--color-gray-600);
}

.badge-activo {
  background: var(--color-success-light);
  color: var(--color-success);
}

.badge-inactivo {
  background: var(--color-gray-100);
  color: var(--color-gray-500);
}

.badge-vigente {
  background: var(--color-success-light);
  color: var(--color-success);
}

.badge-por-vencer {
  background: var(--color-warning-light);
  color: var(--color-warning);
}

.badge-vencido {
  background: var(--color-danger-light);
  color: var(--color-danger);
}

.badge-vencimiento {
  background: var(--color-danger-light);
  color: var(--color-danger);
  padding: 0.25rem 0.5rem;
  font-size: 0.7rem;
}

/* Cards de secciones corporativas */
.section-card {
  background: white;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-xs);
  border: 1px solid var(--color-gray-200);
  overflow: hidden;
  transition: var(--transition);
}

.section-card:hover {
  box-shadow: var(--shadow-sm);
}

.section-header {
  background: var(--color-gray-50);
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--color-gray-200);
}

.section-title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--color-gray-900);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
}

.section-content {
  padding: 1.5rem;
}

/* Formularios corporativos */
.form-group {
  margin-bottom: 1.25rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--color-gray-700);
  margin-bottom: 0.5rem;
  letter-spacing: 0.025em;
}

.form-label.required::after {
  content: ' *';
  color: var(--color-danger);
  font-weight: 600;
}

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid var(--color-gray-300);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  transition: var(--transition-fast);
  background-color: white;
  font-family: inherit;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: var(--shadow-focus);
}

.form-input.error,
.form-select.error,
.form-textarea.error {
  border-color: var(--color-danger);
  box-shadow: 0 0 0 1px var(--color-danger);
}

.error-message {
  color: var(--color-danger);
  font-size: 0.75rem;
  margin-top: 0.25rem;
  font-weight: 500;
}

/* Grid responsivo */
.form-grid {
  display: grid;
  gap: 1.5rem;
}

.form-grid-2 {
  grid-template-columns: repeat(2, 1fr);
}

.form-grid-3 {
  grid-template-columns: repeat(3, 1fr);
}

@media (max-width: 768px) {
  .form-grid-2,
  .form-grid-3 {
    grid-template-columns: 1fr;
  }
}

/* Lotes corporativos */
.lote-item {
  background: var(--color-gray-50);
  border: 1px solid var(--color-gray-200);
  border-radius: var(--radius-lg);
  padding: 1.25rem;
  margin-bottom: 1rem;
  transition: var(--transition-fast);
  position: relative;
}

.lote-item:hover {
  border-color: var(--color-gray-300);
  box-shadow: var(--shadow-sm);
}

.lote-item.vencido {
  background: #fef2f2;
  border-color: var(--color-danger);
}

.lote-item.por-vencer {
  background: #fffbeb;
  border-color: var(--color-warning);
}

.lote-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--color-gray-200);
}

.lote-title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--color-gray-900);
  margin: 0;
}

.lote-meta {
  font-size: 0.75rem;
  color: var(--color-gray-500);
  margin-left: 0.5rem;
}

/* Botones corporativos */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: var(--transition-fast);
  min-height: 40px;
  min-width: 40px;
  font-family: inherit;
}

.btn:focus {
  outline: none;
  box-shadow: 0 0 0 2px var(--color-primary);
}

.btn-primary {
  background: var(--color-primary);
  color: white;
}

.btn-primary:hover {
  background: var(--color-primary-hover);
}

.btn-secondary {
  background: white;
  color: var(--color-gray-700);
  border: 1px solid var(--color-gray-300);
}

.btn-secondary:hover {
  background: var(--color-gray-50);
  border-color: var(--color-gray-400);
}

.btn-success {
  background: var(--color-success);
  color: white;
}

.btn-success:hover {
  background: #047857;
}

.btn-danger {
  background: transparent;
  color: var(--color-gray-500);
  padding: 0.375rem;
}

.btn-danger:hover {
  background: var(--color-danger);
  color: white;
}

/* Alertas corporativas */
.alert {
  padding: 0.875rem 1.25rem;
  border-radius: var(--radius-md);
  margin-bottom: 1.25rem;
  border: 1px solid;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.alert-error {
  background: var(--color-danger-light);
  border-color: var(--color-danger);
  color: var(--color-danger);
}

.alert-success {
  background: var(--color-success-light);
  border-color: var(--color-success);
  color: var(--color-success);
}

.alert-warning {
  background: var(--color-warning-light);
  border-color: var(--color-warning);
  color: var(--color-warning);
}

.stock-item {
  background: white;
  border: 1px solid var(--color-gray-200);
  border-radius: var(--radius-md);
  padding: 0.75rem;
  transition: var(--transition-fast);
}

.stock-item:hover {
  border-color: var(--color-gray-300);
  box-shadow: var(--shadow-xs);
}

.stock-label {
  display: block;
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--color-gray-600);
  margin-bottom: 0.375rem;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.stock-field-label {
  display: block;
  font-size: 0.65rem;
  color: var(--color-gray-400);
  margin-top: 0.375rem;
  margin-bottom: 0.125rem;
}

.stock-small-input {
  padding: 0.375rem 0.5rem !important;
  font-size: 0.8rem !important;
}

.stock-item-central {
  border-color: var(--color-primary) !important;
  background: var(--color-primary-light) !important;
}

.stocks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 0.75rem;
  margin-top: 0.75rem;
}

/* Animaciones corporativas */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.lote-item {
  animation: slideIn 0.2s ease-out;
}

/* Layout de dos columnas */
.two-column-layout {
  display: grid;
  grid-template-columns: 380px 1fr;
  gap: 1.5rem;
  align-items: start;
}

@media (max-width: 1024px) {
  .two-column-layout {
    grid-template-columns: 1fr;
  }
}

/* Loading states */
.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn.loading::after {
  content: '';
  width: 14px;
  height: 14px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-left: 0.5rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
@endprepend

@section('content')
<div class="min-h-screen p-4 md:p-6">
    <!-- Header Profesional Mejorado -->
    <div class="page-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <!-- Breadcrumb -->
                <nav class="breadcrumb">
                    <a href="{{ route('admin.almacen-medicamentos.index') }}">Almacén</a>
                    <span>/</span>
                    <a href="{{ route('admin.almacen-medicamentos.show', $catalogo) }}">{{ $catalogo->nombre }}</a>
                    <span>/</span>
                    <span>Editar</span>
                </nav>
                
                <!-- Título con badges -->
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="page-title">{{ $catalogo->nombre }}</h1>
                    <span class="badge {{ $catalogo->tipo === 'medicamento' ? 'badge-medicamento' : 'badge-insumo' }}">
                        {{ $catalogo->tipo === 'medicamento' ? 'Medicamento' : 'Insumo' }}
                    </span>
                    <span class="badge {{ $catalogo->activo ? 'badge-activo' : 'badge-inactivo' }}">
                        {{ $catalogo->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <p class="page-subtitle">Edición completa del catálogo y gestión de lotes con precios</p>
            </div>
            <a href="{{ route('admin.almacen-medicamentos.show', $catalogo) }}"
               class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al detalle
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-error">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <strong>Error de validación:</strong>
            <ul class="mt-1 list-disc list-inside">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.almacen-medicamentos.update', $catalogo) }}" id="editForm">
        @csrf @method('PUT')

        <!-- Layout de Dos Columnas -->
        <div class="two-column-layout">
            <!-- Columna Izquierda: Información General -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <svg class="w-5 h-5" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Información General
                    </h2>
                </div>
                <div class="section-content">
                    <!-- Nombre destacado -->
                    <div class="form-group">
                        <label class="form-label required" for="nombre">Nombre del Producto</label>
                        <input type="text" id="nombre" name="nombre" 
                               value="{{ old('nombre', $catalogo->nombre) }}" 
                               required maxlength="255"
                               class="form-input @error('nombre') error @enderror"
                               style="font-size: 1rem; font-weight: 500;">
                        @error('nombre')<p class="error-message">{{ $message }}</p>@enderror
                    </div>

                    <!-- Grid de clasificación -->
                    <div class="form-grid form-grid-2">
                        <div class="form-group">
                            <label class="form-label required" for="tipo">Tipo</label>
                            <select id="tipo" name="tipo" required class="form-select">
                                @foreach($tipos as $v => $l)
                                    <option value="{{ $v }}" {{ old('tipo', $catalogo->tipo) == $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="unidad_medida">Unidad de Medida</label>
                            <select id="unidad_medida" name="unidad_medida" required class="form-select">
                                @foreach($unidades as $u)
                                    <option value="{{ $u }}" {{ old('unidad_medida', $catalogo->unidad_medida) == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="form-group">
                        <label class="form-label" for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4" 
                                  class="form-textarea" 
                                  placeholder="Descripción del producto, usos, indicaciones...">{{ old('descripcion', $catalogo->descripcion) }}</textarea>
                    </div>

                    <!-- Observaciones -->
                    <div class="form-group">
                        <label class="form-label" for="observaciones">
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align: text-bottom; margin-right: 4px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Observaciones Internas
                        </label>
                        <textarea id="observaciones" name="observaciones" rows="2" 
                                  class="form-textarea"
                                  placeholder="Notas internas para el personal...">{{ old('observaciones', $catalogo->observaciones) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Gestión de Lotes -->
            <div class="section-card">
                <div class="section-header">
                    <div class="flex items-center justify-between">
                        <h2 class="section-title">
                            <svg class="w-5 h-5" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Lotes y Precios
                            <span id="lotesCount" class="badge" style="background: var(--color-gray-100); color: var(--color-gray-600); margin-left: 0.5rem;">0</span>
                        </h2>
                        <button type="button" onclick="agregarLote()" 
                                class="btn btn-success">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nuevo Lote
                        </button>
                    </div>
                </div>
                <div class="section-content" style="max-height: calc(100vh - 300px); overflow-y: auto;">
                    <div id="lotesContainer" class="space-y-4">
                        <!-- Los lotes existentes se cargarán aquí con JavaScript -->
                    </div>
                    
                    <!-- Mensaje cuando no hay lotes -->
                    <div id="noLotesMessage" class="text-center py-8" style="display: none;">
                        <svg class="w-12 h-12 mx-auto mb-3" style="color: var(--color-gray-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p style="color: var(--color-gray-500); font-size: 0.875rem;">No hay lotes registrados</p>
                        <p style="color: var(--color-gray-400); font-size: 0.75rem; margin-top: 0.25rem;">Haga clic en "Nuevo Lote" para agregar uno</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción sticky -->
        <div class="flex gap-3 mt-6" style="position: sticky; bottom: 0; background: var(--color-gray-25); padding: 1rem 0; z-index: 10;">
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Cambios
            </button>
            <a href="{{ route('admin.almacen-medicamentos.show', $catalogo) }}"
               class="btn btn-secondary">
                Cancelar
            </a>
        </div>
    </form>
</div>

<!-- Template para nuevo lote -->
<template id="loteTemplate">
    <div class="lote-item" data-lote-id="">
        <div class="lote-header">
            <h3 class="lote-title">Nuevo Lote</h3>
            <button type="button" onclick="eliminarLote(this)" class="btn btn-danger">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

        <input type="hidden" name="lotes[{INDEX}][id]" value="">
        
        <div class="form-grid form-grid-3">
            <div class="form-group">
                <label class="form-label" for="codigo_lote_{INDEX}">Código de Lote</label>
                <input type="text" id="codigo_lote_{INDEX}" name="lotes[{INDEX}][codigo_lote]" 
                       class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label" for="fecha_vencimiento_{INDEX}">Fecha Vencimiento</label>
                <input type="date" id="fecha_vencimiento_{INDEX}" name="lotes[{INDEX}][fecha_vencimiento]" 
                       class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label required" for="cantidad_inicial_{INDEX}">Cantidad Inicial</label>
                <input type="number" id="cantidad_inicial_{INDEX}" name="lotes[{INDEX}][cantidad_inicial]" 
                       required min="0"
                       class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label" for="precio_compra_{INDEX}">Precio Compra</label>
                <input type="number" id="precio_compra_{INDEX}" name="lotes[{INDEX}][precio_compra]" 
                       step="0.01" min="0"
                       class="form-input"
                       onchange="calcularPrecioVenta({INDEX})">
            </div>

            <div class="form-group">
                <label class="form-label" for="porcentaje_ganancia_{INDEX}">% Ganancia</label>
                <input type="number" id="porcentaje_ganancia_{INDEX}" name="lotes[{INDEX}][porcentaje_ganancia]" 
                       step="0.01" min="0" max="999"
                       class="form-input"
                       onchange="calcularPrecioVenta({INDEX})">
            </div>

            <div class="form-group">
                <label class="form-label" for="precio_venta_{INDEX}">Precio Venta</label>
                <input type="number" id="precio_venta_{INDEX}" name="lotes[{INDEX}][precio_venta]" 
                       step="0.01" min="0"
                       class="form-input">
            </div>
        </div>

        <!-- Stocks por ubicación -->
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Stock y Distribución por Área</h4>
            <div class="stocks-grid">
                @foreach($ubicaciones as $key => $label)
                <div class="stock-item{{ $key === 'central' ? ' stock-item-central' : '' }}">
                    <label class="stock-label">{{ $label }}</label>
                    <input type="hidden" name="lotes[{INDEX}][stocks][{{ $loop->index }}][ubicacion]" value="{{ $key }}">
                    @if($key === 'central')
                    <span class="stock-field-label">Mínimo</span>
                    <input type="number"
                           name="lotes[{INDEX}][stocks][{{ $loop->index }}][stock_minimo]"
                           min="0" value="0" class="form-input stock-small-input">
                    @else
                    <span class="stock-field-label">Distribuir</span>
                    <input type="number"
                           name="lotes[{INDEX}][stocks][{{ $loop->index }}][cantidad_a_agregar]"
                           min="0" value="0" class="form-input stock-small-input">
                    <span class="stock-field-label">Mínimo</span>
                    <input type="number"
                           name="lotes[{INDEX}][stocks][{{ $loop->index }}][stock_minimo]"
                           min="0" value="0" class="form-input stock-small-input">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</template>

<script>
let loteIndex = 0;
const lotesExistentes = @json($catalogo->lotes);

// Cargar lotes existentes al iniciar
document.addEventListener('DOMContentLoaded', function() {
    cargarLotesExistentes();
    actualizarContadorLotes();
});

function cargarLotesExistentes() {
    const container = document.getElementById('lotesContainer');
    const noLotesMessage = document.getElementById('noLotesMessage');
    
    if (lotesExistentes.length === 0) {
        noLotesMessage.style.display = 'block';
        return;
    }
    
    noLotesMessage.style.display = 'none';
    lotesExistentes.forEach(function(lote, index) {
        const loteHtml = crearLoteHtml(lote, index);
        container.insertAdjacentHTML('beforeend', loteHtml);
        loteIndex = index + 1;
    });
}

function actualizarContadorLotes() {
    const container = document.getElementById('lotesContainer');
    const countBadge = document.getElementById('lotesCount');
    const noLotesMessage = document.getElementById('noLotesMessage');
    const lotesCount = container.querySelectorAll('.lote-item').length;
    
    countBadge.textContent = lotesCount;
    
    if (lotesCount === 0) {
        noLotesMessage.style.display = 'block';
    } else {
        noLotesMessage.style.display = 'none';
    }
}

function crearLoteHtml(lote, index) {
    let stocksHtml = '';
    const ubicaciones = @json($ubicaciones);
    
    Object.entries(ubicaciones).forEach(([key, label], stockIndex) => {
        const stock = lote.stocks?.find(s => s.ubicacion === key);
        const stockMinimo = stock?.stock_minimo || 0;

        if (key === 'central') {
            const cantidadActual = stock?.cantidad_actual ?? 0;
            stocksHtml += `
                <div class="stock-item stock-item-central">
                    <label class="stock-label">${label}</label>
                    <input type="hidden" name="lotes[${index}][stocks][${stockIndex}][ubicacion]" value="${key}">
                    <span class="stock-field-label">Stock actual</span>
                    <input type="number"
                           name="lotes[${index}][stocks][${stockIndex}][cantidad_actual]"
                           min="0" value="${cantidadActual}"
                           class="form-input stock-small-input">
                    <span class="stock-field-label">Mínimo</span>
                    <input type="number"
                           name="lotes[${index}][stocks][${stockIndex}][stock_minimo]"
                           min="0" value="${stockMinimo}"
                           class="form-input stock-small-input">
                </div>
            `;
        } else {
            const cantidadActual = stock?.cantidad_actual ?? 0;
            stocksHtml += `
                <div class="stock-item">
                    <label class="stock-label">${label}</label>
                    <input type="hidden" name="lotes[${index}][stocks][${stockIndex}][ubicacion]" value="${key}">
                    <span class="stock-field-label" style="color:var(--color-gray-400);">Actual: ${cantidadActual}</span>
                    <span class="stock-field-label">Distribuir</span>
                    <input type="number"
                           name="lotes[${index}][stocks][${stockIndex}][cantidad_a_agregar]"
                           min="0" value="0"
                           class="form-input stock-small-input">
                    <span class="stock-field-label">Mínimo</span>
                    <input type="number"
                           name="lotes[${index}][stocks][${stockIndex}][stock_minimo]"
                           min="0" value="${stockMinimo}"
                           class="form-input stock-small-input">
                </div>
            `;
        }
    });

    const vencimientoClass = lote.estado_vencimiento === 'vencido' ? 'vencido' : 
                             (lote.estado_vencimiento === 'por_vencer' ? 'por-vencer' : '');
    
    let badgeVencimiento = '';
    if (lote.estado_vencimiento === 'vencido') {
        badgeVencimiento = '<span class="badge badge-vencimiento"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>Vencido</span>';
    } else if (lote.estado_vencimiento === 'por_vencer') {
        badgeVencimiento = '<span class="badge badge-por-vencer"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Por vencer</span>';
    } else if (lote.estado_vencimiento === 'vigente') {
        badgeVencimiento = '<span class="badge badge-vigente"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Vigente</span>';
    }
    
    const diasParaVencer = lote.dias_para_vencer !== null ? ` (${lote.dias_para_vencer} días)` : '';

    return `
        <div class="lote-item ${vencimientoClass}" data-lote-id="${lote.id}">
            <div class="lote-header">
                <div class="flex items-center gap-2">
                    <h3 class="lote-title">${lote.codigo_lote || 'Nuevo Lote'}</h3>
                    ${badgeVencimiento}
                </div>
                <button type="button" onclick="eliminarLote(this)" class="btn btn-danger" title="Eliminar lote">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>

            <input type="hidden" name="lotes[${index}][id]" value="${lote.id}">
            
            <div class="form-grid form-grid-3">
                <div class="form-group">
                    <label class="form-label" for="codigo_lote_${index}">Código de Lote</label>
                    <input type="text" id="codigo_lote_${index}" name="lotes[${index}][codigo_lote]" 
                           value="${lote.codigo_lote || ''}"
                           class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label" for="fecha_vencimiento_${index}">Fecha Vencimiento</label>
                    <input type="date" id="fecha_vencimiento_${index}" name="lotes[${index}][fecha_vencimiento]" 
                           value="${lote.fecha_vencimiento || ''}"
                           class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label required" for="cantidad_inicial_${index}">Cantidad Inicial</label>
                    <input type="number" id="cantidad_inicial_${index}" name="lotes[${index}][cantidad_inicial]" 
                           required min="0"
                           value="${lote.cantidad_inicial || 0}"
                           class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label" for="precio_compra_${index}">Precio Compra</label>
                    <input type="number" id="precio_compra_${index}" name="lotes[${index}][precio_compra]" 
                           step="0.01" min="0"
                           value="${lote.precio_compra || ''}"
                           class="form-input"
                           onchange="calcularPrecioVenta(${index})">
                </div>

                <div class="form-group">
                    <label class="form-label" for="porcentaje_ganancia_${index}">% Ganancia</label>
                    <input type="number" id="porcentaje_ganancia_${index}" name="lotes[${index}][porcentaje_ganancia]" 
                           step="0.01" min="0" max="999"
                           value="${lote.porcentaje_ganancia || ''}"
                           class="form-input"
                           onchange="calcularPrecioVenta(${index})">
                </div>

                <div class="form-group">
                    <label class="form-label" for="precio_venta_${index}">Precio Venta</label>
                    <input type="number" id="precio_venta_${index}" name="lotes[${index}][precio_venta]" 
                           step="0.01" min="0"
                           value="${lote.precio_venta || ''}"
                           class="form-input">
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Stock y Distribución por Área</h4>
                <div class="stocks-grid">
                    ${stocksHtml}
                </div>
            </div>
        </div>
    `;
}

function agregarLote() {
    const container = document.getElementById('lotesContainer');
    const template = document.getElementById('loteTemplate').innerHTML;
    const loteHtml = template.replace(/{INDEX}/g, loteIndex);
    container.insertAdjacentHTML('beforeend', loteHtml);
    loteIndex++;
    actualizarContadorLotes();
}

function eliminarLote(button) {
    const loteItem = button.closest('.lote-item');
    const loteId = loteItem.dataset.loteId;
    
    // Si tiene ID, es un lote existente - pedir confirmación
    if (loteId) {
        if (!confirm('¿Está seguro de eliminar este lote? Esta acción no se puede deshacer.')) {
            return;
        }
    }
    
    loteItem.style.opacity = '0.5';
    setTimeout(() => {
        loteItem.remove();
        actualizarContadorLotes();
    }, 150);
}

function calcularPrecioVenta(index) {
    const precioCompra = parseFloat(document.querySelector(`input[name="lotes[${index}][precio_compra]"]`).value) || 0;
    const porcentaje = parseFloat(document.querySelector(`input[name="lotes[${index}][porcentaje_ganancia]"]`).value) || 0;
    const precioVenta = precioCompra * (1 + porcentaje / 100);
    
    const precioVentaInput = document.querySelector(`input[name="lotes[${index}][precio_venta]"]`);
    if (precioVenta > 0) {
        precioVentaInput.value = precioVenta.toFixed(2);
    }
}
</script>
@endsection
