<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->string('codigo', 20)->primary();
            $table->string('nombre', 80);
            $table->string('descripcion', 120)->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('insumos');
    }
};
