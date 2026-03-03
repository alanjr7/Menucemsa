<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('INSUMOS', function (Blueprint $table) {
            $table->string('CODIGO', 15)->primary();
            $table->string('NOMBRE', 80);
            $table->string('DESCRIPCION', 80)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('INSUMOS');
    }
};
