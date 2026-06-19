@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Correcciones de Cuenta</h1>
            <p class="text-sm text-gray-500">{{ $paciente->nombre }} — CI: {{ $paciente->ci ?? $paciente->temp_code }}</p>
        </div>
        <a href="{{ route('admin.ajustes-pacientes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Patient Info Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $paciente->nombre }}</h3>
                    <p class="text-sm text-gray-500">CI: {{ $paciente->ci ?? $paciente->temp_code }} | {{ $paciente->seguro->nombre_empresa ?? 'Particular' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Total de Cuentas</p>
                <p class="text-2xl font-bold text-gray-800">{{ $cuentas->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Accounts List -->
    @forelse($cuentas as $cuenta)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <!-- Account Header -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Cuenta #{{ $cuenta->id }}</h3>
                        <p class="text-sm text-gray-500">Creada: {{ $cuenta->created_at->format('d/m/Y H:i') }} · {{ $cuenta->tipo_atencion_label }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($cuenta->estado === 'pagado') bg-green-100 text-green-800 border border-green-200
                        @elseif($cuenta->estado === 'parcial') bg-yellow-100 text-yellow-800 border border-yellow-200
                        @else bg-red-100 text-red-800 border border-red-200 @endif">
                        {{ $cuenta->estado_label }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <!-- Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="text-xl font-bold text-gray-800">Bs. {{ number_format($cuenta->total_calculado, 2) }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500">Pagado</p>
                        <p class="text-xl font-bold text-green-600">Bs. {{ number_format($cuenta->pagos->sum('monto'), 2) }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500">Pendiente</p>
                        <p class="text-xl font-bold text-red-600">Bs. {{ number_format($cuenta->saldo_pendiente, 2) }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <p class="text-sm text-gray-500">Cargos activos</p>
                        <p class="text-xl font-bold text-blue-600">{{ $cuenta->detalles->whereNull('deshabilitado_en')->count() }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Unit.</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad a anular</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($cuenta->detalles as $detalle)
                                @php
                                    $off = $detalle->deshabilitado_en !== null;
                                    $liquidado = $detalle->liquidado_en !== null;
                                    $anulaciones = $detalle->anulaciones ?? collect();
                                @endphp
                                <tr class="{{ $off ? 'bg-gray-100 text-gray-400' : 'hover:bg-gray-50' }}">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm {{ $off ? 'text-gray-400' : 'text-gray-500' }}">
                                        {{ $detalle->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm {{ $off ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                        {{ $detalle->descripcion }}
                                        @if($off)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-200 text-gray-600 no-underline">Anulado</span>
                                        @elseif($liquidado)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-700">Pagado</span>
                                        @else
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-700">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm {{ $off ? 'text-gray-400' : 'text-gray-500' }}">{{ rtrim(rtrim(number_format($detalle->cantidad, 2), '0'), '.') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm {{ $off ? 'text-gray-400' : 'text-gray-500' }}">Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium {{ $off ? 'text-gray-400' : 'text-gray-900' }}">Bs. {{ number_format($detalle->subtotal, 2) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                        @if($liquidado && !$off)
                                            <span class="text-[11px] text-gray-400">Cargo pagado</span>
                                        @elseif(!$off)
                                            {{-- Anular N unidades (parcial o total). Default = cantidad viva. --}}
                                            <form action="{{ route('admin.cargos.anular', $detalle->id) }}" method="POST"
                                                  onsubmit="return prepararAnulacion(this)"
                                                  class="flex items-center justify-end gap-1.5">
                                                @csrf
                                                <input type="number" name="cantidad"
                                                       value="{{ rtrim(rtrim(number_format($detalle->cantidad, 2), '0'), '.') }}"
                                                       step="1" min="1" max="{{ $detalle->cantidad }}"
                                                       title="Cantidad a anular (máx. {{ rtrim(rtrim(number_format($detalle->cantidad, 2), '0'), '.') }})"
                                                       class="w-16 px-2 py-1 border border-gray-200 rounded-lg bg-gray-50 text-right text-xs focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                                <input type="hidden" name="motivo">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-amber-200 shadow-sm text-xs font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition-all">
                                                    Anular
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-gray-400">Anulado totalmente</span>
                                        @endif
                                    </td>
                                </tr>
                                {{-- Historial de anulaciones de esta línea (con Revertir) --}}
                                @foreach($anulaciones as $anul)
                                    <tr class="bg-gray-50/60 text-xs">
                                        <td class="px-4 py-1.5 text-gray-400">{{ $anul->eliminado_en?->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-1.5 text-gray-500" colspan="3">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 font-medium mr-1">
                                                Anuladas {{ rtrim(rtrim(number_format($anul->cantidad, 2), '0'), '.') }} u.
                                            </span>
                                            <span class="text-gray-400">{{ $anul->motivo_eliminacion }}</span>
                                            <span class="text-gray-300">· {{ $anul->usuarioEliminacion?->name ?? 'N/A' }}</span>
                                            @if($anul->revertido_en)
                                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded bg-blue-50 text-blue-600">Revertida {{ $anul->revertido_en->format('d/m/Y H:i') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-1.5 text-right text-gray-500 font-medium">Bs. {{ number_format($anul->subtotal, 2) }}</td>
                                        <td class="px-4 py-1.5 text-right">
                                            @if(!$anul->revertido_en && !$liquidado)
                                                <form action="{{ route('admin.anulaciones.revertir', $anul->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 border border-blue-200 text-[11px] font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 transition-all">
                                                        Revertir
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">No hay cargos en esta cuenta.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Add Charge Form -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Agregar cargo</h4>
                    <form action="{{ route('admin.ajustes-pacientes.cargos.store', $cuenta->id) }}" method="POST"
                          class="js-cargo-form grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        @csrf

                        {{-- Buscador de catálogo --}}
                        <div class="md:col-span-12 relative">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Buscar en catálogo
                                <span class="text-gray-400 font-normal">(opcional — autocompleta concepto, tipo, código y precio)</span>
                            </label>
                            <input type="text" autocomplete="off"
                                   class="js-buscar block w-full px-3 py-2 border border-gray-200 rounded-lg bg-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm"
                                   data-url="{{ route('admin.ajustes-pacientes.buscar-catalogo') }}"
                                   placeholder="Ej. paracetamol, hemograma, cirugía…">
                            <div class="js-resultados absolute z-20 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden"></div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Fecha</label>
                            <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" required
                                   class="block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tipo</label>
                            <select name="tipo_item" class="js-tipo block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
                                <option value="servicio">Servicio</option>
                                <option value="material">Bien</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Concepto</label>
                            <input type="text" name="descripcion" required maxlength="255" placeholder="Ej. Servicio adicional"
                                   class="js-descripcion block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Código
                                <span class="text-gray-400 font-normal">(auto si vacío)</span>
                            </label>
                            <input type="text" name="codigo_item" maxlength="12" placeholder="—"
                                   class="js-codigo block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 font-mono text-xs focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cant.</label>
                            <input type="number" name="cantidad" value="1" step="1" min="1" required
                                   class="block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Monto</label>
                            <input type="number" name="monto" step="0.01" min="0" required placeholder="0.00"
                                   class="js-monto block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-transparent mb-1">·</label>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition-colors shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-600 mb-2">No hay cuentas</h3>
            <p class="text-gray-400">Este paciente no tiene cuentas registradas; no es posible agregar cargos.</p>
        </div>
    @endforelse

</div>

<script>
    // Valida la cantidad a anular y pide el motivo (obligatorio) antes de enviar.
    function prepararAnulacion(form) {
        const input = form.querySelector('input[name=cantidad]');
        const max = parseFloat(input.max);
        const cant = parseFloat(input.value);
        if (isNaN(cant) || cant <= 0) {
            alert('Ingresá una cantidad válida a anular.');
            return false;
        }
        if (cant > max) {
            alert('No podés anular más de ' + max + ' unidades de este cargo.');
            return false;
        }
        const esTotal = cant >= max;
        const motivo = prompt('Motivo de la anulación' + (esTotal ? ' total' : ' de ' + cant + ' u.') + ':');
        if (!motivo || !motivo.trim()) return false;
        form.motivo.value = motivo.trim();
        return true;
    }

    // ── Buscador de catálogo en el formulario "Agregar cargo" ──────────────
    // Autocompleta concepto + tipo + código + precio. Tras elegir, todos los
    // campos quedan EDITABLES y el CÓDIGO es independiente del nombre: podés
    // "tomar prestado" un código de catálogo (familia 1/2/5/6) y cambiar texto
    // y precio → el comprobante muestra ese código + tu texto + tu precio.
    // Solo si dejás el código VACÍO (concepto nuevo sin elegir) → diccionario 9.
    const BIENES = ['medicamento', 'farmacia', 'material', 'equipo_medico'];

    document.querySelectorAll('.js-cargo-form').forEach(function (form) {
        const buscar      = form.querySelector('.js-buscar');
        const resultados  = form.querySelector('.js-resultados');
        const descripcion = form.querySelector('.js-descripcion');
        const monto       = form.querySelector('.js-monto');
        const codigo      = form.querySelector('.js-codigo');
        const tipo        = form.querySelector('.js-tipo');
        if (!buscar) return;

        let timer = null, ultimo = '';

        const cerrar = function () { resultados.classList.add('hidden'); resultados.innerHTML = ''; };

        const elegir = function (item) {
            descripcion.value = item.descripcion;
            codigo.value = item.codigo || '';
            tipo.value = BIENES.indexOf(item.tipo_item) !== -1 ? 'material' : 'servicio';
            if (item.precio !== null && item.precio !== undefined && item.precio !== '') {
                monto.value = parseFloat(item.precio).toFixed(2);
            }
            buscar.value = '';
            cerrar();
            monto.focus();
        };

        const render = function (items) {
            if (!items.length) { resultados.innerHTML = '<div class="px-3 py-2 text-sm text-gray-400">Sin coincidencias</div>'; resultados.classList.remove('hidden'); return; }
            resultados.innerHTML = '';
            items.forEach(function (item) {
                const row = document.createElement('button');
                row.type = 'button';
                row.className = 'w-full text-left px-3 py-2 hover:bg-purple-50 flex items-center justify-between gap-2 border-b border-gray-50 last:border-0';
                row.innerHTML =
                    '<span class="text-sm text-gray-800">' + item.descripcion +
                    ' <span class="text-[10px] text-gray-400">' + (item.grupo || '') + '</span></span>' +
                    '<span class="text-[11px] font-mono text-purple-600 whitespace-nowrap">' + (item.codigo || '') +
                    (item.precio ? ' · Bs ' + parseFloat(item.precio).toFixed(2) : '') + '</span>';
                row.addEventListener('click', function () { elegir(item); });
                resultados.appendChild(row);
            });
            resultados.classList.remove('hidden');
        };

        buscar.addEventListener('input', function () {
            const q = buscar.value.trim();
            if (q === ultimo) return;
            ultimo = q;
            clearTimeout(timer);
            if (q.length < 2) { cerrar(); return; }
            timer = setTimeout(function () {
                fetch(buscar.dataset.url + '?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(render)
                    .catch(cerrar);
            }, 250);
        });

        // Cerrar el dropdown al hacer clic fuera.
        document.addEventListener('click', function (e) {
            if (!form.contains(e.target)) cerrar();
        });
    });
</script>
@endsection
