@extends('layouts.app')

@section('content')

@php
$userRole = auth()->user()->role;
$roleAreaRouteMap = [
    'emergencia'            => 'evaluacion.emergencia',
    'enfermera-emergencia'  => 'evaluacion.emergencia',
    'uti'                   => 'evaluacion.uti',
    'internacion'           => 'evaluacion.internacion',
    'enfermera-internacion' => 'evaluacion.internacion',
];
$evalRouteName = $roleAreaRouteMap[$userRole] ?? null;
$isAdmin = in_array($userRole, ['admin', 'administrador']);

$pacientesData = $pacientes->map(function ($paciente) use ($evalRouteName) {
    $isTemporal  = isset($paciente->is_temporal) && $paciente->is_temporal;
    $tipoIngreso = $paciente->tipo_ingreso ?? 'otro';

    if ($isTemporal) {
        $codigoDisplay = $paciente->emergency_code;
        $datosUrl      = route('reception.emergencia.comprobante', $paciente->emergency_id);
        $seguroLabel   = 'Emergencia - ' . $tipoIngreso;
    } else {
        $cajaId        = $paciente->consultas->first()?->caja?->id;
        $codigoDisplay = $cajaId ?? $paciente->registro_codigo;
        $seguroLabel   = $paciente->seguro->nombre_empresa ?? 'Particular';

        if ($tipoIngreso === 'internacion' && $paciente->hospitalizaciones->isNotEmpty()) {
            $datosUrl = route('reception.hospitalizacion.comprobante', $paciente->hospitalizaciones->first()->id);
        } elseif ($tipoIngreso === 'emergencia' && $paciente->emergencias->isNotEmpty()) {
            $datosUrl = route('reception.emergencia.comprobante', $paciente->emergencias->first()->id);
        } elseif (in_array($tipoIngreso, ['consulta_externa', 'enfermeria', 'otro']) && $paciente->registro_codigo) {
            $datosUrl = route('reception.confirmacion-registro', $paciente->registro_codigo);
        } else {
            $datosUrl = route('patients.show', $paciente->ci);
        }
    }

    return [
        'ci'            => $paciente->ci,
        'nombre'        => $paciente->nombre,
        'is_temporal'   => $isTemporal,
        'codigo'        => $codigoDisplay,
        'emergency_code'=> $paciente->emergency_code ?? null,
        'seguro'        => $seguroLabel,
        'seguro_temp'   => $isTemporal,
        'tipo_ingreso'  => $tipoIngreso,
        'historial_url' => route('evaluacion.historial', $paciente->ci),
        'eval_url'      => $evalRouteName ? route($evalRouteName, $paciente->ci) : null,
        'datos_url'     => $datosUrl,
    ];
});

$appData = [
    'stats'             => $stats,
    'pacientes'         => $pacientesData->values(),
    'total'             => $pacientes->total(),
    'pagination'        => [
        'hasPages'    => $pacientes->hasPages(),
        'currentPage' => $pacientes->currentPage(),
        'lastPage'    => $pacientes->lastPage(),
        'from'        => $pacientes->firstItem(),
        'to'          => $pacientes->lastItem(),
        'prevUrl'     => $pacientes->appends(request()->query())->previousPageUrl(),
        'nextUrl'     => $pacientes->appends(request()->query())->nextPageUrl(),
    ],
    'isAdmin'           => $isAdmin,
    'historialAltasUrl' => $isAdmin ? route('patients.historial-altas') : null,
    'currentSearch'     => request('search', ''),
    'currentEstado'     => request('estado', ''),
    'baseUrl'           => route('patients.index'),
];
@endphp

<div id="patients-root"></div>

<script>
window.__PATIENTS_DATA__ = @json($appData);
</script>

@endsection

@push('scripts')
<script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
<script type="text/babel">
const { useState } = React;

const INGRESO_CONFIG = {
    enfermeria:       { badge: 'bg-purple-100 text-purple-800 border-purple-200', dot: 'bg-purple-500', label: 'Enfermería',  pulse: false },
    consulta_externa: { badge: 'bg-green-100 text-green-800 border-green-200',   dot: 'bg-green-500',  label: 'Consulta',    pulse: false },
    emergencia:       { badge: 'bg-red-100 text-red-800 border-red-200',         dot: 'bg-red-500',    label: 'Emergencia',  pulse: true  },
    internacion:      { badge: 'bg-yellow-100 text-yellow-800 border-yellow-200',dot: 'bg-yellow-500', label: 'Internación', pulse: false },
};
const DEFAULT_INGRESO = { badge: 'bg-gray-100 text-gray-800 border-gray-200', dot: 'bg-gray-500', label: 'Otro', pulse: false };

