@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/30 to-teal-50/20 p-4 sm:p-6 lg:p-8" x-data="farmaciaDashboard()">
    <!-- Header Section -->
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-slate-800 tracking-tight">
                    Panel de Farmacia
                    <span class="block text-lg font-medium text-slate-500 mt-1">Sistema de gestión integral</span>
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Sistema Activo
                </span>
                <a href="{{ route('farmacia.pos') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:shadow-emerald-300 transition-all duration-200 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nueva Venta
                </a>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Ventas Hoy -->
            <div class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg hover:border-emerald-200 transition-all duration-300">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ventas Hoy</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-2">${{ number_format($ingresosHoy, 2) }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ $ventasHoy }} transacciones</p>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm">
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Activo
                    </span>
                </div>
            </div>

            <!-- Productos en Stock -->
            <div class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Productos en Stock</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-2">{{ $totalMedicamentos }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ $medicamentosDistintos }} distintos</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl group-hover:bg-blue-100 transition-colors">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('farmacia.inventario') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                        Ver inventario
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Alertas -->
            <div class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg hover:border-amber-200 transition-all duration-300">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-400 to-orange-500 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Alertas</p>
                        <p class="text-2xl sm:text-3xl font-bold {{ ($alertasStock->count() + $alertasVencimiento->count()) > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-2">
                            {{ $alertasStock->count() + $alertasVencimiento->count() }}
                        </p>
                        <p class="text-sm text-slate-500 mt-1">Requieren atención</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-xl group-hover:bg-amber-100 transition-colors">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    @if($alertasStock->count() > 0)
                        <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-medium">{{ $alertasStock->count() }} stock bajo</span>
                    @endif
                    @if($alertasVencimiento->count() > 0)
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-medium">{{ $alertasVencimiento->count() }} por vencer</span>
                    @endif
                </div>
            </div>

            <!-- Total Ventas -->
            <div class="group relative bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg hover:border-purple-200 transition-all duration-300">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-purple-400 to-pink-500 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Ventas</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-2">{{ $totalVentas }}</p>
                        <p class="text-sm text-slate-500 mt-1">Histórico completo</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-xl group-hover:bg-purple-100 transition-colors">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('farmacia.ventas') }}" class="text-sm font-medium text-purple-600 hover:text-purple-700 inline-flex items-center gap-1">
                        Ver reportes
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Gráfico de Ventas -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Tendencia de Ventas</h2>
                        <p class="text-sm text-slate-500">Últimos 7 días</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-3 py-1.5 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">7 días</button>
                        <button class="px-3 py-1.5 text-sm font-medium text-slate-500 hover:bg-slate-100 rounded-lg transition-colors">30 días</button>
                    </div>
                </div>
                <div class="h-64 relative">
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>

            <!-- Alertas Detalladas -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-slate-800">Alertas de Inventario</h2>
                    <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">
                        {{ $alertasStock->count() + $alertasVencimiento->count() }} alertas
                    </span>
                </div>
                
                <div class="space-y-3 max-h-64 overflow-y-auto custom-scrollbar">
                    @forelse($alertasStock as $alerta)
                        <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl border border-amber-100">
                            <div class="p-2 bg-amber-100 rounded-lg">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $alerta['nombre'] }}</p>
                                <p class="text-xs text-slate-500">Stock: {{ $alerta['stock_actual'] }}/{{ $alerta['stock_minimo'] }}</p>
                            </div>
                            <span class="px-2 py-1 bg-amber-200 text-amber-800 rounded-lg text-xs font-semibold">Bajo</span>
                        </div>
                    @empty
                        @if($alertasVencimiento->count() == 0)
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-full mb-3">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-slate-600 font-medium">Sin alertas activas</p>
                                <p class="text-xs text-slate-400 mt-1">Todo el inventario está en orden</p>
                            </div>
                        @endif
                    @endforelse
                    
                    @foreach($alertasVencimiento as $alerta)
                        <div class="flex items-center gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $alerta['nombre'] }}</p>
                                <p class="text-xs text-slate-500">Vence en {{ $alerta['dias_para_vencer'] }} días</p>
                            </div>
                            <span class="px-2 py-1 bg-red-200 text-red-800 rounded-lg text-xs font-semibold">Vencer</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Últimas Ventas y Accesos Rápidos -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Últimas Ventas -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Últimas Ventas</h2>
                        <p class="text-sm text-slate-500">Transacciones recientes</p>
                    </div>
                    <a href="{{ route('farmacia.ventas') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                        Ver todas
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-slate-100">
                                <th class="pb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Código</th>
                                <th class="pb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Cliente</th>
                                <th class="pb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha</th>
                                <th class="pb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Total</th>
                                <th class="pb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Productos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($ultimasVentas as $venta)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium">
                                            {{ $venta->codigo_venta }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <p class="text-sm font-medium text-slate-800">{{ $venta->cliente ?: 'Cliente General' }}</p>
                                    </td>
                                    <td class="py-3">
                                        <p class="text-sm text-slate-600">{{ $venta->fecha_venta ? $venta->fecha_venta->format('d/m/Y H:i') : 'N/A' }}</p>
                                    </td>
                                    <td class="py-3 text-right">
                                        <p class="text-sm font-bold text-emerald-600">${{ number_format($venta->total, 2) }}</p>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold">
                                            {{ $venta->detalles->count() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm text-slate-600 font-medium">No hay ventas registradas</p>
                                            <p class="text-xs text-slate-400 mt-1">Las ventas aparecerán aquí</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Accesos Rápidos -->
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-slate-800">Accesos Rápidos</h2>
                
                <a href="{{ route('farmacia.pos') }}" class="group flex items-center gap-4 p-4 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl text-white shadow-lg shadow-emerald-200 hover:shadow-xl hover:scale-[1.02] transition-all duration-200">
                    <div class="p-3 bg-white/20 rounded-xl group-hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">Nueva Venta</p>
                        <p class="text-xs text-emerald-100">Punto de venta</p>
                    </div>
                    <svg class="w-5 h-5 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('farmacia.inventario') }}" class="group flex items-center gap-4 p-4 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white shadow-lg shadow-blue-200 hover:shadow-xl hover:scale-[1.02] transition-all duration-200">
                    <div class="p-3 bg-white/20 rounded-xl group-hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">Inventario</p>
                        <p class="text-xs text-blue-100">Gestionar productos</p>
                    </div>
                    <svg class="w-5 h-5 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('farmacia.clientes') }}" class="group flex items-center gap-4 p-4 bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl text-white shadow-lg shadow-violet-200 hover:shadow-xl hover:scale-[1.02] transition-all duration-200">
                    <div class="p-3 bg-white/20 rounded-xl group-hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">Clientes</p>
                        <p class="text-xs text-violet-100">Administrar clientes</p>
                    </div>
                    <svg class="w-5 h-5 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('farmacia.reporte') }}" class="group flex items-center gap-4 p-4 bg-white border-2 border-slate-200 rounded-xl text-slate-700 hover:border-emerald-300 hover:bg-emerald-50 transition-all duration-200">
                    <div class="p-3 bg-slate-100 rounded-xl group-hover:bg-emerald-100 transition-colors">
                        <svg class="w-6 h-6 text-slate-600 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">Reportes</p>
                        <p class="text-xs text-slate-500">Análisis y estadísticas</p>
                    </div>
                    <svg class="w-5 h-5 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function farmaciaDashboard() {
        return {
            init() {
                this.initChart();
            },
            initChart() {
                const ctx = document.getElementById('ventasChart');
                if (!ctx) return;

                // Datos de ejemplo para el gráfico
                const labels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                const data = [1200, 1900, 1500, 2200, 2800, 3200, 2400];

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ventas ($)',
                            data: data,
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }
    }
</script>
@endsection
