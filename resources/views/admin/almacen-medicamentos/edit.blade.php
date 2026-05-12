@extends('layouts.app')

@section('title', 'Editar: ' . $catalogo->nombre)

@prepend('head')
<style>
:root {
  --primary:       #0d4f6b;
  --primary-hover: #093d54;
  --primary-light: #edf4f7;
  --primary-ring:  rgba(13, 79, 107, 0.16);

  --success:       #065f46;
  --success-hover: #054d38;
  --danger:        #b91c1c;
  --danger-bg:     #fef2f2;
  --warning:       #92400e;
  --warning-bg:    #fffbeb;

  --gray-50:  #f9fafb;
  --gray-100: #f3f4f6;
  --gray-200: #e5e7eb;
  --gray-300: #d1d5db;
  --gray-400: #9ca3af;
  --gray-500: #6b7280;
  --gray-600: #4b5563;
  --gray-700: #374151;
  --gray-800: #1f2937;
  --gray-900: #111827;

  --radius-sm: 4px;
  --radius-md: 7px;
  --radius-lg: 11px;

  --shadow-xs: 0 1px 2px rgb(0 0 0 / 0.05);
  --shadow-sm: 0 1px 4px rgb(0 0 0 / 0.07), 0 1px 2px rgb(0 0 0 / 0.04);

  --t: 140ms ease;
}

body {
  background: #eef0f3;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
  color: var(--gray-900);
  line-height: 1.5;
}

/* ---- Page header ---- */
.page-header {
  background: white;
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-200);
  padding: 1.25rem 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow-xs);
}
.page-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--gray-900);
  letter-spacing: -0.015em;
  margin: 0 0 0.125rem;
}
.page-subtitle { font-size: 0.8125rem; color: var(--gray-500); margin: 0; }

/* ---- Breadcrumb ---- */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.75rem;
  color: var(--gray-400);
  margin-bottom: 0.5rem;
}
.breadcrumb a { color: var(--gray-500); text-decoration: none; transition: color var(--t); }
.breadcrumb a:hover { color: var(--primary); }

/* ---- Badges — sobrios ---- */
.badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.2rem 0.6rem;
  border-radius: var(--radius-sm);
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
.badge-medicamento { background: #dbeafe; color: #1e40af; }
.badge-insumo      { background: var(--gray-100); color: var(--gray-600); }
.badge-activo      { background: #dcfce7; color: #166534; }
.badge-inactivo    { background: var(--gray-100); color: var(--gray-500); }
.badge-vigente     { background: #dcfce7; color: #166534; }
.badge-por-vencer  { background: #fef9c3; color: #854d0e; }
.badge-vencido     { background: #fee2e2; color: #991b1b; }
.badge-vencimiento { background: #fee2e2; color: #991b1b; padding: 0.2rem 0.5rem; font-size: 0.6875rem; }
#lotesCount        { background: var(--gray-100); color: var(--gray-600); }

/* ---- Section cards ---- */
.section-card {
  background: white;
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-200);
  box-shadow: var(--shadow-xs);
  overflow: hidden;
}
.section-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid var(--gray-200);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: white;
}
.section-title {
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--gray-800);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
}
.section-title svg { color: var(--primary); }
.section-content { padding: 1.5rem; }

/* ---- Forms ---- */
.form-group { margin-bottom: 1.125rem; }

.form-label {
  display: block;
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--gray-600);
  margin-bottom: 0.375rem;
  letter-spacing: 0.01em;
}
.form-label.required::after { content: ' *'; color: var(--danger); }

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 0.6875rem 0.9375rem;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
  font-size: 0.9375rem;
  line-height: 1.5;
  min-height: 44px;
  background: white;
  color: var(--gray-900);
  font-family: inherit;
  transition: border-color var(--t), box-shadow var(--t);
}
.form-input::placeholder, .form-textarea::placeholder { color: var(--gray-400); }
.form-input:hover, .form-select:hover { border-color: var(--gray-400); }
.form-input:focus, .form-select:focus, .form-textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px var(--primary-ring);
}
.form-input.error { border-color: var(--danger); box-shadow: 0 0 0 2px rgba(185,28,28,.12); }
.error-message { font-size: 0.75rem; color: var(--danger); margin-top: 0.25rem; font-weight: 500; }

/* ---- Grid ---- */
.form-grid { display: grid; gap: 1.125rem; }
.form-grid-2 { grid-template-columns: repeat(2, 1fr); }
.form-grid-3 { grid-template-columns: repeat(3, 1fr); }
@media (max-width: 768px) {
  .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
}

