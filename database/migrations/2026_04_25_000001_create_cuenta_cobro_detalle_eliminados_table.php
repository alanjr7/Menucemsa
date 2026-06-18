<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bitácora única de ANULACIONES de cargos (fuente única de "eliminaciones
        // seguras"). Cada fila es un evento de anulación de N unidades de una línea
        // de cuenta — puede ser total (toda la línea) o parcial (algunas unidades).
        // `cantidad`/`subtotal` = lo anulado en ESTE evento. Reversible vía
        // revertido_en. Conserva snapshot para mostrar histórico aunque la línea cambie.
        Schema::create('cuenta_cobro_detalle_eliminados', function (Blueprint $table) {
            $table->id();
            $table->string('cuenta_cobro_id');
            // Enlace a la línea viva (persiste: una anulación total sólo la deshabilita,
            // nunca la borra). Nullable por filas legacy del antiguo hard-delete.
            $table->foreignId('cuenta_cobro_detalle_id')->nullable()
                ->constrained('cuenta_cobro_detalles')->nullOnDelete();
            $table->enum('tipo_item', ['servicio', 'medicamento', 'procedimiento', 'estadia', 'laboratorio', 'imagenologia', 'farmacia', 'material', 'equipo_medico']);
            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2)->default(1)->comment('Unidades anuladas en este evento');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2)->comment('Monto anulado en este evento (cantidad * precio_unitario)');
            $table->string('origen_type')->nullable();
            $table->string('origen_id')->nullable();
            $table->string('area_origen', 50)->nullable()
                ->comment('emergencia|quirofano|internacion|uti|farmacia|consulta_externa');
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_eliminacion_id')->constrained('users');
            $table->string('motivo_eliminacion');
            $table->timestamp('eliminado_en');

            // Reversibilidad: una anulación se puede revertir (devuelve las unidades a
            // la línea). Una vez revertida queda sellada (revertido_en no null).
            $table->timestamp('revertido_en')->nullable();
            $table->foreignId('revertido_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Foreign keys
            $table->foreign('cuenta_cobro_id')->references('id')->on('cuenta_cobros')->onDelete('cascade');

            // Indices
            $table->index(['cuenta_cobro_id', 'tipo_item']);
            $table->index('cuenta_cobro_detalle_id');
            $table->index('eliminado_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobro_detalle_eliminados');
    }
};
