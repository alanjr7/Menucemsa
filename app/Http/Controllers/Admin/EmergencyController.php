<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Emergency;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class EmergencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin|administrador');
    }

    /**
     * Vista principal - Solo lectura
     */
    public function index(): View
    {
        return view('admin.emergencies.index');
    }

    /**
     * API: Listado de emergencias para admin
     */
    public function apiIndex(): JsonResponse
    {
        $emergencies = Emergency::with(['paciente'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($emg) {
                return [
                    'id' => $emg->id,
                    'code' => $emg->code,
                    'paciente_id' => $emg->paciente_id,
                    'paciente_nombre' => $emg->paciente?->is_temp ? 'Paciente Temporal' : ($emg->paciente?->nombre ?? 'Desconocido'),
                    'paciente_ci' => $emg->paciente?->ci ?? $emg->paciente?->temp_code ?? '—',
                    'is_temp' => $emg->paciente?->is_temp ?? false,
                    'tipo_ingreso' => $emg->tipo_ingreso,
                    'tipo_ingreso_label' => $emg->tipo_ingreso_label,
                    'destino_inicial' => $emg->destino_inicial,
                    'ubicacion_actual' => $emg->ubicacion_actual,
                    'ubicacion_label' => $emg->ubicacion_label,
                    'status' => $emg->status,
                    'admission_date' => $emg->admission_date,
                    'flujo_historial' => $emg->flujo_historial ?? [],
                    'cost' => $emg->cost ?? 0,
                    'total_pagado' => $emg->total_pagado ?? 0,
                    'deuda' => $emg->deuda ?? 0,
                    'es_parto' => $emg->es_parto,
                    'sintomas' => $emg->symptoms,
                    'nro_cirugia' => $emg->nro_cirugia,
                    'nro_hospitalizacion' => $emg->nro_hospitalizacion,
                    'nro_uti' => $emg->nro_uti,
                ];
            });

        $stats = [
            'total' => Emergency::count(),
            'activos' => Emergency::whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])->count(),
            'uti' => Emergency::where('ubicacion_actual', 'uti')->count(),
            'cirugia' => Emergency::where('ubicacion_actual', 'cirugia')->count(),
            'alta' => Emergency::where('status', 'alta')->count(),
            'deuda_total' => Emergency::sum('deuda'),
        ];

        return response()->json([
            'success' => true,
            'emergencias' => $emergencies,
            'stats' => $stats
        ]);
    }

    /**
     * API: Ver detalle de emergencia
     */
    public function apiShow(Emergency $emergency): JsonResponse
    {
        $emergency->load(['paciente', 'user']);

        return response()->json([
            'success' => true,
            'emergencia' => [
                'id' => $emergency->id,
                'code' => $emergency->code,
                'paciente_id' => $emergency->paciente_id,
                'paciente_nombre' => $emergency->paciente?->is_temp ? 'Paciente Temporal' : ($emergency->paciente?->nombre ?? 'Desconocido'),
                'paciente_ci' => $emergency->paciente?->ci ?? $emergency->paciente?->temp_code ?? '—',
                'is_temp' => $emergency->paciente?->is_temp ?? false,
                'tipo_ingreso' => $emergency->tipo_ingreso,
                'tipo_ingreso_label' => $emergency->tipo_ingreso_label,
                'destino_inicial' => $emergency->destino_inicial,
                'ubicacion_actual' => $emergency->ubicacion_actual,
                'ubicacion_label' => $emergency->ubicacion_label,
                'status' => $emergency->status,
                'admission_date' => $emergency->admission_date,
                'discharge_date' => $emergency->discharge_date,
                'flujo_historial' => $emergency->flujo_historial ?? [],
                'cost' => $emergency->cost ?? 0,
                'total_pagado' => $emergency->total_pagado ?? 0,
                'deuda' => $emergency->deuda ?? 0,
                'es_parto' => $emergency->es_parto,
                'sintomas' => $emergency->symptoms,
                'initial_assessment' => $emergency->initial_assessment,
                'vital_signs' => $emergency->vital_signs,
                'nro_cirugia' => $emergency->nro_cirugia,
                'nro_hospitalizacion' => $emergency->nro_hospitalizacion,
                'nro_uti' => $emergency->nro_uti,
                'user_name' => $emergency->user?->name ?? 'No asignado',
            ]
        ]);
    }

    /**
     * Vista show - Solo lectura
     */
    public function show(Emergency $emergency): View
    {
        $emergency->load(['paciente', 'user']);
        return view('admin.emergencies.show', compact('emergency'));
    }
}
