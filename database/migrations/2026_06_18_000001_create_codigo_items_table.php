<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Diccionario de códigos para ítems facturables que NO tienen catálogo propio
 * (laboratorio, imagenología, roles de quirófano, estadía, cargos manuales…).
 *
 * Familia 9 del esquema de códigos. Se auto-registra la primera vez que una
 * descripción se cobra: (tipo_item + descripcion_normalizada) -> codigo estable.
 * Es la red de seguridad de la Opción B (catálogos); ver ResolverCodigoItem.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codigo_items', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 12)->nullable()->unique();
            $table->string('tipo_item', 30);
            $table->string('descripcion_normalizada');
            $table->string('descripcion_original');
            $table->timestamps();

            // Una entrada por (tipo de ítem + descripción normalizada).
            $table->unique(['tipo_item', 'descripcion_normalizada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('codigo_items');
    }
};
