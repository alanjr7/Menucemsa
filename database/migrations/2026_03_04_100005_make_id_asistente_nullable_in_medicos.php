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
        Schema::table('medicos', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['id_asistente']);
            
            // Make the column nullable
            $table->string('id_asistente', 15)->nullable()->change();
            
            // Re-add the foreign key constraint (optional)
            // $table->foreign('id_asistente')->references('id')->on('asistente_quirofanos')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicos', function (Blueprint $table) {
            $table->string('id_asistente', 15)->nullable(false)->change();
        });
    }
};
