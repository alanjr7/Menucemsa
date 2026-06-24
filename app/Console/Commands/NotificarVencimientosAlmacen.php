<?php

namespace App\Console\Commands;

use App\Services\StockAlertaService;
use Illuminate\Console\Command;

class NotificarVencimientosAlmacen extends Command
{
    protected $signature = 'almacen:notificar-vencimientos {--dias=30 : Días de anticipación para "por vencer"}';

    protected $description = 'Notifica al almacenista los medicamentos vencidos o por vencer que aún tienen existencias.';

    public function handle(): int
    {
        $dias = (int) $this->option('dias');

        $resumen = StockAlertaService::escanearVencimientos($dias);

        $this->info("Alertas enviadas — vencidos: {$resumen['vencido']}, por vencer ({$dias}d): {$resumen['por_vencer']}.");

        return self::SUCCESS;
    }
}
