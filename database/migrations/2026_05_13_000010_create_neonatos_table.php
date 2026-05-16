<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('neonatos', function (Blueprint $table) {
            $table->id();

            // Identificación del RN
            $table->string('nombre', 150)->nullable();
            $table->char('sexo', 1)->nullable();                   // M|F
            $table->unsignedBigInteger('paciente_id')->nullable();  // FK a pacientes.id (temp o real)
            $table->string('code', 30)->unique();                  // NEO-YYYYMMDD-###

            // Vínculo materno
            $table->unsignedBigInteger('madre_id')->nullable();
            $table->string('madre_nombre', 150)->nullable();

            // Datos clínicos del nacimiento
            $table->decimal('peso', 8, 2)->nullable();             // gramos
            $table->decimal('talla', 8, 2)->nullable();            // cm
            $table->decimal('perimetro_cefalico', 8, 2)->nullable(); // cm
            $table->tinyInteger('apgar1')->nullable()->unsigned();  // 0–10
            $table->tinyInteger('apgar5')->nullable()->unsigned();  // 0–10
            $table->string('tipo_parto', 30)->nullable();          // normal|cesarea|instrumentado
            $table->dateTime('fecha_hora_nacimiento')->nullable();

            // Estado y flujo
            $table->string('status', 30)->default('recibido');     // recibido|en_observacion|estable|uti_neonatal|alta|fallecido
            $table->json('status_logs')->nullable();
            $table->text('observaciones')->nullable();

            // Fechas de ingreso/egreso del área neonatal
            $table->dateTime('admission_date')->nullable();
            $table->dateTime('discharge_date')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreign('paciente_id')->references('id')->on('pacientes')->nullOnDelete();
            $table->foreign('madre_id')->references('id')->on('pacientes')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('neonatos');
    }
};
