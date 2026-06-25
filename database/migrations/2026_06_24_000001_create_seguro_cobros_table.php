<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sub-mayor de cuentas por cobrar a aseguradoras.
 *
 * Cada fila es la porción de una cuenta cubierta por un seguro: una VENTA devengada
 * (con su base imponible + débito fiscal IVA, igual que PagoCuenta/VentaFarmacia) que
 * la aseguradora aún debe a la clínica. Antes esta porción desaparecía del sistema
 * (no era ingreso, ni cuenta por cobrar, ni venta fiscal). Acá queda registrada para:
 *   - declararse en el Libro de Ventas IVA / débito fiscal (devengado, no caja),
 *   - cobrarse luego a la aseguradora (liquidación → estado 'cobrado').
 *
 * NO entra al flujo de caja físico: el dinero del paciente (copago) sigue viviendo en
 * pago_cuentas; este ledger es exclusivamente lo que paga el seguro.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguro_cobros', function (Blueprint $table) {
            // Correlativo legible SEG-AAAA-NNNNNN (mismo estilo que REC-/PAGO-).
            $table->string('id')->primary();
            $table->string('cuenta_cobro_id');
            $table->foreignId('seguro_id')->constrained('seguros');

            // Venta devengada a la aseguradora. monto = cobertura; IVA "por dentro":
            // base_imponible = monto; debito_fiscal = monto * 13%.
            $table->decimal('monto', 10, 2);
            $table->decimal('base_imponible', 10, 2)->default(0);
            $table->decimal('debito_fiscal', 10, 2)->default(0);

            // Ciclo de la cuenta por cobrar: pendiente (autorizado, sin cobrar a la
            // aseguradora) → cobrado (liquidado) | anulado (autorización revertida).
            $table->enum('estado', ['pendiente', 'cobrado', 'anulado'])->default('pendiente');

            $table->foreignId('autorizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observaciones')->nullable();

            // Liquidación (cobro real a la aseguradora) — lo usa el Sprint 2.
            $table->timestamp('liquidado_en')->nullable();
            $table->foreignId('liquidado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('liquidado_referencia')->nullable();

            // Anulación reversible + auditada (no se borra).
            $table->timestamp('anulado_en')->nullable();
            $table->foreignId('anulado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('anulado_motivo')->nullable();

            $table->timestamps();

            $table->foreign('cuenta_cobro_id')->references('id')->on('cuenta_cobros')->onDelete('cascade');
            $table->index(['seguro_id', 'estado']);
            $table->index(['estado', 'created_at']);
            $table->index(['cuenta_cobro_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguro_cobros');
    }
};
