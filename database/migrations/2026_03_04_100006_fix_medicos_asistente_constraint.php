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
            // Drop the foreign key constraint
            $table->dropForeign(['id_asistente']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicos', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('id_asistente')->references('id')->on('asistente_quirofanos')->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
