<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_session_id')->constrained('caja_sessions')->onDelete('cascade');
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->string('concepto', 255);
            $table->decimal('monto', 10, 2);
            $table->string('referencia', 255)->nullable();
            $table->string('metodo_pago', 50)->nullable();
            $table->morphs('movable');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['caja_session_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
