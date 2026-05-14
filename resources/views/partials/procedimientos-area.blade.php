{{--
  Partial SIN @extends — se incluye desde cada vista de área.
  Variables: $procedimientos, $area_label, $back_route, $btn_class, $accent_class, $ring_class
--}}
<div class="p-6 bg-gray-50/50 min-h-screen" x-data="procedimientosArea()" x-cloak>

    {{-- CABECERA --}}
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Procedimientos — {{ $area_label }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Procedimientos disponibles para el área · Solo lectura</p>
        </div>
        <div class="flex gap-2">
            @if(in_array(auth()->user()->role, ['admin','administrador']))
                <a href="{{ route('admin.procedimientos.create') }}"
                   class="px-4 py-2 text-white rounded-xl text-sm font-medium shadow-sm transition-colors {{ $btn_class }}">
                    + Nuevo procedimiento
                </a>
            @else
                <a href="{{ route($back_route) }}"
                   class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    ← Volver
                </a>
            @endif
        </div>
    </div>

    {{-- TARJETA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap gap-3 justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">
                {{ $procedimientos->total() }} procedimiento(s)
            </h3>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" x-model="busqueda" placeholder="Buscar procedimiento..."
                       class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl w-64 focus:outline-none focus:ring-2 {{ $ring_class }}"/>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Descripción</th>
                        @if(in_array(auth()->user()->role, ['admin','administrador']))
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Precio</th>
                        @endif
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($procedimientos as $proc)
                    <tr class="hover:bg-gray-50/40 transition-colors"
                        x-show="coincide({{ Js::from(['n' => $proc->nombre, 'd' => $proc->descripcion ?? '']) }})">
                        <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ $proc->nombre }}</td>
                        <td class="px-6 py-3 text-sm text-gray-500 max-w-xs">
                            <span class="line-clamp-2">{{ $proc->descripcion ?? '—' }}</span>
                        </td>
                        @if(in_array(auth()->user()->role, ['admin','administrador']))
                        <td class="px-6 py-3 text-sm text-right font-semibold text-gray-800 whitespace-nowrap">
                            Bs. {{ number_format($proc->precio, 2) }}
                        </td>
                        @endif
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $proc->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $proc->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="verDetalle({{ Js::from([
                                        'nombre'      => $proc->nombre,
                                        'descripcion' => $proc->descripcion ?? '',
                                        'precio'      => number_format($proc->precio, 2),
                                        'activo'      => $proc->activo,
                                    ]) }})"
                                    class="px-3 py-1 text-xs border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                                    Ver detalle
                                </button>
                                @if(in_array(auth()->user()->role, ['admin','administrador']))
                                    <a href="{{ route('admin.procedimientos.edit', $proc->id) }}"
                                       class="px-3 py-1 text-xs border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                                        Editar
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Sin procedimientos registrados para esta área.
                            @if(in_array(auth()->user()->role, ['admin','administrador']))
                                <a href="{{ route('admin.procedimientos.create') }}" class="text-blue-600 hover:underline ml-1">Crear uno</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-show="busqueda && sinResultados" class="px-6 py-8 text-center text-gray-400 text-sm border-t border-gray-100">
            Ningún procedimiento coincide con "<span class="font-medium text-gray-600" x-text="busqueda"></span>"
        </div>

        @if($procedimientos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $procedimientos->links() }}</div>
        @endif
    </div>

    {{-- MODAL DETALLE --}}
    <template x-teleport="body">
        <div x-show="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="modal = false" style="display:none;">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="modal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-7"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <button @click="modal = false"
                        class="absolute top-4 right-4 p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="flex items-start gap-3 mb-5">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg leading-tight" x-text="detalle.nombre"></h3>
                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium"
                              :class="detalle.activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                              x-text="detalle.activo ? 'Activo' : 'Inactivo'">
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Descripción</p>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line"
                           x-text="detalle.descripcion || 'Sin descripción registrada.'"></p>
                    </div>
                    @if(in_array(auth()->user()->role, ['admin','administrador']))
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Precio</p>
                        <p class="text-2xl font-black text-gray-800">Bs. <span x-text="detalle.precio"></span></p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="modal = false"
                            class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function procedimientosArea() {
    return {
        busqueda: '',
        modal: false,
        detalle: {},
        get sinResultados() {
            return this.busqueda.length > 0;
        },
        coincide(proc) {
            if (!this.busqueda) return true;
            const q = this.busqueda.toLowerCase();
            return (proc.n || '').toLowerCase().includes(q)
                || (proc.d || '').toLowerCase().includes(q);
        },
        verDetalle(proc) {
            this.detalle = proc;
            this.modal = true;
        },
    };
}
</script>
