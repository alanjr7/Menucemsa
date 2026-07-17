<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configuración (fila única) del módulo de backups automáticos.
     * Se trata como singleton: BackupSetting::actual() garantiza una sola fila.
     */
    public function up(): void
    {
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('automatico_activo')->default(false);
            // Cada cuánto se ejecuta el respaldo automático.
            $table->enum('frecuencia', ['diario', 'cada_2_dias', 'semanal', 'quincenal', 'mensual'])
                  ->default('diario');
            // Hora preferida de ejecución (el scheduler corre a partir de esta hora).
            $table->time('hora')->default('02:00:00');
            // Cuántos respaldos automáticos conservar (los más viejos se podan).
            $table->unsignedSmallInteger('retencion')->default(7);
            $table->boolean('incluir_archivos')->default(true);
            $table->timestamp('ultima_ejecucion_at')->nullable();
            $table->timestamps();
        });

        // Sembramos la fila única de configuración por defecto.
        DB::table('backup_settings')->insert([
            'automatico_activo' => false,
            'frecuencia' => 'diario',
            'hora' => '02:00:00',
            'retencion' => 7,
            'incluir_archivos' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};
