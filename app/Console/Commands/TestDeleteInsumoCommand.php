<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Insumos;

class TestDeleteInsumoCommand extends Command
{
    protected $signature = 'test:delete-insumo {codigo}';
    protected $description = 'Probar eliminación de insumo';

    public function handle()
    {
        $codigo = $this->argument('codigo');
        
        $this->info("Buscando insumo con código: {$codigo}");
        
        $insumo = Insumos::find($codigo);
        
        if ($insumo) {
            $this->info("✅ Insumo encontrado:");
            $this->table(
                ['Código', 'Nombre', 'Descripción', 'Precio'],
                [[$insumo->CODIGO, $insumo->NOMBRE, $insumo->DESCRIPCION, $insumo->PRECIO]]
            );
            
            if ($this->confirm('¿Desea eliminar este insumo?')) {
                try {
                    $insumo->delete();
                    $this->info("✅ Insumo eliminado exitosamente");
                } catch (\Exception $e) {
                    $this->error("❌ Error eliminando insumo: " . $e->getMessage());
                }
            }
        } else {
            $this->error("❌ Insumo no encontrado con código: {$codigo}");
            
            // Mostrar todos los insumos disponibles
            $todos = Insumos::all();
            if ($todos->count() > 0) {
                $this->info("Insumos disponibles:");
                $this->table(
                    ['Código', 'Nombre'],
                    $todos->map(fn($i) => [$i->CODIGO, $i->NOMBRE])->toArray()
                );
            }
        }
        
        return 0;
    }
}
