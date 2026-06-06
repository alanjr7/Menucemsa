<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('egresos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->enum('categoria', [
                'sueldos',
                'honorarios',
                'alquiler',
                'servicios_basicos',
                'insumos_medicos',
                'mantenimiento',
                'limpieza',
                'equipamiento',
                'impuestos',
                'otros',
            ]);
            $table->string('descripcion');
            $table->decimal('monto', 12, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'tarjeta', 'qr'])->default('efectivo');
            $table->string('proveedor')->nullable();
            $table->string('comprobante_nro', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            $table->index('fecha');
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egresos');
    }
};
