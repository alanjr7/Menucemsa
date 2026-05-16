@extends('layouts.app')

@section('content')

@php
$preciosCamillas = $camillas->pluck('precio_por_hora', 'id')->toArray();

$camillasData = $camillas->map(fn($c) => [
    'id'             => $c->id,
    'nombre'         => $c->nombre,
    'codigo'         => $c->codigo,
    'precio_por_hora'=> (float) $c->precio_por_hora,
])->values();

$INGRESO = [
    'enfermeria'       => ['color' => 'purple', 'label' => 'Enfermería'],
    'consulta_externa' => ['color' => 'green',  'label' => 'Consulta'],
    'emergencia'       => ['color' => 'red',    'label' => 'Emergencia'],
    'internacion'      => ['color' => 'yellow', 'label' => 'Internación'],
];

$pacientesData = $pacientes->map(function($paciente) use ($INGRESO) {
    $esTemporal  = isset($paciente->is_temporal) && $paciente->is_temporal;
    $tipoIngreso = $paciente->tipo_ingreso ?? 'otro';
    $cajaId      = !$esTemporal ? ($paciente->consultas->first()?->caja?->id) : null;

    return [
        'ci'            => $paciente->ci,
        'nombre'        => $paciente->nombre,
        'is_temporal'   => $esTemporal,
        'codigo'        => $esTemporal ? ($paciente->emergency_code ?? null) : ($cajaId ?? ($paciente->registro_codigo ?? null)),
        'emergency_code'=> $paciente->emergency_code ?? null,
        'seguro'        => $esTemporal ? null : ($paciente->seguro->nombre_empresa ?? 'Particular'),
        'tipo_ingreso'  => $tipoIngreso,
        'ingreso_color' => $INGRESO[$tipoIngreso]['color'] ?? 'gray',
        'ingreso_label' => $INGRESO[$tipoIngreso]['label'] ?? 'Otro',
    ];
})->values();

$appData = [
    'pacientes'    => $pacientesData,
    'total'        => $pacientes->total(),
    'camillas'     => $camillasData,
    'camillasVacias' => $camillas->isEmpty(),
    'pagination'   => [
        'hasPages'    => $pacientes->hasPages(),
        'currentPage' => $pacientes->currentPage(),
        'lastPage'    => $pacientes->lastPage(),
        'from'        => $pacientes->firstItem(),
        'to'          => $pacientes->lastItem(),
        'prevUrl'     => $pacientes->appends(request()->query())->previousPageUrl(),
        'nextUrl'     => $pacientes->appends(request()->query())->nextPageUrl(),
    ],
    'flash'        => [
        'success' => session('success'),
        'error'   => session('error'),
        'info'    => session('info'),
    ],
    'csrfToken'    => csrf_token(),
    'storeUrl'     => route('emergency-staff.camillas.store'),
    'indexUrl'     => route('emergency-staff.camillas.index'),
    'dashboardUrl' => route('emergency-staff.dashboard'),
    'currentSearch'=> request('search', ''),
];
@endphp

<div id="camillas-root"></div>

<script>
window.__CAMILLAS_DATA__ = @json($appData);
</script>

@endsection

@push('scripts')
<script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
<script type="text/babel">
const { useState, useEffect, useMemo } = React;

function Pagination({ pagination, total, indexUrl, currentSearch }) {
    function pageUrl(n) {
        const params = new URLSearchParams();
        if (currentSearch) params.set('search', currentSearch);
        if (n > 1) params.set('page', n);
        const qs = params.toString();
        return indexUrl + (qs ? '?' + qs : '');
    }

    function getPages(current, last) {
        const delta = 2;
        const range = [];
        for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
            range.push(i);
        }
        if (current - delta > 2) range.unshift('…');
        if (current + delta < last - 1) range.push('…');
        if (last > 1) range.unshift(1);
        if (last >= 1) range.push(last);
        return [...new Set(range)];
    }

    const { currentPage, lastPage, from, to, prevUrl, nextUrl } = pagination;
    const pages = getPages(currentPage, lastPage);
    const btnBase = 'inline-flex items-center justify-center min-w-[2rem] h-8 px-2 text-sm rounded-lg border transition-colors';

    return (
        <div className="px-6 py-4 border-t border-gray-100 bg-gray-50/30 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p className="text-sm text-gray-500">
                Mostrando {from}–{to} de {total} pacientes
            </p>
            <div className="flex items-center gap-1">
                <a href={prevUrl || '#'}
                   onClick={!prevUrl ? e => e.preventDefault() : undefined}
                   className={`${btnBase} ${prevUrl ? 'border-gray-200 text-gray-600 bg-white hover:bg-gray-50' : 'border-gray-100 text-gray-300 bg-white cursor-not-allowed'}`}>
                    ←
                </a>

                {pages.map((p, i) =>
                    p === '…' ? (
                        <span key={`ellipsis-${i}`} className="px-1 text-sm text-gray-400">…</span>
                    ) : (
                        <a key={p} href={pageUrl(p)}
                           className={`${btnBase} ${p === currentPage
                               ? 'border-blue-500 bg-blue-600 text-white'
                               : 'border-gray-200 text-gray-600 bg-white hover:bg-gray-50'}`}>
                            {p}
                        </a>
                    )
                )}

                <a href={nextUrl || '#'}
                   onClick={!nextUrl ? e => e.preventDefault() : undefined}
                   className={`${btnBase} ${nextUrl ? 'border-gray-200 text-gray-600 bg-white hover:bg-gray-50' : 'border-gray-100 text-gray-300 bg-white cursor-not-allowed'}`}>
                    →
                </a>
            </div>
        </div>
    );
}

