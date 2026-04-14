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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ejemplo: 'Pacientes'
            $table->string('route')->nullable(); // Ejemplo: 'patients.index' (nulo si es un menú padre)
            $table->string('active_pattern')->nullable(); // Para mantenerlo abierto: 'patients*,consulta*'
            $table->text('icon_path')->nullable(); // El código del <path d="...">
            $table->string('color')->default('blue'); // blue, red, purple, emerald, etc.
            $table->foreignId('parent_id')->nullable()->constrained('menus')->cascadeOnDelete();
            $table->integer('order')->default(0); // Para ordenar el menú
            // Define quién puede ver este menú (Ej: 'admin,doctor,dir_medico')
            $table->string('roles')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
