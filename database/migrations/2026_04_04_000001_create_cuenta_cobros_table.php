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
            $table->bigInteger('paciente_ci')->unsigned();
            $table->string('tipo_atencion'); // consulta_externa, emergencia, hospitalizacion, etc.
            $table->string('referencia_id')->nullable();
            $table->string('referencia_type')->nullable();
            $table->enum('estado', ['pendiente', 'parcial', 'pagado'])->default('pendiente');
            $table->decimal('total_calculado', 10, 2)->default(0);
            $table->decimal('total_pagado', 10, 2)->default(0);
            $table->decimal('saldo_pendiente', 10, 2)->default(0);
            $table->boolean('es_emergencia')->default(false);
            $table->boolean('es_post_pago')->default(false); // true para emergencias que pagan después
            $table->string('ci_nit_facturacion')->nullable();
            $table->string('razon_social')->nullable();
            $table->foreignId('caja_session_id')->nullable()->constrained('caja_sessions');
            $table->foreignId('usuario_caja_id')->nullable()->constrained('users');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['paciente_ci', 'estado']);
            $table->index(['estado', 'created_at']);
            $table->index(['es_emergencia', 'es_post_pago']);
            $table->index(['referencia_id', 'referencia_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobros');
    }
};