/* ---- Lote cards ---- */
.lote-item {
  background: white;
  border: 1px solid var(--gray-200);
  border-left: 3px solid var(--primary);
  border-radius: var(--radius-lg);
  padding: 1.25rem 1.375rem;
  margin-bottom: 0.875rem;
  animation: slideIn 0.18s ease-out;
  transition: box-shadow var(--t);
}
.lote-item:hover { box-shadow: var(--shadow-sm); }
.lote-item.vencido    { border-left-color: var(--danger);  background: #fffafa; }
.lote-item.por-vencer { border-left-color: var(--warning); background: #fffef5; }

.lote-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.125rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--gray-100);
}
.lote-title { font-size: 0.9375rem; font-weight: 600; color: var(--gray-800); margin: 0; }

/* ---- Stock area ---- */
.stock-item {
  background: var(--gray-50);
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  padding: 0.625rem 0.75rem;
  transition: border-color var(--t);
}
.stock-item:hover { border-color: var(--gray-300); }

.stock-label {
  display: block;
  font-size: 0.6875rem;
  font-weight: 700;
  color: var(--gray-500);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.375rem;
}
.stock-field-label {
  display: block;
  font-size: 0.6875rem;
  color: var(--gray-400);
  margin: 0.3rem 0 0.1rem;
}
.stock-small-input {
  padding: 0.375rem 0.5rem !important;
  font-size: 0.8125rem !important;
  min-height: 32px !important;
}
.stocks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
  gap: 0.625rem;
  margin-top: 0.75rem;
}
.stock-item-central {
  border-color: var(--primary) !important;
  background: var(--primary-light) !important;
}
.form-input[readonly] {
  background: var(--gray-50);
  color: var(--gray-600);
  cursor: default;
  border-color: var(--gray-200);
  user-select: none;
}
.stocks-section-label {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--gray-400);
  margin: 0 0 0.5rem;
}

/* ---- Buttons ---- */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.375rem;
  padding: 0.625rem 1.125rem;
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  min-height: 40px;
  font-family: inherit;
  text-decoration: none;
  transition: background var(--t), border-color var(--t), box-shadow var(--t);
}
.btn:focus { outline: none; box-shadow: 0 0 0 3px var(--primary-ring); }
.btn:disabled { opacity: 0.45; cursor: not-allowed; }
.btn-primary   { background: var(--primary); color: white; }
.btn-primary:hover { background: var(--primary-hover); }
.btn-secondary { background: white; color: var(--gray-700); border: 1px solid var(--gray-300); }
.btn-secondary:hover { background: var(--gray-50); border-color: var(--gray-400); }
.btn-success   { background: var(--success); color: white; }
.btn-success:hover { background: var(--success-hover); }
.btn-danger    { background: transparent; color: var(--gray-400); padding: 0.375rem 0.5rem; border-radius: var(--radius-sm); }
.btn-danger:hover { background: var(--danger-bg); color: var(--danger); }

