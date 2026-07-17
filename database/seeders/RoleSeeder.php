<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
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
            ['email' => 'admin@cemsa.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('admin123'),
                'password_encrypted' => Crypt::encryptString('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Crear usuario recepción
        User::firstOrCreate(
            ['email' => 'recepcion@cemsa.com'],
            [
                'name' => 'Recepción',
                'password' => bcrypt('recepcion123'),
                'password_encrypted' => Crypt::encryptString('recepcion123'),
                'role' => 'reception',
                'is_active' => true,
            ]
        );

        // Crear usuario Director Médico
        User::firstOrCreate(
            ['email' => 'dirmedico@cemsa.com'],
            [
                'name' => 'Director Médico',
                'password' => bcrypt('dirmedico123'),
                'password_encrypted' => Crypt::encryptString('dirmedico123'),
                'role' => 'dirmedico',
                'is_active' => true,
            ]
        );

        // Crear usuario Emergencia
        User::firstOrCreate(
            ['email' => 'emergencia@cemsa.com'],
            [
                'name' => 'Emergencia',
                'password' => bcrypt('emergencia123'),
                'password_encrypted' => Crypt::encryptString('emergencia123'),
                'role' => 'emergencia',
                'is_active' => true,
            ]
        );

        // Crear usuario Caja
        User::firstOrCreate(
            ['email' => 'caja@cemsa.com'],
            [
                'name' => 'Caja',
                'password' => bcrypt('caja123'),
                'password_encrypted' => Crypt::encryptString('caja123'),
                'role' => 'caja',
                'is_active' => true,
            ]
        );

        // Crear usuario Gerente
        User::firstOrCreate(
            ['email' => 'gerente@cemsa.com'],
            [
                'name' => 'Gerente',
                'password' => bcrypt('gerente123'),
                'password_encrypted' => Crypt::encryptString('gerente123'),
                'role' => 'gerente',
                'is_active' => true,
            ]
        );

        // Crear usuario UTI
        User::firstOrCreate(
            ['email' => 'uti@cemsa.com'],
            [
                'name' => 'UTI',
                'password' => bcrypt('uti123'),
                'password_encrypted' => Crypt::encryptString('uti123'),
                'role' => 'uti',
                'is_active' => true,
            ]
        );

        // Crear usuario Internación
        User::firstOrCreate(
            ['email' => 'internacion@cemsa.com'],
            [
                'name' => 'Internación',
                'password' => bcrypt('internacion123'),
                'password_encrypted' => Crypt::encryptString('internacion123'),
                'role' => 'internacion',
                'is_active' => true,
            ]
        );

        // Crear usuario Cirujano
        User::firstOrCreate(
            ['email' => 'cirujano@cemsa.com'],
            [
                'name' => 'Cirujano',
                'password' => bcrypt('cirujano123'),
                'password_encrypted' => Crypt::encryptString('cirujano123'),
                'role' => 'cirujano',
                'is_active' => true,
            ]
        );

        // Crear usuario Doctor
        User::firstOrCreate(
            ['email' => 'doctor@cemsa.com'],
            [
                'name' => 'Doctor',
                'password' => bcrypt('doctor123'),
                'password_encrypted' => Crypt::encryptString('doctor123'),
                'role' => 'doctor',
                'is_active' => true,
            ]
        );

        // Crear usuario Farmacia
        User::firstOrCreate(
            ['email' => 'farmacia@cemsa.com'],
            [
                'name' => 'Farmacia',
                'password' => bcrypt('farmacia123'),
                'password_encrypted' => Crypt::encryptString('farmacia123'),
                'role' => 'farmacia',
                'is_active' => true,
            ]
        );

        // Crear usuario Enfermera Emergencia
        User::firstOrCreate(
            ['email' => 'enfermera-emergencia@cemsa.com'],
            [
                'name' => 'Enfermera Emergencia',
                'password' => bcrypt('enfermera123'),
                'password_encrypted' => Crypt::encryptString('enfermera123'),
                'role' => 'enfermera-emergencia',
                'is_active' => true,
            ]
        );

        // Crear usuario Enfermera Internación
        User::firstOrCreate(
            ['email' => 'enfermera-internacion@cemsa.com'],
            [
                'name' => 'Enfermera Internación',
                'password' => bcrypt('enfermera123'),
                'password_encrypted' => Crypt::encryptString('enfermera123'),
                'role' => 'enfermera-internacion',
                'is_active' => true,
            ]
        );

        // Crear usuario Administrador (rol visualizador general)
        User::firstOrCreate(
            ['email' => 'administrador@cemsa.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('administrador123'),
                'password_encrypted' => Crypt::encryptString('administrador123'),
                'role' => 'administrador',
                'is_active' => true,
            ]
        );

        // Crear usuario Almacenista (gestión de medicamentos e insumos de todas las áreas)
        User::firstOrCreate(
            ['email' => 'almacenista@cemsa.com'],
            [
                'name' => 'Almacenista',
                'password' => bcrypt('almacenista123'),
                'password_encrypted' => Crypt::encryptString('almacenista123'),
                'role' => 'almacenista',
                'is_active' => true,
            ]
        );
    }
}
