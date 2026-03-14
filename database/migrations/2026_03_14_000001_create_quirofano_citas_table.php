<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quirofano_citas', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('procedure_name');
            $table->string('surgeon_name');
            $table->dateTime('scheduled_at');
            $table->string('operating_room', 20);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quirofano_citas');
    }
};