const INGRESO_STYLES = {
    enfermeria:       { badge: 'bg-purple-100 text-purple-800 border-purple-200', dot: 'bg-purple-500', pulse: false },
    consulta_externa: { badge: 'bg-green-100 text-green-800 border-green-200',   dot: 'bg-green-500',  pulse: false },
    emergencia:       { badge: 'bg-red-100 text-red-800 border-red-200',         dot: 'bg-red-500',    pulse: true  },
    internacion:      { badge: 'bg-yellow-100 text-yellow-800 border-yellow-200',dot: 'bg-yellow-500', pulse: false },
};
const DEFAULT_STYLE = { badge: 'bg-gray-100 text-gray-800 border-gray-200', dot: 'bg-gray-500', pulse: false };

function Flash({ flash }) {
    if (!flash.success && !flash.error && !flash.info) return null;
    return (
        <div className="mb-4 space-y-2">
            {flash.success && <div className="rounded-lg bg-green-100 px-4 py-3 text-green-800 text-sm">{flash.success}</div>}
            {flash.error   && <div className="rounded-lg bg-red-100 px-4 py-3 text-red-800 text-sm">{flash.error}</div>}
            {flash.info    && <div className="rounded-lg bg-blue-100 px-4 py-3 text-blue-800 text-sm">{flash.info}</div>}
        </div>
    );
}

