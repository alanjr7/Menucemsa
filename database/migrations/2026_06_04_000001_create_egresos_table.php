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

            // Retención de impuestos (la clínica como agente de retención): pagos sin
            // factura a personas naturales (p.ej. honorarios médicos). Servicios = IUE
            // 12,5% + IT 3% = 15,5%; bienes = IUE 5% + IT 3% = 8%. Excluyente con crédito
            // fiscal. El neto al beneficiario = monto - (retencion_iue + retencion_it).
            $table->boolean('aplica_retencion')->default(false);
            $table->string('retencion_tipo', 20)->nullable(); // servicios | bienes
            $table->decimal('retencion_iue', 12, 2)->default(0);
            $table->decimal('retencion_it', 12, 2)->default(0);

            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users');

            // Inmutabilidad: un egreso no se borra, se anula (reversible + auditado).
            // El registro permanece visible para la auditoría; los anulados no suman al flujo de caja.
            $table->timestamp('anulado_at')->nullable();
            $table->foreignId('anulado_por')->nullable()->constrained('users');
            $table->string('motivo_anulacion')->nullable();

            $table->timestamps();

            $table->index('fecha');
            $table->index('categoria');
            $table->index('anulado_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egresos');
    }
};
