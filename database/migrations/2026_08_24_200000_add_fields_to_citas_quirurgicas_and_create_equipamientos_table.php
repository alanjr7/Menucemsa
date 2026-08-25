<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear tabla de catálogo de equipamientos de quirófano
        if (!Schema::hasTable('quirofano_equipamientos')) {
            Schema::create('quirofano_equipamientos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 150)->unique();
                $table->decimal('precio_base', 12, 2)->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });

            // Insertar equipamientos iniciales
            DB::table('quirofano_equipamientos')->insert([
                [
                    'nombre' => 'Arco en C (C-Arm)',
                    'precio_base' => 0.00,
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nombre' => 'Torre de lámparas',
                    'precio_base' => 0.00,
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 2. Modificar tabla de citas_quirurgicas para agregar campos de anestesia y equipamiento
        Schema::table('citas_quirurgicas', function (Blueprint $table) {
            if (!Schema::hasColumn('citas_quirurgicas', 'tipo_anestesia')) {
                $table->string('tipo_anestesia', 100)->nullable()->after('tipo_final');
            }
            if (!Schema::hasColumn('citas_quirurgicas', 'equipamiento_nombre')) {
                $table->string('equipamiento_nombre', 150)->nullable()->after('quirofano_id');
            }
            if (!Schema::hasColumn('citas_quirurgicas', 'equipamiento_precio')) {
                $table->decimal('equipamiento_precio', 12, 2)->default(0)->after('equipamiento_nombre');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas_quirurgicas', function (Blueprint $table) {
            if (Schema::hasColumn('citas_quirurgicas', 'tipo_anestesia')) {
                $table->dropColumn('tipo_anestesia');
            }
            if (Schema::hasColumn('citas_quirurgicas', 'equipamiento_nombre')) {
                $table->dropColumn('equipamiento_nombre');
            }
            if (Schema::hasColumn('citas_quirurgicas', 'equipamiento_precio')) {
                $table->dropColumn('equipamiento_precio');
            }
        });

        Schema::dropIfExists('quirofano_equipamientos');
    }
};
