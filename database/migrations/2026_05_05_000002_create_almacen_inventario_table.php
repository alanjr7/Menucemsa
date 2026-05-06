<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen_inventario', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_activo', 100)->unique();
            $table->string('nombre', 255);
            $table->decimal('precio', 12, 2)->default(0);
            $table->integer('cantidad')->default(0);
            $table->string('marca', 150)->nullable();
            $table->string('proveedor', 255)->nullable();
            $table->string('nro_factura', 100)->nullable();
            $table->string('numero_recibo', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_inventario');
    }
};