function PatientRow({ p }) {
    const ingreso = p.is_temporal
        ? { badge: 'bg-red-100 text-red-800 border-red-200', dot: 'bg-red-500', label: 'Emergencia', pulse: true }
        : (INGRESO_CONFIG[p.tipo_ingreso] || DEFAULT_INGRESO);

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
                {p.seguro_temp
                    ? <span className="text-xs text-red-500">{p.seguro}</span>
                    : p.seguro}
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${ingreso.badge}`}>
                    <span className={`w-1.5 h-1.5 rounded-full mr-1.5 ${ingreso.dot}${ingreso.pulse ? ' animate-pulse' : ''}`}></span>
                    {ingreso.label}
                </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div className="flex justify-end gap-2">
                    {p.eval_url && (
                        <a href={p.eval_url} className="inline-flex items-center px-3 py-1.5 border border-blue-200 shadow-sm text-xs font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 transition-all">
                            Evaluar
                        </a>
                    )}
                    <a href={p.historial_url} className="inline-flex items-center px-3 py-1.5 border border-gray-200 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                        Historial
                    </a>
                    {p.datos_url && (
                        <a href={p.datos_url} className="inline-flex items-center px-3 py-1.5 border border-gray-200 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                            Datos
                        </a>
                    )}
                </div>
            </td>
        </tr>
    );
}

function PatientsIndex() {
    const {
        stats, pacientes, total, pagination,
        isAdmin, historialAltasUrl,
        currentSearch, currentEstado, baseUrl,
    } = window.__PATIENTS_DATA__;

    const [search, setSearch] = useState(currentSearch);
    const [estado, setEstado] = useState(currentEstado);

    function handleSearch(e) {
        e.preventDefault();
        const params = new URLSearchParams();
        if (search) params.set('search', search);
        if (estado) params.set('estado', estado);
        window.location.href = baseUrl + (params.toString() ? '?' + params.toString() : '');
    }

    const hasFilters = currentSearch || currentEstado;

    return (
        <div className="w-full p-6 bg-gray-50/50 min-h-screen">

            {/* Header */}
            <div className="flex justify-between items-end mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-gray-800">Maestro Único de Pacientes</h1>
                    <p className="text-sm text-gray-500">Pacientes registrados y pagados en el sistema</p>
                </div>
                {isAdmin && (
                    <div className="flex gap-3">
                        <a href={historialAltasUrl}
                           className="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 font-medium transition-colors shadow-sm text-sm">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Historial de Altas
                        </a>
                    </div>
                )}
            </div>

            {/* Stats */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div className="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm text-gray-500">Total Pacientes</p>
                            <p className="text-2xl font-bold text-gray-800">{stats.total}</p>
                        </div>
                        <div className="p-2 bg-blue-100 rounded-lg">
                            <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div className="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm text-gray-500">Hospitalizados</p>
                            <p className="text-2xl font-bold text-yellow-600">{stats.hospitalizados}</p>
                        </div>
                        <div className="p-2 bg-yellow-100 rounded-lg">
                            <svg className="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div className="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm text-gray-500">Emergencias</p>
                            <p className="text-2xl font-bold text-red-600">{stats.emergencias}</p>
                        </div>
                        <div className="p-2 bg-red-100 rounded-lg">
                            <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {/* Search & Filter */}
            <form onSubmit={handleSearch} className="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
                <div className="relative flex-1">
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg className="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        value={search}
                        onChange={e => setSearch(e.target.value)}
                        className="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:placeholder-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors"
                        placeholder="Buscar por nombre, documento o código de registro..."
                    />
                </div>

                <select
                    value={estado}
                    onChange={e => setEstado(e.target.value)}
                    className="px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
                >
                    <option value="">Todos los estados</option>
                    <option value="hospitalizado">Hospitalizados</option>
                    <option value="emergencia">Emergencias</option>
                </select>

                <button type="submit" className="flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-colors shadow-sm">
                    <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar
                </button>

                {hasFilters && (
                    <button type="button" onClick={() => window.location.href = baseUrl}
                            className="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                        <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Limpiar
                    </button>
                )}
            </form>

            {/* Table */}
            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div className="px-6 py-4 border-b border-gray-100 bg-white flex justify-between items-center">
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
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p className="text-lg font-medium text-gray-600 mb-2">No se encontraron pacientes</p>
                                            <p className="text-sm text-gray-400">No hay pacientes registrados que cumplan con los criterios de búsqueda.</p>
                                        </div>
                                    </td>
                                </tr>
                            ) : pacientes.map(p => <PatientRow key={p.ci} p={p} />)}
                        </tbody>
                    </table>
                </div>

                {pagination.hasPages && (
                    <div className="px-6 py-4 border-t border-gray-100 bg-gray-50/30 flex items-center justify-between">
                        <p className="text-sm text-gray-500">
                            Mostrando {pagination.from}–{pagination.to} de {total} pacientes
                        </p>
                        <div className="flex gap-2 items-center">
                            {pagination.prevUrl && (
                                <a href={pagination.prevUrl}
                                   className="px-3 py-1.5 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                                    ← Anterior
                                </a>
                            )}
                            <span className="px-3 py-1.5 text-sm text-gray-600">
                                Página {pagination.currentPage} de {pagination.lastPage}
                            </span>
                            {pagination.nextUrl && (
                                <a href={pagination.nextUrl}
                                   className="px-3 py-1.5 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                                    Siguiente →
                                </a>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

ReactDOM.createRoot(document.getElementById('patients-root')).render(<PatientsIndex />);
</script>
@endpush
