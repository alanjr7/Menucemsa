<?php

namespace App\Http\Controllers;

use App\Models\Cama;
use App\Models\Habitacion;
use App\Services\Habitacion\AsignacionCamaService;
use App\Services\Habitacion\LiberacionCamaService;
use App\Traits\Transaccionable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HabitacionAsignacionController extends Controller
{
    use Transaccionable;

    private AsignacionCamaService $asignacionService;
    private LiberacionCamaService $liberacionService;

    public function __construct(
        AsignacionCamaService $asignacionService,
        LiberacionCamaService $liberacionService
    ) {
        $this->middleware('role:internacion|admin|dirmedico|enfermera-internacion');
        $this->asignacionService = $asignacionService;
        $this->liberacionService = $liberacionService;
    }

    public function asignarPaciente(Request $request, Habitacion $habitacion): JsonResponse
    {
        $request->validate([
            'cama_id' => 'required|exists:camas,id',
            'hospitalizacion_id' => 'required|exists:hospitalizaciones,id',
        ]);

        $resultado = $this->transaction(
            fn() => $this->asignacionService->asignar(
                $habitacion,
                (int) $request->cama_id,
                $request->hospitalizacion_id
            ),
            fn($e) => ['success' => false, 'error' => $e->getMessage()]
        );

        if (!$resultado['success']) {
            return response()->json($resultado, 400);
        }

        return response()->json($resultado);
    }

    public function liberarCama(Request $request, Cama $cama): JsonResponse
    {
        $resultado = $this->transaction(
            fn() => $this->liberacionService->liberar($cama),
            fn($e) => ['success' => false, 'error' => $e->getMessage()]
        );

        if (!$resultado['success']) {
            return response()->json($resultado, 400);
        }

        return response()->json($resultado);
    }
}
