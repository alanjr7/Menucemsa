<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedimientos', function (Blueprint $table) {
            $table->id();
            // Código interno de servicio (familia 5 = PROCEDIMIENTOS) para comprobantes.
            $table->string('codigo', 12)->nullable()->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('area', ['emergencia', 'uti', 'internacion', 'cirugia', 'hospitalizacion', 'neonato']);
            $table->decimal('precio', 10, 2);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimientos');
    }
};
