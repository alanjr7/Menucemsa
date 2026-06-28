<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Una venta de farmacia puede tener como receptor un cliente registrado
     * (cliente_id) O un paciente del sistema (paciente_id). Ambos son nullable
     * y excluyentes: permite venderle a un paciente sin re-registrarlo como
     * cliente, preservando la trazabilidad de sus compras.
     */
    public function up(): void
    {
        Schema::table('ventas_farmacia', function (Blueprint $table) {
            $table->foreignId('paciente_id')->nullable()->after('cliente_id')
                ->constrained('pacientes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ventas_farmacia', function (Blueprint $table) {
            $table->dropForeign(['paciente_id']);
            $table->dropColumn('paciente_id');
        });
    }
};
