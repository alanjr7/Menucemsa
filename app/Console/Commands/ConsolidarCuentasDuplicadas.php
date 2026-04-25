<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use Illuminate\Support\Facades\DB;

class ConsolidarCuentasDuplicadas extends Command
{
    protected $signature   = 'cuentas:consolidar {--dry-run : Solo mostrar lo que haría sin modificar}';
    protected $description = 'Consolida múltiples CuentaCobro activas del mismo paciente en una sola (Cuenta Maestra)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info($dryRun ? '[DRY-RUN] Modo simulación — no se modificará nada' : 'Consolidando cuentas duplicadas...');

        // Encontrar pacientes con más de 1 cuenta activa post-pago
        $duplicados = DB::select("
            SELECT paciente_ci, COUNT(*) as cnt
            FROM cuenta_cobros
            WHERE estado IN ('pendiente', 'parcial')
              AND es_post_pago = 1
              AND deleted_at IS NULL
            GROUP BY paciente_ci
            HAVING cnt > 1
        ");

        if (empty($duplicados)) {
            $this->info('✅ No se encontraron cuentas duplicadas activas. Todo está limpio.');
            return 0;
        }

        $this->warn(count($duplicados) . ' paciente(s) con cuentas duplicadas encontradas.');
        $consolidadas = 0;

        foreach ($duplicados as $dup) {
            $cuentas = CuentaCobro::where('paciente_ci', $dup->paciente_ci)
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->where('es_post_pago', true)
                ->orderBy('created_at', 'asc')
                ->get();

            // La cuenta más antigua es la "maestra"
            $cuentaMaestra = $cuentas->first();
            $cuentasDuplicadas = $cuentas->slice(1);

            $this->line(sprintf(
                '  Paciente CI %s → Cuenta maestra: %s | Duplicadas a consolidar: %d',
                $dup->paciente_ci,
                $cuentaMaestra->id,
                $cuentasDuplicadas->count()
            ));

            if ($dryRun) {
                foreach ($cuentasDuplicadas as $c) {
                    $this->line(sprintf('    [DRY-RUN] Movería %d detalle(s) de %s → %s', $c->detalles()->count(), $c->id, $cuentaMaestra->id));
                }
                continue;
            }

            DB::transaction(function () use ($cuentaMaestra, $cuentasDuplicadas) {
                foreach ($cuentasDuplicadas as $cuentaDup) {
                    // Mover todos los detalles a la cuenta maestra
                    CuentaCobroDetalle::where('cuenta_cobro_id', $cuentaDup->id)
                        ->update(['cuenta_cobro_id' => $cuentaMaestra->id]);

                    // Mover pagos a la cuenta maestra
                    DB::table('pago_cuentas')
                        ->where('cuenta_cobro_id', $cuentaDup->id)
                        ->update(['cuenta_cobro_id' => $cuentaMaestra->id]);

                    // Marcar la cuenta duplicada como anulada
                    $cuentaDup->update([
                        'estado'       => 'anulada',
                        'observaciones'=> ($cuentaDup->observaciones ?? '') . ' | ANULADA: consolidada en ' . $cuentaMaestra->id,
                    ]);
                }

                // Recalcular total de la cuenta maestra
                $cuentaMaestra->total_calculado = CuentaCobroDetalle::where('cuenta_cobro_id', $cuentaMaestra->id)->sum('subtotal');
                $cuentaMaestra->total_pagado    = DB::table('pago_cuentas')->where('cuenta_cobro_id', $cuentaMaestra->id)->sum('monto');
                $cuentaMaestra->save();
                $cuentaMaestra->recalcularTotales();
            });

            $consolidadas++;
        }

        $this->info("✅ Consolidación completa: {$consolidadas} paciente(s) procesados.");
        return 0;
    }
}
