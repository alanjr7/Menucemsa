<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 6 — `seguros.formulario` era NOT NULL pero el formulario lo trata como opcional
 * (validación 'nullable'). Se alinea la BD haciéndolo nullable: el campo "Formulario/Tipo
 * de atención" es informativo y no debería bloquear el alta de un convenio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seguros', function (Blueprint $table) {
            $table->string('formulario', 80)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('seguros', function (Blueprint $table) {
            $table->string('formulario', 80)->nullable(false)->default('')->change();
        });
    }
};
