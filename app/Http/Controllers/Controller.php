<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Maneja errores de forma segura: loguea el detalle, retorna mensaje genérico.
     * Para responses JSON (API interna).
     */
    protected function errorResponse(string $contexto, \Throwable $e, int $status = 500): \Illuminate\Http\JsonResponse
    {
        \Log::error($contexto . ': ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error. Por favor contacte al administrador.',
        ], $status);
    }

    /**
     * Para redirects con error (vistas Blade).
     */
    protected function errorRedirect(\Throwable $e, string $contexto, string $ruta): \Illuminate\Http\RedirectResponse
    {
        \Log::error($contexto . ': ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route($ruta)->with('error', 'Ocurrió un error inesperado.');
    }
}
