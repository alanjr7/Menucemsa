<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('INVENTARIO', function (Blueprint $table) {
            $table->string('ID', 15);
            $table->string('ID_FARMACIA', 15);
            $table->string('TIPO_ITEM', 15)->nullable();
            $table->string('STOCK_MINIMO', 80)->nullable();
            $table->string('STOCK_DISPONIBLE', 80)->nullable();
            $table->string('REPOSICION', 80)->nullable();
            $table->date('FECHA_INGRESO')->nullable();
            
            $table->primary(['ID', 'ID_FARMACIA']);
            
            $table->foreign('ID_FARMACIA')
                  ->references('ID')
                  ->on('FARMACIA')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('INVENTARIO');
    }
};
