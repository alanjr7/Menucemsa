<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Proformas (cotizaciones / presupuestos pre-admisión).
 *
 * Una proforma es un estimado NO vinculante que se entrega al paciente para
 * que conozca el costo aproximado de uno o varios servicios. No es un cargo
 * real: no toca CuentaCobro, caja ni contabilidad. El paciente va como texto
 * libre (puede no estar registrado todavía).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proformas', function (Blueprint $table) {
            $table->id();
            // Correlativo legible sin saltos: PRF-AAAA-NNNNNN (espejo de CuentaCobro::generarNumero).
            $table->string('numero')->unique();

            // Receptor de la proforma (texto libre: puede no ser un paciente registrado).
            $table->string('paciente_nombre');
            $table->string('paciente_documento')->nullable(); // CI / NIT
            $table->string('paciente_telefono')->nullable();

            // Vigencia del presupuesto (los precios pueden cambiar).
            $table->unsignedSmallInteger('validez_dias')->default(15);

            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0); // snapshot = sum(items) - descuento

            $table->text('observaciones')->nullable();

            // Autor: cada usuario ve las suyas; admin/administrador ven todas.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('proforma_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_id')->constrained('proformas')->cascadeOnDelete();

            // Código interno de producto/servicio (familia+id) si el ítem vino del
            // catálogo; nulo si es un concepto escrito a mano.
            $table->string('codigo_item', 12)->nullable();
            $table->string('tipo_item', 30)->nullable();

            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->unsignedInteger('orden')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma_items');
        Schema::dropIfExists('proformas');
    }
};
