@extends('layouts.app')

@section('content')
@php
    $verCss = @filemtime(public_path('css/cirugias-externas-admin.css')) ?: '1';
    $verJs = @filemtime(public_path('js/cirugias-externas-admin.js')) ?: '1';
    $tab0 = in_array(request('tab'), ['reservas', 'cal', 'tipos', 'qr'], true) ? request('tab') : 'reservas';
@endphp
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/cirugias-externas-admin.css') }}?v={{ $verCss }}">

<div class="cea" style="padding:24px;background:#f1f5f9;min-height:100%" x-data="{ tab: '{{ $tab0 }}' }">

    {{-- Barra: pestañas + acceso a la página pública --}}
    <div class="cea-toolbar">
        <div class="tabs" style="margin-bottom:0">
            <button :class="tab==='reservas' ? 'active' : ''" @click="tab='reservas'">
                <i class="fa-solid fa-table-list"></i> Reservas
                @if($pendientes > 0)
                    <span class="badge b-pend" style="margin-left:4px"><i class="fa-solid fa-clock"></i> {{ $pendientes }}</span>
                @endif
            </button>
            <button :class="tab==='cal' ? 'active' : ''" @click="tab='cal'">
                <i class="fa-regular fa-calendar"></i> Calendario
            </button>
            <button :class="tab==='tipos' ? 'active' : ''" @click="tab='tipos'">
                <i class="fa-solid fa-sliders"></i> Tipos y precios
            </button>
            <button :class="tab==='qr' ? 'active' : ''" @click="tab='qr'">
                <i class="fa-solid fa-qrcode"></i> QR
            </button>
        </div>
        <a href="{{ route('cirugias-externas.public.create') }}" target="_blank" rel="noopener" class="btn-open">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Ir a la página pública
        </a>
    </div>

    {{-- ===== RESERVAS ===== --}}
    <section class="card" x-show="tab==='reservas'">
        <div class="panel-head">
            <div class="ph-icon blue"><i class="fa-solid fa-table-list"></i></div>
            <div>
                <h1>Reservas registradas</h1>
                <p>Verifique pagos y rechace reservas sin comprobante.</p>
            </div>
        </div>
        <div class="panel-body" style="padding-top:16px">
            {{-- Búsqueda y filtros --}}
            <form method="GET" class="cea-filtros">
                <div class="fgroup fbusca">
                    <label>Buscar</label>
                    <input type="text" name="buscar" value="{{ $filtros['buscar'] }}" placeholder="Código, cirujano, paciente, correo o teléfono…">
                </div>
                <div class="fgroup">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="pendiente" @selected($filtros['estado']==='pendiente')>Pendiente</option>
                        <option value="pagado" @selected($filtros['estado']==='pagado')>Pagado</option>
                        <option value="rechazado" @selected($filtros['estado']==='rechazado')>Rechazado</option>
                    </select>
                </div>
                <div class="fgroup">
                    <label>Desde</label>
                    <input type="date" name="desde" value="{{ $filtros['desde'] }}">
                </div>
                <div class="fgroup">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="{{ $filtros['hasta'] }}">
                </div>
                <button type="submit" class="btn-filtro"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
                @if($filtros['buscar'] !== '' || $filtros['estado'] !== '' || $filtros['desde'] !== '' || $filtros['hasta'] !== '')
                    <a href="{{ route('admin.cirugias-externas.index') }}" class="btn-limpiar">Limpiar</a>
                @endif
            </form>

            <div class="scrollx">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Código</th><th>Cirujano</th><th>Paciente</th><th>Cirugía</th><th>Quirófano</th>
                            <th>Fecha · Hora</th><th>Monto</th><th>Estado</th><th>Recibo</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cirugias as $r)
                            <tr>
                                <td><span class="codigo">{{ $r->codigo }}</span></td>
                                <td><b>Dr. {{ $r->cirujano_nombre }}</b><br><span class="hint">{{ $r->cirujano_telefono }} · {{ $r->cirujano_email }}</span></td>
                                <td>{{ $r->paciente_nombre }}</td>
                                <td>{{ $r->cirugia_nombre ?: ($r->tipo->nombre ?? '—') }}</td>
                                <td><b>Q{{ $r->quirofano_id }}</b></td>
                                <td>{{ $r->fecha->format('Y-m-d') }}<br><span class="hint">{{ \Illuminate\Support\Str::of($r->hora_inicio)->substr(0,5) }}–{{ \Illuminate\Support\Str::of($r->hora_fin)->substr(0,5) }}</span></td>
                                <td style="font-variant-numeric:tabular-nums">Bs {{ $r->precio_final }}
                                    @if($r->es_nocturno)
                                        <br><span style="font-size:11px;color:var(--green-dk)"><i class="fa-solid fa-moon"></i> Desc. -Bs {{ $r->descuento }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $b = match($r->estado) {
                                            'pagado' => ['b-pago', 'fa-circle-check', 'Pagado'],
                                            'rechazado' => ['b-rech', 'fa-ban', 'Rechazado'],
                                            default => ['b-pend', 'fa-clock', 'Pendiente'],
                                        };
                                    @endphp
                                    <span class="badge {{ $b[0] }}"><i class="fa-solid {{ $b[1] }}"></i> {{ $b[2] }}</span>
                                </td>
                                <td>
                                    @if($r->recibo_path)
                                        <img src="{{ route('admin.cirugias-externas.recibo-thumb', $r) }}" class="recibo-mini" alt="Recibo"
                                             width="40" height="40" loading="lazy" decoding="async"
                                             title="Ver grande" onclick="CEA.verRecibo('{{ route('admin.cirugias-externas.recibo', $r) }}')">
                                    @else
                                        <span class="hint">Sin recibo</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap">
                                    @if($r->estado === 'pendiente')
                                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                                            <form method="POST" action="{{ route('admin.cirugias-externas.verificar-pago', $r) }}" onsubmit="return confirm('¿Confirmar que el pago fue verificado?')">
                                                @csrf
                                                <button type="submit" class="btn-success"><i class="fa-solid fa-check"></i> Verificar pago</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.cirugias-externas.rechazar', $r) }}" id="rechForm{{ $r->id }}">
                                                @csrf
                                                <input type="hidden" name="motivo_rechazo">
                                            </form>
                                            <button type="button" class="btn-danger" onclick="CEA.rechazar({{ $r->id }})"><i class="fa-solid fa-ban"></i> Rechazar</button>
                                        </div>
                                    @elseif($r->estado === 'rechazado' && $r->motivo_rechazo)
                                        <span class="hint" title="{{ $r->motivo_rechazo }}">Rechazada</span>
                                    @else
                                        <span class="hint">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty"><i class="fa-regular fa-calendar-xmark"></i> No hay reservas registradas.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($cirugias->hasPages())
                <div class="cea-pager">
                    <span class="hint">Mostrando {{ $cirugias->firstItem() }}–{{ $cirugias->lastItem() }} de {{ $cirugias->total() }}</span>
                    <div class="cea-pager-btns">
                        <a class="pg {{ $cirugias->onFirstPage() ? 'dis' : '' }}" href="{{ $cirugias->previousPageUrl() ?: '#' }}"><i class="fa-solid fa-chevron-left"></i> Anterior</a>
                        <span class="pg-cur">Página {{ $cirugias->currentPage() }} de {{ $cirugias->lastPage() }}</span>
                        <a class="pg {{ ! $cirugias->hasMorePages() ? 'dis' : '' }}" href="{{ $cirugias->nextPageUrl() ?: '#' }}">Siguiente <i class="fa-solid fa-chevron-right"></i></a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== CALENDARIO ===== --}}
    <section class="card" x-show="tab==='cal'" style="display:none">
        <div class="panel-head">
            <div class="ph-icon navy"><i class="fa-regular fa-calendar"></i></div>
            <div>
                <h1>Calendario de cirugías</h1>
                <p>Visualice todas las reservas externas por día.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="month-cal-head">
                <h2 id="calTitulo"></h2>
                <div class="month-nav">
                    <button type="button" onclick="CEA.navCal(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" class="hoy" onclick="CEA.navCal(0)">Hoy</button>
                    <button type="button" onclick="CEA.navCal(1)"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="month-grid" id="calGrid"></div>
            <div class="cal-legend">
                <span><span class="leg-dot green"></span>Pagado</span>
                <span><span class="leg-dot amber"></span>Pendiente de pago</span>
            </div>
            <div class="cal-det hidden" id="calDet"></div>
        </div>
    </section>

    {{-- ===== TIPOS Y PRECIOS ===== --}}
    <section class="card" x-show="tab==='tipos'" style="display:none">
        <div class="panel-head">
            <div class="ph-icon navy"><i class="fa-solid fa-sliders"></i></div>
            <div>
                <h1>Tipos y precios</h1>
                <p>Defina el precio y la duración de cada tipo de cirugía. Se guarda al salir del campo.</p>
            </div>
        </div>
        <div class="panel-body" style="padding-top:0">
            <div class="scrollx">
                <table class="tabla">
                    <thead>
                        <tr><th>Tipo de cirugía</th><th>Precio (Bs)</th><th>Duración (min)</th></tr>
                    </thead>
                    <tbody>
                        @foreach($tipos as $t)
                            <tr data-tipo="{{ $t->id }}" data-nombre="{{ e($t->nombre) }}" data-activo="{{ $t->activo ? 1 : 0 }}">
                                <td>
                                    <b>{{ $t->nombre }}</b>
                                    @if($t->descripcion)<br><span class="hint">{{ $t->descripcion }}</span>@endif
                                    @if($t->incluye)<br><span class="hint" style="color:var(--green-dk)"><i class="fa-solid fa-circle-check"></i> {{ implode(', ', $t->incluye) }}</span>@endif
                                    @if($t->no_incluye)<br><span class="hint" style="color:var(--amber)">No incluye: {{ implode(', ', $t->no_incluye) }}</span>@endif
                                </td>
                                <td><input class="admin-input" style="width:110px" type="text" inputmode="decimal" data-decimal data-field="precio" value="{{ $t->precio }}" onchange="CEA.guardarTipo({{ $t->id }})"></td>
                                <td><input class="admin-input" style="width:90px" type="number" min="1" step="1" data-field="duracion" value="{{ $t->duracion_minutos }}" onchange="CEA.guardarTipo({{ $t->id }})"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ===== QR DE PAGO ===== --}}
    <section class="card" x-show="tab==='qr'" style="display:none">
        <div class="panel-head">
            <div class="ph-icon navy"><i class="fa-solid fa-qrcode"></i></div>
            <div>
                <h1>QR de pago</h1>
                <p>Esta imagen se muestra a los cirujanos en la página pública para que realicen el pago.</p>
            </div>
        </div>
        <div class="panel-body">
            <div style="display:flex;gap:24px;flex-wrap:wrap;align-items:flex-start">
                {{-- Vista previa actual --}}
                <div style="text-align:center">
                    <div style="width:200px;height:200px;border:2px dashed var(--line);border-radius:12px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden">
                        @if($qrUrl)
                            <img src="{{ $qrUrl }}" alt="QR de pago actual" style="max-width:100%;max-height:100%">
                        @else
                            <span class="hint">Sin QR cargado</span>
                        @endif
                    </div>
                    <p class="hint" style="margin-top:8px">{{ $qrUrl ? 'QR actual (visible en la página pública)' : 'La página pública muestra un marcador por defecto' }}</p>
                </div>

                {{-- Cargar / quitar --}}
                <div style="flex:1;min-width:260px">
                    <form method="POST" action="{{ route('admin.cirugias-externas.qr.update') }}" enctype="multipart/form-data">
                        @csrf
                        <label class="hint" style="display:block;margin-bottom:6px;font-weight:600">Subir nueva imagen del QR (PNG o JPG)</label>
                        <input type="file" name="qr" accept="image/*" required style="display:block;margin-bottom:12px;font-size:13px">
                        <button type="submit" class="btn-filtro"><i class="fa-solid fa-upload"></i> Guardar QR</button>
                    </form>

                    @if($qrUrl)
                        <form method="POST" action="{{ route('admin.cirugias-externas.qr.destroy') }}" style="margin-top:12px"
                              onsubmit="return confirm('¿Quitar el QR actual? La página pública mostrará el marcador por defecto.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger"><i class="fa-solid fa-trash"></i> Quitar QR</button>
                        </form>
                    @endif

                    <p class="hint" style="margin-top:16px"><i class="fa-solid fa-circle-info"></i> El QR se normaliza a PNG con fondo blanco (máx. 800px) para que siempre escanee. Se guarda en una sola ubicación y se refleja de inmediato en la página pública.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal recibo + toast --}}
    <div class="cea-modal" id="ceaModal" onclick="CEA.cerrarModal()">
        <img id="ceaModalImg" alt="Recibo ampliado">
    </div>
    <div class="cea-toast" id="ceaToast"><i class="fa-solid fa-circle-check"></i> <span id="ceaToastMsg"></span></div>
</div>

<script>
    window.CEA_CONFIG = {
        csrf: @json(csrf_token()),
        tiposBase: @json(url('/admin/tipos-cirugia-externa')),
        flash: @json(session('success') ?? session('error')),
        reservas: @json($reservasCalendario),
    };
</script>
<script src="{{ asset('js/cirugias-externas-admin.js') }}?v={{ $verJs }}"></script>
@endsection
