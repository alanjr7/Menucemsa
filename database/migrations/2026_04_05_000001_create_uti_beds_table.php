<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uti_beds', function (Blueprint $table) {
            $table->id();
            $table->string('bed_number', 10)->unique();
            $table->enum('status', ['disponible', 'ocupada', 'mantenimiento', 'reservada'])->default('disponible');
            $table->string('tipo', 50)->default('standard');
            $table->text('equipamiento')->nullable();
            $table->decimal('precio_dia', 10, 2)->default(0);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_beds');
    }
};
