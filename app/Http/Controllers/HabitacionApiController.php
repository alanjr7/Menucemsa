<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\Hospitalizacion;
use Illuminate\Http\JsonResponse;

class HabitacionApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:internacion|admin|dirmedico|enfermera-internacion|administrador');
    }

    public function index(): JsonResponse
    {
        $habitaciones = Habitacion::with(['camas' => fn($q) => $q->orderBy('nro')])
            ->withCount(['camas as camas_disponibles' => fn($q) => $q->where('disponibilidad', 'disponible')])
            ->withCount('camas')
            ->orderBy('id')
            ->get()
            ->map(fn($h) => [
                'id' => $h->id,
                'estado' => $h->estado,
                'detalle' => $h->detalle,
                'capacidad' => $h->capacidad,
                'camas_count' => $h->camas_count,
                'camas_disponibles' => $h->camas_disponibles,
            ]);

        return response()->json([
            'success' => true,
            'habitaciones' => $habitaciones,
        ]);
    }

    public function show(Habitacion $habitacion): JsonResponse
    {
        $habitacion->load(['camas' => fn($q) => $q->orderBy('nro')->with(['hospitalizacionActiva.paciente'])]);

        return response()->json([
            'success' => true,
            'habitacion' => $this->formatHabitacion($habitacion),
        ]);
    }

    public function pacientesSinHabitacion(): JsonResponse
    {
        $pacientes = Hospitalizacion::whereNull('fecha_alta')
            ->whereNull('habitacion_id')
            ->with('paciente')
            ->get()
            ->map(fn($h) => [
                'id' => $h->id,
                'paciente' => [
                    'nombre' => $h->paciente?->nombre,
                    'ci' => $h->paciente?->ci,
                ],
            ]);

        return response()->json([
            'success' => true,
            'pacientes' => $pacientes,
        ]);
    }

    private function formatHabitacion(Habitacion $h): array
    {
        return [
            'id' => $h->id,
            'estado' => $h->estado,
            'detalle' => $h->detalle,
            'capacidad' => $h->capacidad,
            'camas' => $h->camas->map(fn($c) => [
                'id' => $c->id,
                'nro' => $c->nro,
                'disponibilidad' => $c->disponibilidad,
                'tipo' => $c->tipo,
                'precio_por_dia' => $c->precio_por_dia,
                'hospitalizacion_activa' => $c->hospitalizacionActiva ? [
                    'id' => $c->hospitalizacionActiva->id,
                    'paciente' => [
                        'nombre' => $c->hospitalizacionActiva->paciente?->nombre,
                        'ci' => $c->hospitalizacionActiva->paciente?->ci,
                    ],
                ] : null,
            ]),
        ];
    }
}
