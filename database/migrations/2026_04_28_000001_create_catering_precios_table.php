<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_precios', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_comida', ['desayuno', 'almuerzo', 'merienda', 'cena']);
            $table->decimal('precio', 10, 2)->default(0);
            $table->timestamps();

            $table->unique('tipo_comida');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_precios');
    }
};
