<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingreso_precios', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_ingreso')->unique();
            $table->decimal('precio', 10, 2);
            $table->boolean('activo')->default(true);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tipo_ingreso');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingreso_precios');
    }
};
