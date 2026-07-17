<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_cuentas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('cuenta_cobro_id');
            $table->decimal('monto', 10, 2);
            // Débito fiscal IVA (Libro de Ventas): el monto cobrado incluye IVA 13% (por
            // dentro). base_imponible = monto; debito_fiscal = monto * 13%. Es la contraparte
            // del crédito fiscal de compras (egresos). Aditivo, no altera el cobro.
            $table->decimal('base_imponible', 10, 2)->default(0);
            $table->decimal('debito_fiscal', 10, 2)->default(0);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'qr']);
            $table->string('referencia')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('caja_session_id')->nullable()->constrained('caja_sessions');
            $table->text('observaciones')->nullable();
            // Idempotencia: token único por intento de cobro. Evita pagos duplicados
            // ante doble-click o reintento de red. NULL para pagos sin token (legacy/internos).
            $table->string('idempotency_key')->nullable()->unique();
            $table->timestamps();

            $table->foreign('cuenta_cobro_id')->references('id')->on('cuenta_cobros')->onDelete('cascade');
            
            $table->index(['cuenta_cobro_id', 'created_at']);
            $table->index(['caja_session_id', 'created_at']);
        });

        // Trazabilidad pago<->item: ya existe la columna liquidado_pago_id en
        // cuenta_cobro_detalles (creada en 000002); su FK se agrega acá, una vez
        // que pago_cuentas existe.
        Schema::table('cuenta_cobro_detalles', function (Blueprint $table) {
            $table->foreign('liquidado_pago_id')->references('id')->on('pago_cuentas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_cobro_detalles', function (Blueprint $table) {
            $table->dropForeign(['liquidado_pago_id']);
        });

        Schema::dropIfExists('pago_cuentas');
    }
};
