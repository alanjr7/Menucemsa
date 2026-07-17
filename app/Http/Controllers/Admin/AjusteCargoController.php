<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuentaCobroDetalle;
use App\Models\CuentaCobroDetalleEliminado;
use Illuminate\Http\Request;

/**
 * Punto ÚNICO de eliminaciones seguras de cargos de una cuenta.
 *
 * Las tres vistas (Correcciones de paciente, Cuenta del paciente y Caja-gestión)
 * apuntan a este controlador. No borra cargos: anula N unidades (parcial o total)
 * de forma reversible y auditada, delegando en el dominio
 * CuentaCobroDetalle::anular() / revertirAnulacion().
 *
 * Responde JSON cuando el cliente lo pide (fetch de caja-gestión) o redirect con
 * flash cuando es un POST de formulario Blade.
 */
class AjusteCargoController extends Controller
{
    /**
     * Anula `cantidad` unidades de un cargo. Total si cantidad >= cantidad viva.
     */
    public function anular(Request $request, $detalleId)
    {
        $validated = $request->validate([
            'cantidad' => 'required|numeric|decimal:0,2|min:0.01',
            'motivo'   => 'required|string|max:500',
        ], [], ['cantidad' => 'cantidad a anular', 'motivo' => 'motivo']);

        // conDeshabilitados: por si la línea ya fue anulada totalmente antes (queda
        // oculta por el global scope) — aunque el flujo normal anula líneas vivas.
        $detalle = CuentaCobroDetalle::conDeshabilitados()->with('cuentaCobro')->find($detalleId);
        if (!$detalle) {
            return $this->responder($request, false, 'El cargo no existe o ya fue eliminado.', 404);
        }

        try {
            $detalle->anular($validated['cantidad'], $validated['motivo'], (int) auth()->id());
        } catch (\RuntimeException | \InvalidArgumentException $e) {
            return $this->responder($request, false, $e->getMessage(), 422);
        }

        return $this->responder($request, true, 'Cargo anulado y registrado en el historial.');
    }

    /**
     * Revierte una anulación: devuelve las unidades al cargo.
     */
    public function revertir(Request $request, $anulacionId)
    {
        $evento = CuentaCobroDetalleEliminado::find($anulacionId);
        if (!$evento) {
            return $this->responder($request, false, 'La anulación no existe.', 404);
        }
        if ($evento->estaRevertida()) {
            return $this->responder($request, true, 'La anulación ya había sido revertida.');
        }

        $detalle = $evento->cuenta_cobro_detalle_id
            ? CuentaCobroDetalle::conDeshabilitados()->with('cuentaCobro')->find($evento->cuenta_cobro_detalle_id)
            : null;

        if (!$detalle) {
            return $this->responder($request, false, 'El cargo asociado ya no existe; no se puede revertir.', 422);
        }

        try {
            $detalle->revertirAnulacion($evento, (int) auth()->id());
        } catch (\RuntimeException | \InvalidArgumentException $e) {
            return $this->responder($request, false, $e->getMessage(), 422);
        }

        return $this->responder($request, true, 'Anulación revertida; las unidades volvieron al cargo.');
    }

    /**
     * Respuesta dual: JSON para clientes AJAX, redirect+flash para formularios.
     */
    private function responder(Request $request, bool $ok, string $message, int $statusError = 200)
    {
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(
                ['success' => $ok, 'message' => $message],
                $ok ? 200 : $statusError
            );
        }

        return redirect()->back()->with($ok ? 'success' : 'error', $message);
    }
}
