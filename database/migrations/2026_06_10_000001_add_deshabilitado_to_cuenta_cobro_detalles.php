<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Soft-disable para cargos: un cargo deshabilitado se sigue viendo (en gris)
     * pero deja de sumar al total de la cuenta. Es reversible (restaurar).
     */
    public function up(): void
    {
        Schema::table('cuenta_cobro_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('cuenta_cobro_detalles', 'deshabilitado_en')) {
                $table->timestamp('deshabilitado_en')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('cuenta_cobro_detalles', 'deshabilitado_por')) {
                $table->foreignId('deshabilitado_por')->nullable()->after('deshabilitado_en')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('cuenta_cobro_detalles', 'motivo_deshabilitacion')) {
                $table->string('motivo_deshabilitacion')->nullable()->after('deshabilitado_por');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_cobro_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('cuenta_cobro_detalles', 'deshabilitado_por')) {
                $table->dropConstrainedForeignId('deshabilitado_por');
            }
            if (Schema::hasColumn('cuenta_cobro_detalles', 'deshabilitado_en')) {
                $table->dropColumn('deshabilitado_en');
            }
            if (Schema::hasColumn('cuenta_cobro_detalles', 'motivo_deshabilitacion')) {
                $table->dropColumn('motivo_deshabilitacion');
            }
        });
    }
};
