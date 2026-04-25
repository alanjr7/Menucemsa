<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_cobro_detalles', function (Blueprint $table) {
            $table->id();
            $table->string('cuenta_cobro_id');
            $table->enum('tipo_item', ['servicio', 'medicamento', 'procedimiento', 'estadia', 'laboratorio', 'imagenologia', 'farmacia', 'material', 'equipo_medico']);
            $table->foreignId('tarifa_id')->nullable()->constrained('tarifas');
            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->string('origen_id')->nullable();
            $table->string('origen_type')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('area_origen', 50)->nullable()
                ->comment('emergencia|quirofano|internacion|uti|farmacia|consulta_externa');
            $table->timestamps();

            // Foreign keys
            $table->foreign('cuenta_cobro_id')->references('id')->on('cuenta_cobros')->onDelete('cascade');
            
            // Índices
            $table->index(['cuenta_cobro_id', 'tipo_item']);
            $table->index(['origen_id', 'origen_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobro_detalles');
    }
};
