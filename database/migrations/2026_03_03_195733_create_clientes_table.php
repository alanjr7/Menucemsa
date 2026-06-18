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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255); // razón social / nombre completo (tal cual va en la factura)
            $table->string('telefono', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('direccion')->nullable();

            // Datos fiscales del receptor (factura SFE Bolivia)
            $table->unsignedTinyInteger('tipo_documento')->default(5); // catálogo SIN: 1=CI,2=CEX,3=Pas,4=Otro,5=NIT
            $table->string('numero_documento', 20)->nullable();        // NIT o CI
            $table->string('complemento', 5)->nullable();              // complemento alfanumérico del CI

            $table->timestamps();

            $table->index('nombre');
            $table->index('numero_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
