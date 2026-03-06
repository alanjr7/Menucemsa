<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hacer que medicos.id_asistente sea nullable para poder crear médicos sin asistente.
     * En SQLite se recrea la tabla porque no soporta ALTER COLUMN.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $this->upSqlite();
        } else {
            $this->upOther();
        }
    }

    private function upSqlite(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::dropIfExists('medicos_temp');
        Schema::create('medicos_temp', function (Blueprint $table) {
            $table->foreignId('id_usuario')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('ci');
            $table->integer('telefono')->nullable();
            $table->string('estado', 80);
            $table->string('id_asistente', 15)->nullable();
            $table->string('codigo_especialidad', 15)->nullable();
            $table->timestamps();
            $table->primary(['id_usuario', 'ci']);
        });

        DB::statement('INSERT INTO medicos_temp (id_usuario, ci, telefono, estado, id_asistente, codigo_especialidad, created_at, updated_at) SELECT id_usuario, ci, telefono, estado, id_asistente, codigo_especialidad, created_at, updated_at FROM medicos');

        Schema::drop('medicos');
        Schema::rename('medicos_temp', 'medicos');

        Schema::table('medicos', function (Blueprint $table) {
            $table->foreign('codigo_especialidad')->references('codigo')->on('especialidades')->onDelete('cascade')->onUpdate('cascade');
        });

        DB::statement('PRAGMA foreign_keys = ON');
    }

    private function upOther(): void
    {
        try {
            Schema::table('medicos', function (Blueprint $table) {
                $table->dropForeign(['id_asistente']);
            });
        } catch (\Throwable $e) {
            // La FK puede no existir si ya se eliminó en una migración anterior
        }
        Schema::table('medicos', function (Blueprint $table) {
            $table->string('id_asistente', 15)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('medicos', function (Blueprint $table) {
            $table->string('id_asistente', 15)->nullable(false)->change();
        });
    }
};
