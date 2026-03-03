<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('MEDICAMENTOS', function (Blueprint $table) {
            $table->string('CODIGO', 15)->primary();
            $table->string('DESCRIPCION', 80);
            $table->float('PRECIO');
        });
    }

    public function down()
    {
        Schema::dropIfExists('MEDICAMENTOS');
    }
};
