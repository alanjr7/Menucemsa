<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Insumos;

class CheckInsumosCommand extends Command
{
    protected $signature = 'check:insumos';
    protected $description = 'Verificar insumos en la base de datos';

    public function handle()
    {
        $insumos = Insumos::all();
        
        $this->info("Total de insumos: " . $insumos->count());
        
        if ($insumos->count() > 0) {
            $this->table(
                ['Código', 'Nombre', 'Precio'],
                $insumos->map(function ($insumo) {
                    return [
                        $insumo->CODIGO,
                        $insumo->NOMBRE,
                        $insumo->PRECIO
                    ];
                })->toArray()
            );
        } else {
            $this->warn("No hay insumos registrados en la base de datos");
        }
        
        return 0;
    }
}
