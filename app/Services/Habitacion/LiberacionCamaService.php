<?php

namespace App\Services\Habitacion;

use App\Models\Cama;
use App\Models\Hospitalizacion;
use App\Models\CuentaCobroDetalle;

class LiberacionCamaService
{
    public function liberar(Cama $cama): array
    {
        $hospitalizacion = $this->obtenerHospitalizacionActiva($cama);

        if ($hospitalizacion) {
            $this->finalizarEstadia($hospitalizacion);
        }

        $this->liberarCama($cama);
        $this->actualizarEstadoHabitacion($cama);

        return [
            'success' => true,
            'message' => $this->generarMensaje($hospitalizacion),
        ];
    }

    private function obtenerHospitalizacionActiva(Cama $cama): ?Hospitalizacion
    {
        return Hospitalizacion::where('cama_id', $cama->id)
            ->whereNull('fecha_alta')
            ->first();
    }

    private function finalizarEstadia(Hospitalizacion $hospitalizacion): void
    {
        $dias = $hospitalizacion->getDiasEstancia();
        $costoTotal = $hospitalizacion->getCostoEstancia();

        $this->actualizarDetalleCuenta($hospitalizacion, $dias, $costoTotal);
        $this->recalcularTotalCuenta($hospitalizacion);
        $this->guardarTotalesHospitalizacion($hospitalizacion, $costoTotal);
    }

    private function actualizarDetalleCuenta(
        Hospitalizacion $hospitalizacion,
        int $dias,
        float $costoTotal
    ): void {
        if (!$hospitalizacion->cuenta_cobro_detalle_id) {
            return;
        }

        $detalle = CuentaCobroDetalle::find($hospitalizacion->cuenta_cobro_detalle_id);
        if (!$detalle) {
            return;
        }

        $detalle->update([
            'cantidad' => $dias,
            'subtotal' => $costoTotal,
            'descripcion' => "Estancia Internación - {$dias} días (Hab. {$hospitalizacion->habitacion_id}, Cama {$hospitalizacion->cama?->nro})",
        ]);
    }

    private function recalcularTotalCuenta(Hospitalizacion $hospitalizacion): void
    {
        if (!$hospitalizacion->cuenta_cobro_detalle_id) {
            return;
        }

        $detalle = CuentaCobroDetalle::find($hospitalizacion->cuenta_cobro_detalle_id);
        if (!$detalle || !$detalle->cuentaCobro) {
            return;
        }

        $cuenta = $detalle->cuentaCobro;
        $cuenta->total_calculado = $cuenta->detalles->sum('subtotal');
        $cuenta->save();
    }

    private function guardarTotalesHospitalizacion(
        Hospitalizacion $hospitalizacion,
        float $costoTotal
    ): void {
        $hospitalizacion->update([
            'total_estancia' => $costoTotal,
            'fecha_alta' => now(),
            'habitacion_id' => null,
            'cama_id' => null,
        ]);
    }

    private function liberarCama(Cama $cama): void
    {
        $cama->update(['disponibilidad' => 'disponible']);
    }

    private function actualizarEstadoHabitacion(Cama $cama): void
    {
        $habitacion = $cama->habitacion;
        $camasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->count();

        if ($camasOcupadas === 0 && $habitacion->estado === 'ocupada') {
            $habitacion->update(['estado' => 'disponible']);
        }
    }

    private function generarMensaje(?Hospitalizacion $hospitalizacion): string
    {
        if (!$hospitalizacion) {
            return 'Cama liberada exitosamente.';
        }

        $dias = $hospitalizacion->getDiasEstancia();
        $total = number_format($hospitalizacion->total_estancia, 2);

        return "Cama liberada. Estancia: {$dias} días. Total: Bs. {$total}";
    }
}
