<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use Illuminate\Support\Facades\DB;

class ConsolidarCuentasPaciente extends Command
{
    protected $signature = 'cuentas:consolidar {--dry-run : Solo mostrar cuentas que se consolidarían sin ejecutar}';
    protected $description = 'Consolida cuentas duplicadas por paciente en una sola cuenta maestra';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('Buscando cuentas duplicadas por paciente...');

        // Buscar pacientes con múltiples cuentas pendientes
        $pacientesDuplicados = CuentaCobro::select('paciente_ci')
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->where('es_post_pago', true)
            ->groupBy('paciente_ci')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('paciente_ci');

        if ($pacientesDuplicados->isEmpty()) {
            $this->info('No se encontraron cuentas duplicadas.');
            return 0;
        }

        $this->info("Encontrados {$pacientesDuplicados->count()} pacientes con cuentas duplicadas.");

        $consolidadas = 0;

        foreach ($pacientesDuplicados as $pacienteCi) {
            $this->newLine();
            $this->info("Procesando paciente CI: {$pacienteCi}");

            // Obtener todas las cuentas del paciente ordenadas por fecha
            $cuentas = CuentaCobro::where('paciente_ci', $pacienteCi)
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->where('es_post_pago', true)
                ->orderBy('created_at', 'asc')
                ->get();

            $this->info("  Cuentas encontradas: {$cuentas->count()}");

            if ($dryRun) {
                foreach ($cuentas as $index => $cuenta) {
                    $this->line("  [{$index}] {$cuenta->id} - {$cuenta->tipo_atencion} - S/ {$cuenta->total_calculado}");
                }
                continue;
            }

            DB::transaction(function () use ($cuentas, &$consolidadas) {
                // La primera cuenta será la maestra
                $cuentaMaestra = $cuentas->first();
                $cuentasSecundarias = $cuentas->slice(1);

                $totalMigrado = 0;
                $detallesMigrados = 0;

                foreach ($cuentasSecundarias as $cuenta) {
                    // Migrar todos los detalles a la cuenta maestra
                    $detalles = CuentaCobroDetalle::where('cuenta_cobro_id', $cuenta->id)->get();

                    foreach ($detalles as $detalle) {
                        CuentaCobroDetalle::create([
                            'cuenta_cobro_id' => $cuentaMaestra->id,
                            'tipo_item' => $detalle->tipo_item,
                            'tarifa_id' => $detalle->tarifa_id,
                            'descripcion' => $detalle->descripcion . ' (consolidado de ' . $cuenta->id . ')',
                            'cantidad' => $detalle->cantidad,
                            'precio_unitario' => $detalle->precio_unitario,
                            'subtotal' => $detalle->subtotal,
                            'origen_type' => $detalle->origen_type,
                            'origen_id' => $detalle->origen_id,
                            'observaciones' => $detalle->observaciones,
                            'created_at' => $detalle->created_at,
                            'updated_at' => now(),
                        ]);
                        $detallesMigrados++;
                    }

                    $totalMigrado += $cuenta->total_calculado;

                    // Actualizar observaciones de la cuenta maestra
                    $cuentaMaestra->observaciones = ($cuentaMaestra->observaciones ?? '') .
                        " | Consolidado cuenta {$cuenta->id}: S/ {$cuenta->total_calculado}";

                    // Marcar cuenta secundaria como consolidada (no eliminar, solo cambiar estado)
                    $cuenta->update([
                        'estado' => 'consolidada',
                        'observaciones' => ($cuenta->observaciones ?? '') . ' | Consolidada en cuenta ' . $cuentaMaestra->id,
                    ]);

                    $this->line("    Migrada cuenta {$cuenta->id} con {$detalles->count()} detalles");
                }

                // Actualizar cuenta maestra
                $cuentaMaestra->update([
                    'tipo_atencion' => 'multiple',
                    'total_calculado' => $cuentas->sum('total_calculado'),
                    'total_pagado' => $cuentas->sum('total_pagado'),
                    'observaciones' => ($cuentaMaestra->observaciones ?? '') . ' | Cuenta consolidada con ' . $cuentasSecundarias->count() . ' cuentas',
                ]);

                $consolidadas++;

                $this->info("  Consolidada cuenta maestra: {$cuentaMaestra->id}");
                $this->info("  Total nuevo: S/ {$cuentaMaestra->total_calculado}");
                $this->info("  Detalles migrados: {$detallesMigrados}");
            });
        }

        $this->newLine();
        $this->info("Proceso completado. Pacientes consolidados: {$consolidadas}");

        if ($dryRun) {
            $this->warn('Este fue un dry-run. No se realizaron cambios.');
            $this->info('Ejecuta sin --dry-run para consolidar realmente.');
        }

        return 0;
    }
}
