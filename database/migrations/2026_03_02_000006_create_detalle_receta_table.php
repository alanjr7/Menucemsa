<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('DETALLE_RECETA', function (Blueprint $table) {
            $table->string('ID_FARMACIA', 15);
            $table->string('CODIGO_MEDICAMENTOS', 15);
            $table->string('DOSIS', 80)->nullable();
            $table->float('SUBTOTAL')->nullable();
            
            $table->primary(['ID_FARMACIA', 'CODIGO_MEDICAMENTOS']);
            
            $table->foreign('ID_FARMACIA')
                  ->references('ID')
                  ->on('FARMACIA')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->foreign('CODIGO_MEDICAMENTOS')
                  ->references('CODIGO')
                  ->on('MEDICAMENTOS')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('DETALLE_RECETA');
    }
};
