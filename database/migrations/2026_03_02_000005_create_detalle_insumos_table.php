<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('DETALLE_INSUMOS', function (Blueprint $table) {
            $table->string('ID_FARMACIA', 15);
            $table->string('CODIGO_INSUMOS', 15);
            $table->string('LABORATORIO', 80)->nullable();
            $table->date('FECHA_VENCIMIENTO');
            $table->string('DESCRIPCION', 80)->nullable();
            
            $table->primary(['ID_FARMACIA', 'CODIGO_INSUMOS']);
            
            $table->foreign('ID_FARMACIA')
                  ->references('ID')
                  ->on('FARMACIA')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->foreign('CODIGO_INSUMOS')
                  ->references('CODIGO')
                  ->on('INSUMOS')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('DETALLE_INSUMOS');
    }
};
