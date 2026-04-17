<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enfermera_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enfermera_id')->constrained('enfermeras', 'user_id')->onDelete('cascade');
            $table->string('permission_key', 50);
            $table->foreignId('granted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint to prevent duplicate permissions
            $table->unique(['enfermera_id', 'permission_key'], 'unique_permission');

            // Index for faster lookups
            $table->index(['enfermera_id', 'permission_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enfermera_permissions');
    }
};
