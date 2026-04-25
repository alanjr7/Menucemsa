@extends('layouts.app')

@section('content')
<div class="p-8 bg-gray-50/50 min-h-screen">
    <div class="mb-6">
        <div class="flex items-center gap-3 text-blue-600 mb-1">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Control de Accesos por IP</h1>
        </div>
        <p class="text-sm text-gray-500 ml-11 font-medium">Gestione las direcciones IP permitidas para acceder al sistema</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm font-medium text-green-800">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-2 mb-6">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="font-bold text-gray-800 text-sm">Modo de Acceso</h3>
                </div>

                <form action="{{ route('seguridad.accesos.mode') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-3">
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition">
                            <input type="radio" name="mode" value="all" class="mt-0.5" {{ ($setting?->mode ?? 'all') === 'all' ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-bold text-gray-800">Aceptar todos</p>
                                <p class="text-xs text-gray-500">Cualquier IP puede acceder al sistema</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition">
                            <input type="radio" name="mode" value="specific" class="mt-0.5" {{ ($setting?->mode ?? 'all') === 'specific' ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-bold text-gray-800">Solo IPs permitidas</p>
                                <p class="text-xs text-gray-500">Solo las IPs de la lista pueden acceder</p>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-blue-700 shadow-md shadow-blue-200 transition">
                        Guardar Configuración
                    </button>
                </form>
            </div>

            <div class="bg-blue-50 rounded-2xl border border-blue-100 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="font-bold text-blue-800 text-sm">Tu IP Actual</h3>
                </div>
                <p class="text-2xl font-bold text-blue-900">{{ $currentIp }}</p>
                <p class="text-xs text-blue-600 mt-2">Esta es la dirección IP desde la que estás accediendo al sistema.</p>
            </div>

            
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                <div class="flex items-center gap-2 mb-6">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <h3 class="font-bold text-gray-800 text-sm">Agregar Nueva IP</h3>
                </div>

                <form action="{{ route('seguridad.accesos.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Dirección IP</label>
                            <input type="text" name="ip_address" placeholder="192.168.1.100 o 192.168.1.0/24" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-300">
                            <p class="text-[10px] text-gray-500 mt-1">Para rangos use formato CIDR (ej: 192.168.1.0/24)</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Tipo</label>
                            <select name="ip_type" required
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-300">
                                <option value="single">IP Individual</option>
                                <option value="range">Rango de IPs</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-800 mb-2">Descripción (opcional)</label>
                        <input type="text" name="description" placeholder="Ej: Oficina principal, WiFi clínica, etc."
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-300">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-blue-700 shadow-md shadow-blue-200 transition">
                        Agregar a Lista Blanca
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="font-bold text-gray-800 text-sm">IPs Permitidas</h3>
                    </div>
                    <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-1 rounded-lg">{{ $allowedIps->count() }} registradas</span>
                </div>

                @if($allowedIps->isEmpty())
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <p class="text-sm text-gray-500">No hay IPs registradas en la lista blanca</p>
                        <p class="text-xs text-gray-400 mt-1">Agregue IPs para restringir el acceso al sistema</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left py-3 px-3 text-[11px] font-bold text-gray-600">IP / Rango</th>
                                    <th class="text-left py-3 px-3 text-[11px] font-bold text-gray-600">Tipo</th>
                                    <th class="text-left py-3 px-3 text-[11px] font-bold text-gray-600">Descripción</th>
                                    <th class="text-left py-3 px-3 text-[11px] font-bold text-gray-600">Agregado por</th>
                                    <th class="text-center py-3 px-3 text-[11px] font-bold text-gray-600">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allowedIps as $ip)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                        <td class="py-3 px-3">
                                            <code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono">{{ $ip->ip_address }}</code>
                                        </td>
                                        <td class="py-3 px-3">
                                            @if($ip->ip_type === 'single')
                                                <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded-full">Individual</span>
                                            @else
                                                <span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-1 rounded-full">Rango</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-xs text-gray-600">{{ $ip->description ?: '-' }}</td>
                                        <td class="py-3 px-3 text-xs text-gray-600">{{ $ip->creator->name ?? 'Desconocido' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <form action="{{ route('seguridad.accesos.destroy', $ip) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta IP de la lista blanca?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
