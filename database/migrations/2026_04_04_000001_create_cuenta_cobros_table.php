<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_cobros', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('paciente_ci')->unsigned()->nullable();
            $table->string('tipo_atencion');
            $table->string('referencia_id')->nullable();
            $table->string('referencia_type')->nullable();
            $table->enum('estado', ['pendiente', 'parcial', 'pagado', 'anulado'])->default('pendiente');
            $table->decimal('total_calculado', 10, 2)->default(0);
            $table->decimal('total_pagado', 10, 2)->default(0);
            $table->boolean('es_emergencia')->default(false);
            $table->boolean('es_post_pago')->default(false);
            $table->string('ci_nit_facturacion', 30)->nullable();
            $table->string('razon_social', 255)->nullable();
            $table->foreignId('caja_session_id')->nullable()->constrained('caja_sessions');
            $table->foreignId('user_caja_id')->nullable()->constrained('users');
            $table->text('observaciones')->nullable();
            $table->enum('seguro_estado', ['pendiente_autorizacion', 'autorizado', 'rechazado'])->nullable();
            $table->foreignId('seguro_id')->nullable()->constrained('seguros')->onDelete('set null');
            $table->foreignId('seguro_autorizado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('seguro_fecha_autorizacion')->nullable();
            $table->text('seguro_observaciones')->nullable();
            $table->decimal('seguro_monto_cobertura', 10, 2)->nullable();
            $table->decimal('seguro_monto_paciente', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['paciente_ci', 'estado']);
            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobros');
    }
};
