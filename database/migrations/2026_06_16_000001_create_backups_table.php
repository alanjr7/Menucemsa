<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            // Disco lógico (config/filesystems) y ruta relativa dentro del disco.
            $table->string('disk')->default('local');
            $table->string('path');
            $table->unsignedBigInteger('size')->default(0); // bytes
            // Origen del respaldo: manual (botón), automatico (scheduler),
            // pre_restauracion (snapshot de seguridad antes de restaurar).
            $table->enum('type', ['manual', 'automatico', 'pre_restauracion'])->default('manual');
            $table->boolean('incluye_archivos')->default(true);
            // Estado del proceso: permite registrar fallos parciales sin perder rastro.
            $table->enum('estado', ['en_proceso', 'completado', 'fallido'])->default('en_proceso');
            $table->text('error')->nullable();
            // Quién lo generó. NULL cuando lo crea el scheduler (sin sesión).
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
