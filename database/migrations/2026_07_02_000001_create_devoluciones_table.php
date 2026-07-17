<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            // Nota de Crédito interna: NC-AAAA-NNNNNN (correlativo anual, patrón CTA-).
            $table->string('id')->primary();

            // El pago original NUNCA se toca: la devolución es un contra-ingreso
            // nuevo que apunta al recibo PAGO- devuelto (total o parcialmente).
            $table->string('pago_cuenta_id');
            $table->string('cuenta_cobro_id');

            $table->decimal('monto', 10, 2);

            // Reversa del débito fiscal IVA (Libro de Ventas): la NC resta 13% por
            // dentro, espejo exacto del débito que generó el pago. Se guarda en
            // POSITIVO; los reportes la restan (mismo criterio que egresos anulados).
            $table->decimal('base_imponible', 10, 2)->default(0);
            $table->decimal('debito_fiscal', 10, 2)->default(0);

            $table->enum('metodo_devolucion', ['efectivo', 'transferencia', 'tarjeta', 'qr']);
            $table->string('referencia')->nullable();
            $table->string('motivo');
            $table->text('observaciones')->nullable();

            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('caja_session_id')->nullable()->constrained('caja_sessions');

            // Idempotencia: token único por intento (doble-click / reintento de red).
            $table->string('idempotency_key')->nullable()->unique();

            // Inmutabilidad: la NC no se borra, se anula (reversible + auditado).
            $table->timestamp('anulado_at')->nullable();
            $table->foreignId('anulado_por')->nullable()->constrained('users');
            $table->string('motivo_anulacion')->nullable();

            $table->timestamps();

            $table->foreign('pago_cuenta_id')->references('id')->on('pago_cuentas');
            $table->foreign('cuenta_cobro_id')->references('id')->on('cuenta_cobros');

            $table->index(['pago_cuenta_id', 'anulado_at']);
            $table->index(['created_at', 'anulado_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
