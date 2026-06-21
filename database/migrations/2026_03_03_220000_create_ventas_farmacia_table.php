<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas_farmacia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_venta', 20)->unique();
            $table->string('farmacia_id', 20)->nullable();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('cliente', 100)->default('Cliente General'); // nombre para mostrar (ticket/listados)

            // Snapshot fiscal del receptor (registro inmutable para la factura / Libro de Ventas)
            $table->boolean('con_credito_fiscal')->default(false);          // true = nominativa, false = S/N
            $table->string('factura_razon_social', 255)->default('S/N');
            $table->unsignedTinyInteger('factura_tipo_documento')->default(5); // NIT por defecto
            $table->string('factura_numero_documento', 20)->default('0');
            $table->string('factura_complemento', 5)->nullable();

            $table->decimal('total', 10, 2);
            // Débito fiscal IVA (Libro de Ventas): el total ya incluye IVA 13% (por dentro).
            // base_imponible = total; debito_fiscal = total * 13%.
            $table->decimal('base_imponible', 10, 2)->default(0);
            $table->decimal('debito_fiscal', 10, 2)->default(0);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'qr', 'credito']);
            $table->boolean('requiere_receta')->default(false);
            $table->timestamp('fecha_venta')->useCurrent();
            $table->enum('estado', ['COMPLETADA', 'ANULADA', 'PENDIENTE'])->default('COMPLETADA');
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('caja_diaria_id')->nullable()->constrained('caja_diarias');
            $table->text('observaciones')->nullable();
            
            $table->foreign('farmacia_id')->references('id')->on('farmacias')->onDelete('set null');
            $table->index('fecha_venta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas_farmacia');
    }
};
