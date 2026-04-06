<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emergencies', function (Blueprint $table) {
            // Campos para clasificación de ingreso
            $table->enum('tipo_ingreso', ['soat', 'parto', 'general'])->default('general')->after('status');
            
            // Campos para destino inicial
            $table->enum('destino_inicial', ['cirugia', 'camilla', 'uti', 'observacion', 'hospitalizacion', 'alta'])->nullable()->after('tipo_ingreso');
            
            // Campos para trazabilidad del flujo
            $table->json('flujo_historial')->nullable()->after('destination');
            
            // Campos para ubicación actual
            $table->enum('ubicacion_actual', ['emergencia', 'cirugia', 'uti', 'hospitalizacion', 'observacion', 'alta'])->default('emergencia')->after('flujo_historial');
            
            // Campos para sincronización con otros módulos
            $table->string('nro_cirugia')->nullable()->after('ubicacion_actual');
            $table->string('nro_hospitalizacion')->nullable()->after('nro_cirugia');
            $table->string('nro_uti')->nullable()->after('nro_hospitalizacion');
            
            // Campos financieros extendidos
            $table->decimal('deuda', 10, 2)->default(0)->after('paid');
            $table->decimal('total_pagado', 10, 2)->default(0)->after('deuda');
            $table->json('detalle_costos')->nullable()->after('total_pagado');
            
            // Campos para sala de parto
            $table->boolean('es_parto')->default(false)->after('detalle_costos');
            $table->enum('estado_parto', ['primer_trimestre', 'segundo_trimestre', 'tercer_trimestre', 'trabajo_parto', 'postparto'])->nullable()->after('es_parto');
            
            // Índices para mejorar rendimiento de consultas
            $table->index(['status', 'ubicacion_actual']);
            $table->index(['tipo_ingreso']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergencies', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_ingreso',
                'destino_inicial',
                'flujo_historial',
                'ubicacion_actual',
                'nro_cirugia',
                'nro_hospitalizacion',
                'nro_uti',
                'deuda',
                'total_pagado',
                'detalle_costos',
                'es_parto',
                'estado_parto'
            ]);
            
            $table->dropIndex(['status', 'ubicacion_actual']);
            $table->dropIndex(['tipo_ingreso']);
            $table->dropIndex(['created_at']);
        });
    }
};
