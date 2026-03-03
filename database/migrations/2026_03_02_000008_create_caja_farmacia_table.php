<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('CAJA_FARMACIA', function (Blueprint $table) {
            $table->string('CODIGO', 15)->primary();
            $table->string('DETALLE', 80)->nullable();
            $table->float('TOTAL')->nullable();
            $table->string('ID_CAJA', 15)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('CAJA_FARMACIA');
    }
};