/* ---- Alerts ---- */
.alert {
  padding: 0.75rem 1.125rem;
  border-radius: var(--radius-md);
  margin-bottom: 1.25rem;
  border: 1px solid;
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  font-size: 0.875rem;
}
.alert-error   { background: var(--danger-bg); border-color: #fca5a5; color: #991b1b; }
.alert-success { background: #f0fdf4; border-color: #86efac; color: #166534; }
.alert-warning { background: var(--warning-bg); border-color: #fcd34d; color: var(--warning); }

/* ---- Numeric inputs — no spinners, no scroll ---- */
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
input[type=number] { -moz-appearance: textfield; appearance: textfield; }
input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }

/* ---- Layout ---- */
.two-column-layout {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 1.5rem;
  align-items: start;
}
@media (max-width: 1024px) { .two-column-layout { grid-template-columns: 1fr; } }

.action-bar {
  position: sticky;
  bottom: 0;
  background: rgba(238, 240, 243, 0.96);
  backdrop-filter: blur(8px);
  padding: 0.875rem 0;
  margin-top: 1.5rem;
  z-index: 20;
  display: flex;
  gap: 0.75rem;
  border-top: 1px solid var(--gray-200);
}

/* ---- Animations ---- */
@keyframes slideIn {
  from { opacity: 0; transform: translateY(6px); }
  to   { opacity: 1; transform: translateY(0); }
}
.btn.loading::after {
  content: '';
  width: 14px; height: 14px;
  border: 2px solid transparent;
  border-top-color: currentColor;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
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

    @if(session('error'))
    <div class="alert alert-error">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                               class="form-input @error('nombre') error @enderror" style="font-weight:500;">
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
                    <h2 class="section-title">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Lotes y Precios
                        <span id="lotesCount" class="badge" style="margin-left:0.25rem;">0</span>
                    </h2>
                    <button type="button" onclick="agregarLote()" class="btn btn-success">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nuevo Lote
                    </button>
                </div>
                <div class="section-content">
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

        <div class="action-bar">
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Cambios
            </button>
            <a href="{{ route('admin.almacen-medicamentos.show', $catalogo) }}" class="btn btn-secondary">
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
                       class="form-input"
                       oninput="this.closest('.lote-item').querySelector('.lote-title').textContent = this.value || 'Nuevo Lote'">
            </div>

            <div class="form-group">
                <label class="form-label" for="fecha_vencimiento_{INDEX}">Fecha Vencimiento</label>
                <input type="date" id="fecha_vencimiento_{INDEX}" name="lotes[{INDEX}][fecha_vencimiento]"
                       class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label required" for="cantidad_inicial_{INDEX}">Cantidad Inicial</label>
                <input type="number" id="cantidad_inicial_{INDEX}" name="lotes[{INDEX}][cantidad_inicial]"
                       required min="0" placeholder="0"
                       class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label" for="stock_minimo_central_{INDEX}">Stock Mínimo</label>
                <input type="hidden" name="lotes[{INDEX}][stocks][0][ubicacion]" value="central">
                <input type="number" id="stock_minimo_central_{INDEX}" name="lotes[{INDEX}][stocks][0][stock_minimo]"
                       min="0" placeholder="0"
                       class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label" for="precio_compra_{INDEX}">Precio Compra</label>
                <input type="number" id="precio_compra_{INDEX}" name="lotes[{INDEX}][precio_compra]"
                       step="0.01" min="0" placeholder="0.00"
                       class="form-input"
                       onchange="calcularPrecioVenta({INDEX})">
            </div>

            <div class="form-group">
                <label class="form-label" for="porcentaje_ganancia_{INDEX}">% Ganancia</label>
                <input type="number" id="porcentaje_ganancia_{INDEX}" name="lotes[{INDEX}][porcentaje_ganancia]"
                       step="0.01" min="0" max="999" placeholder="0"
                       class="form-input"
                       onchange="calcularPrecioVenta({INDEX})">
            </div>

            <div class="form-group">
                <label class="form-label" for="precio_venta_{INDEX}">Precio Venta</label>
                <input type="number" id="precio_venta_{INDEX}" name="lotes[{INDEX}][precio_venta]"
                       step="0.01" min="0" placeholder="0.00"
                       class="form-input"
                       onchange="calcularPorcentaje({INDEX})">
            </div>
        </div>

        <!-- Stocks por ubicación -->
        <div class="mt-5">
            <p class="stocks-section-label">Distribución por Área</p>
            <div class="stocks-grid">
                @foreach($ubicaciones as $key => $label)
                @if($key === 'central') @continue @endif
                <div class="stock-item">
                    <label class="stock-label">{{ $label }}</label>
                    <input type="hidden" name="lotes[{INDEX}][stocks][{{ $loop->index }}][ubicacion]" value="{{ $key }}">
                    <span class="stock-field-label">Distribuir</span>
                    <input type="number"
                           name="lotes[{INDEX}][stocks][{{ $loop->index }}][cantidad_a_agregar]"
                           min="0" placeholder="0" class="form-input stock-small-input">
                    <span class="stock-field-label">Mínimo</span>
                    <input type="number"
                           name="lotes[{INDEX}][stocks][{{ $loop->index }}][stock_minimo]"
                           min="0" placeholder="0" class="form-input stock-small-input">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</template>

<script>
let loteIndex = 0;
const lotesExistentes = @json($catalogo->lotes);

document.addEventListener('DOMContentLoaded', function() {
    cargarLotesExistentes();
    actualizarContadorLotes();
});

// Evitar que la rueda del mouse cambie valores numéricos
document.addEventListener('wheel', function() {
    if (document.activeElement.type === 'number') {
        document.activeElement.blur();
    }
}, { passive: true });

// Antes de enviar, los campos requeridos vacíos se envían como 0
document.getElementById('editForm').addEventListener('submit', function() {
    this.querySelectorAll('input[name*="stock_minimo"], input[name*="cantidad_actual"]').forEach(function(input) {
        if (input.value === '') input.value = '0';
    });
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

    const centralStock = lote.stocks?.find(s => s.ubicacion === 'central');
    const centralStockMinimo = centralStock?.stock_minimo || 0;
    const centralCantidadActual = centralStock?.cantidad_actual ?? 0;

    stocksHtml += `
        <div class="stock-item stock-item-central">
            <label class="stock-label">Central</label>
            <input type="hidden" name="lotes[${index}][stocks][0][ubicacion]" value="central">
            <span class="stock-field-label">Stock actual</span>
            <input type="number"
                   name="lotes[${index}][stocks][0][cantidad_actual]"
                   min="0" placeholder="0" value="${centralCantidadActual}"
                   class="form-input stock-small-input">
            <span class="stock-field-label">Mínimo</span>
            <input type="number"
                   name="lotes[${index}][stocks][0][stock_minimo]"
                   min="0" placeholder="0" value="${centralStockMinimo}"
                   class="form-input stock-small-input">
        </div>
    `;

    Object.entries(ubicaciones).forEach(([key, label], stockIndex) => {
        if (key === 'central') return;

        const stock = lote.stocks?.find(s => s.ubicacion === key);
        const stockMinimo = stock?.stock_minimo || 0;
        const cantidadActual = stock?.cantidad_actual ?? 0;

        stocksHtml += `
            <div class="stock-item">
                <label class="stock-label">${label}</label>
                <input type="hidden" name="lotes[${index}][stocks][${stockIndex}][ubicacion]" value="${key}">
                <span class="stock-field-label" style="color:var(--color-gray-400);">Actual: ${cantidadActual}</span>
                <span class="stock-field-label">Distribuir</span>
                <input type="number"
                       name="lotes[${index}][stocks][${stockIndex}][cantidad_a_agregar]"
                       min="0" placeholder="0"
                       class="form-input stock-small-input">
                <span class="stock-field-label">Mínimo</span>
                <input type="number"
                       name="lotes[${index}][stocks][${stockIndex}][stock_minimo]"
                       min="0" value="${stockMinimo}"
                       class="form-input stock-small-input">
            </div>
        `;
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
                           class="form-input"
                           oninput="this.closest('.lote-item').querySelector('.lote-title').textContent = this.value || 'Nuevo Lote'">
                </div>

                <div class="form-group">
                    <label class="form-label" for="fecha_vencimiento_${index}">Fecha Vencimiento</label>
                    <input type="date" id="fecha_vencimiento_${index}" name="lotes[${index}][fecha_vencimiento]"
                           value="${lote.fecha_vencimiento || ''}"
                           class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label" for="cantidad_inicial_${index}">Cantidad Inicial</label>
                    <input type="number" id="cantidad_inicial_${index}" name="lotes[${index}][cantidad_inicial]"
                           min="0" value="${lote.cantidad_inicial || 0}"
                           class="form-input" readonly tabindex="-1">
                </div>

                <div class="form-group">
                    <label class="form-label" for="precio_compra_${index}">Precio Compra</label>
                    <input type="number" id="precio_compra_${index}" name="lotes[${index}][precio_compra]"
                           step="0.01" min="0" placeholder="0.00"
                           value="${lote.precio_compra || ''}"
                           class="form-input"
                           onchange="calcularPrecioVenta(${index})">
                </div>

                <div class="form-group">
                    <label class="form-label" for="porcentaje_ganancia_${index}">% Ganancia</label>
                    <input type="number" id="porcentaje_ganancia_${index}" name="lotes[${index}][porcentaje_ganancia]"
                           step="0.01" min="0" max="999" placeholder="0"
                           value="${lote.porcentaje_ganancia || ''}"
                           class="form-input"
                           onchange="calcularPrecioVenta(${index})">
                </div>

                <div class="form-group">
                    <label class="form-label" for="precio_venta_${index}">Precio Venta</label>
                    <input type="number" id="precio_venta_${index}" name="lotes[${index}][precio_venta]"
                           step="0.01" min="0" placeholder="0.00"
                           value="${lote.precio_venta || ''}"
                           class="form-input"
                           onchange="calcularPorcentaje(${index})">
                </div>
            </div>

            <div class="mt-5">
                <p class="stocks-section-label">Distribución por Área</p>
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

function calcularPorcentaje(index) {
    const precioCompra = parseFloat(document.querySelector(`input[name="lotes[${index}][precio_compra]"]`).value) || 0;
    const precioVenta = parseFloat(document.querySelector(`input[name="lotes[${index}][precio_venta]"]`).value) || 0;

    if (precioCompra <= 0 || precioVenta <= 0) return;

    const porcentaje = ((precioVenta - precioCompra) / precioCompra) * 100;
    document.querySelector(`input[name="lotes[${index}][porcentaje_ganancia]"]`).value = porcentaje.toFixed(2);
}
</script>
@endsection
