<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispensaciones_almacen', function (Blueprint $table) {
            $table->integer('paciente_ci')->nullable()->after('recibido_por');
            $table->foreign('paciente_ci')->references('ci')->on('pacientes')->onDelete('restrict');

            $table->foreignId('entregado_por')->nullable()->after('paciente_ci')
                ->constrained('users')->onDelete('restrict');

            $table->timestamp('fecha_entrega_paciente')->nullable()->after('entregado_por');
        });
    }

    public function down(): void
    {
        Schema::table('dispensaciones_almacen', function (Blueprint $table) {
            $table->dropForeign(['paciente_ci']);
            $table->dropForeign(['entregado_por']);
            $table->dropColumn(['paciente_ci', 'entregado_por', 'fecha_entrega_paciente']);
        });
    }
};
