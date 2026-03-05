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
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('descripcion', 200);
            $table->string('categoria', 50); // SERVICIO, PROCEDIMIENTO, CIRUGIA
            $table->decimal('precio_particular', 10, 2);
            $table->decimal('precio_sis', 10, 2)->nullable();
            $table->decimal('precio_eps', 10, 2)->nullable();
            $table->string('tipo_convenio_sis', 50)->nullable(); // CONVENIO, TARIFARIO
            $table->string('tipo_convenio_eps', 50)->nullable(); // CONVENIO, TARIFARIO
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};
