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

            // Crédito fiscal IVA (Libro de Compras) — datos de la factura de compra.
            // Sin factura válida no hay crédito fiscal; importe_iva = total * 13%.
            $table->boolean('con_credito_fiscal')->default(false);
            $table->string('nit_proveedor', 20)->nullable();
            $table->string('nro_factura', 50)->nullable();
            $table->string('codigo_autorizacion', 100)->nullable();
            $table->decimal('importe_iva', 12, 2)->default(0);

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
