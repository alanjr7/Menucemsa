<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::firstOrCreate(
            ['email' => 'admin@ClinicaSantaCruz.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Crear usuario recepción
        User::firstOrCreate(
            ['email' => 'recepcion@ClinicaSantaCruz.com'],
            [
                'name' => 'Recepción',
                'password' => bcrypt('recepcion123'),
                'role' => 'reception',
                'is_active' => true,
            ]
        );

        // Crear usuario Director Médico
        User::firstOrCreate(
            ['email' => 'dirmedico@ClinicaSantaCruz.com'],
            [
                'name' => 'Director Médico',
                'password' => bcrypt('dirmedico123'),
                'role' => 'dirmedico',
                'is_active' => true,
            ]
        );

        // Crear usuario Emergencia
        User::firstOrCreate(
            ['email' => 'emergencia@ClinicaSantaCruz.com'],
            [
                'name' => 'Emergencia',
                'password' => bcrypt('emergencia123'),
                'role' => 'emergencia',
                'is_active' => true,
            ]
        );

        // Crear usuario Caja
        User::firstOrCreate(
            ['email' => 'caja@ClinicaSantaCruz.com'],
            [
                'name' => 'Caja',
                'password' => bcrypt('caja123'),
                'role' => 'caja',
                'is_active' => true,
            ]
        );

        // Crear usuario Gerente
        User::firstOrCreate(
            ['email' => 'gerente@ClinicaSantaCruz.com'],
            [
                'name' => 'Gerente',
                'password' => bcrypt('gerente123'),
                'role' => 'gerente',
                'is_active' => true,
            ]
        );

        // Crear usuario UTI
        User::firstOrCreate(
            ['email' => 'uti@ClinicaSantaCruz.com'],
            [
                'name' => 'UTI',
                'password' => bcrypt('uti123'),
                'role' => 'uti',
                'is_active' => true,
            ]
        );

        // Crear usuario Internación
        User::firstOrCreate(
            ['email' => 'internacion@ClinicaSantaCruz.com'],
            [
                'name' => 'Internación',
                'password' => bcrypt('internacion123'),
                'role' => 'internacion',
                'is_active' => true,
            ]
        );

        // Crear usuario Cirujano
        User::firstOrCreate(
            ['email' => 'cirujano@ClinicaSantaCruz.com'],
            [
                'name' => 'Cirujano',
                'password' => bcrypt('cirujano123'),
                'role' => 'cirujano',
                'is_active' => true,
            ]
        );

        // Crear usuario Doctor
        User::firstOrCreate(
            ['email' => 'doctor@ClinicaSantaCruz.com'],
            [
                'name' => 'Doctor',
                'password' => bcrypt('doctor123'),
                'role' => 'doctor',
                'is_active' => true,
            ]
        );

        // Crear usuario Farmacia
        User::firstOrCreate(
            ['email' => 'farmacia@ClinicaSantaCruz.com'],
            [
                'name' => 'Farmacia',
                'password' => bcrypt('farmacia123'),
                'role' => 'farmacia',
                'is_active' => true,
            ]
        );

        // Crear usuario Enfermera Emergencia
        User::firstOrCreate(
            ['email' => 'enfermera-emergencia@ClinicaSantaCruz.com'],
            [
                'name' => 'Enfermera Emergencia',
                'password' => bcrypt('enfermera123'),
                'role' => 'enfermera-emergencia',
                'is_active' => true,
            ]
        );

        // Crear usuario Enfermera Internación
        User::firstOrCreate(
            ['email' => 'enfermera-internacion@ClinicaSantaCruz.com'],
            [
                'name' => 'Enfermera Internación',
                'password' => bcrypt('enfermera123'),
                'role' => 'enfermera-internacion',
                'is_active' => true,
            ]
        );

        // Crear usuario Administrador (rol visualizador general)
        User::firstOrCreate(
            ['email' => 'administrador@ClinicaSantaCruz.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('administrador123'),
                'role' => 'administrador',
                'is_active' => true,
            ]
        );
    }
}
