<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('FARMACIA', function (Blueprint $table) {
            $table->string('ID', 15)->primary();
            $table->string('DETALLE', 80);
        });
    }

    public function down()
    {
        Schema::dropIfExists('FARMACIA');
    }
};