function CamillaModal({ paciente, camillas, csrfToken, storeUrl, onClose }) {
    const [camillaId, setCamillaId]     = useState('');
    const [fechaInicio, setFechaInicio] = useState('');
    const [fechaFin, setFechaFin]       = useState('');

    const precioHora = useMemo(() => {
        const c = camillas.find(c => String(c.id) === String(camillaId));
        return c ? parseFloat(c.precio_por_hora) || 0 : 0;
    }, [camillaId, camillas]);

    const horas = useMemo(() => {
        if (!fechaInicio || !fechaFin) return 0;
        const diff = (new Date(fechaFin) - new Date(fechaInicio)) / 3600000;
        return diff > 0 ? Math.max(0.5, Math.round(diff * 100) / 100) : 0;
    }, [fechaInicio, fechaFin]);

    useEffect(() => {
        const handler = (e) => { if (e.key === 'Escape') onClose(); };
        document.addEventListener('keydown', handler);
        return () => document.removeEventListener('keydown', handler);
    }, [onClose]);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             onClick={onClose}>
            <div className="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6"
                 onClick={e => e.stopPropagation()}>
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-base font-semibold text-gray-800">Registrar uso de camilla</h3>
                    <button onClick={onClose} className="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <p className="text-sm text-gray-500 mb-4">
                    Paciente: <strong>{paciente.nombre}</strong> ({paciente.ci})
                </p>

                <form method="POST" action={storeUrl}>
                    <input type="hidden" name="_token" value={csrfToken} />
                    <input type="hidden" name="paciente_id" value={paciente.id} />

                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Camilla <span className="text-red-500">*</span>
                            </label>
                            <select name="camilla_id" value={camillaId}
                                    onChange={e => setCamillaId(e.target.value)} required
                                    className="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="">Seleccionar camilla...</option>
                                {camillas.map(c => (
                                    <option key={c.id} value={c.id}>
                                        {c.nombre} ({c.codigo})
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Fecha y hora de inicio <span className="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="fecha_inicio" value={fechaInicio}
                                   onChange={e => setFechaInicio(e.target.value)} required
                                   className="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Fecha y hora de fin <span className="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="fecha_fin" value={fechaFin}
                                   onChange={e => setFechaFin(e.target.value)} required
                                   className="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        {horas > 0 && (
                            <div className="rounded-lg bg-blue-50 border border-blue-100 px-4 py-3 text-sm">
                                <div className="flex justify-between text-blue-800">
                                    <span>Horas: <strong>{horas}</strong></span>
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="mt-6 flex gap-3 justify-end">
                        <button type="button" onClick={onClose}
                                className="px-4 py-2 border rounded-lg text-sm text-gray-600">
                            Cancelar
                        </button>
                        <button type="submit"
                                className="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm hover:bg-orange-700">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

function PatientRow({ p, camillasVacias, onOpenModal }) {
    const style = p.is_temporal
        ? { badge: 'bg-red-100 text-red-800 border-red-200', dot: 'bg-red-500', pulse: true }
        : (INGRESO_STYLES[p.tipo_ingreso] || DEFAULT_STYLE);

    return (
        <tr className={`hover:bg-gray-50/50 transition-colors${p.is_temporal ? ' bg-red-50/30' : ''}`}>
            <td className="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                {p.is_temporal
                    ? <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{p.emergency_code}</span>
                    : p.codigo}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                <div className="flex items-center">
                    {p.is_temporal && <span className="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></span>}
                    <span className={p.is_temporal ? 'font-medium text-red-700' : ''}>{p.nombre}</span>
                </div>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{p.ci}</td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {p.is_temporal
                    ? <span className="text-xs text-red-500">Emergencia temporal</span>
                    : p.seguro}
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${style.badge}`}>
                    <span className={`w-1.5 h-1.5 rounded-full mr-1.5 ${style.dot}${style.pulse ? ' animate-pulse' : ''}`}></span>
                    {p.ingreso_label}
                </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-right">
                {camillasVacias ? (
                    <span className="text-xs text-gray-400">Sin camillas activas</span>
                ) : p.is_temporal ? (
                    <span className="text-xs text-gray-400 cursor-not-allowed" title="Completar datos del paciente primero">Sin CI registrado</span>
                ) : (
                    <button onClick={() => onOpenModal(p)}
                            className="inline-flex items-center px-3 py-1.5 border border-orange-200 shadow-sm text-xs font-medium rounded-lg text-orange-700 bg-orange-50 hover:bg-orange-100 transition-all">
                        Registrar uso de camilla
                    </button>
                )}
            </td>
        </tr>
    );
}

function CamillasIndex() {
    const {
        pacientes, total, camillas, camillasVacias,
        pagination, flash,
        csrfToken, storeUrl, indexUrl, dashboardUrl,
        currentSearch,
    } = window.__CAMILLAS_DATA__;

    const [search, setSearch]             = useState(currentSearch);
    const [modalPaciente, setModalPaciente] = useState(null);

    function handleSearch(e) {
        e.preventDefault();
        const params = new URLSearchParams();
        if (search) params.set('search', search);
        window.location.href = indexUrl + (params.toString() ? '?' + params.toString() : '');
    }

    return (
        <div className="w-full p-6 bg-gray-50/50 min-h-screen">

            {/* Header */}
            <div className="flex justify-between items-end mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-gray-800">Camillas — Emergencia</h1>
                    <p className="text-sm text-gray-500">Registrar uso de camilla por paciente</p>
                </div>
                <a href={dashboardUrl}
                   className="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                    ← Volver al panel
                </a>
            </div>

            <Flash flash={flash} />

            {/* Search */}
            <form onSubmit={handleSearch} className="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
                <div className="relative flex-1">
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg className="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" value={search} onChange={e => setSearch(e.target.value)}
                           className="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
                           placeholder="Buscar por nombre, documento o código de registro..." />
                </div>
                <button type="submit" className="flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-colors shadow-sm">
                    Buscar
                </button>
                {currentSearch && (
                    <button type="button" onClick={() => window.location.href = indexUrl}
                            className="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                        Limpiar
                    </button>
                )}
            </form>

            {/* Table */}
            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div className="px-6 py-4 border-b border-gray-100 bg-white">
                    <h3 className="text-gray-800 font-bold text-sm">Pacientes Registrados ({total})</h3>
                </div>

                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-100">
                        <thead className="bg-gray-50/50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Código</th>
                                <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                                <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Carnet</th>
                                <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Seguro</th>
                                <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ingreso</th>
                                <th className="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-100">
                            {pacientes.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="px-6 py-12 text-center text-gray-500">
                                        <div className="flex flex-col items-center">
                                            <svg className="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <p className="text-lg font-medium text-gray-600 mb-2">No se encontraron pacientes</p>
                                        </div>
                                    </td>
                                </tr>
                            ) : pacientes.map(p => (
                                <PatientRow key={p.id} p={p} camillasVacias={camillasVacias}
                                            onOpenModal={setModalPaciente} />
                            ))}
                        </tbody>
                    </table>
                </div>

                {pagination.hasPages && (
                    <Pagination pagination={pagination} total={total}
                                indexUrl={indexUrl} currentSearch={currentSearch} />
                )}
            </div>

            {/* Single shared modal — mounted only when open */}
            {modalPaciente && (
                <CamillaModal
                    paciente={modalPaciente}
                    camillas={camillas}
                    csrfToken={csrfToken}
                    storeUrl={storeUrl}
                    onClose={() => setModalPaciente(null)}
                />
            )}
        </div>
    );
}

ReactDOM.createRoot(document.getElementById('camillas-root')).render(<CamillasIndex />);
</script>
@endpush
