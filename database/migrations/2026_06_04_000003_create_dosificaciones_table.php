<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dosificación / numeración autorizada por el SIN. Define el rango de números
        // de factura autorizado, su llave/autorización y la fecha límite de emisión.
        // Prerrequisito de la facturación (SFE diferido): hoy es scaffold preparatorio.
        Schema::create('dosificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('modalidad')->default('computarizada_en_linea'); // computarizada_en_linea | electronica_en_linea | manual
            $table->string('numero_autorizacion')->nullable();              // N° de autorización / CUIS
            $table->string('llave_dosificacion')->nullable();               // llave/clave de dosificación
            $table->unsignedBigInteger('rango_desde')->default(1);
            $table->unsignedBigInteger('rango_hasta')->nullable();          // null = sin tope (en línea)
            $table->date('fecha_limite_emision')->nullable();
            $table->boolean('activa')->default(true);
            $table->string('observaciones')->nullable();
            $table->timestamps();

            $table->index('activa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosificaciones');
    }
};
