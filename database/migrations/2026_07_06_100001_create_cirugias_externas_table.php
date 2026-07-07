<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reservas de cirugía registradas por cirujanos externos desde la ruta pública
 * (sin login). Tabla AISLADA del flujo clínico: es entrada no confiable, por lo
 * que nunca se escribe en `citas_quirurgicas`. Los datos de cirujano/paciente se
 * guardan como texto (no se crean `Medico`/`Paciente`). El admin la verifica.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cirugias_externas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();          // CIREXT-Ymd-NNNN

            // Cirujano externo (texto libre)
            $table->string('cirujano_nombre');
            $table->string('cirujano_telefono', 30);
            $table->string('cirujano_email');

            // Paciente (sólo nombre; no se crea registro de Paciente)
            $table->string('paciente_nombre');

            // Catálogo externo + quirófano real
            $table->foreignId('tipo_cirugia_externa_id')->constrained('tipos_cirugia_externa');
            $table->unsignedBigInteger('quirofano_id');

            // Agenda
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            // Precio (prepago tarifa plana). Money helper para todo cálculo.
            $table->decimal('precio_base', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('precio_final', 10, 2)->default(0);
            $table->boolean('es_nocturno')->default(false);

            // Comprobante de pago + estado
            $table->string('recibo_path')->nullable();
            $table->enum('estado', ['pendiente', 'pagado', 'rechazado'])->default('pendiente');
            $table->text('motivo_rechazo')->nullable();

            // Verificación por administración
            $table->unsignedBigInteger('verificado_por')->nullable();
            $table->timestamp('verificado_at')->nullable();

            $table->timestamps();

            $table->index(['quirofano_id', 'fecha']);
            $table->index('estado');

            $table->foreign('quirofano_id')->references('id')->on('quirofanos');
            $table->foreign('verificado_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cirugias_externas');
    }
};
