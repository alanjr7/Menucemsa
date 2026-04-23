<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificacionesDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            // Notificación de emergencia
            \App\Services\NotificationService::notify(
                $user->id,
                'emergencia',
                'Nueva Emergencia',
                'Paciente Juan Pérez registrado en emergencia',
                '/emergency-staff/dashboard',
                ['emergency_id' => 1]
            );

            // Notificación de pago
            \App\Services\NotificationService::notify(
                $user->id,
                'pago',
                'Pago Completado',
                'Paciente: María González - Monto: $150.00',
                '/caja-operativa',
                ['cuenta_id' => 1]
            );

            // Notificación de cirugía
            \App\Services\NotificationService::notify(
                $user->id,
                'cirugia',
                'Cirugía Programada',
                'Paciente: Carlos López - Fecha: 2026-04-25 10:00',
                '/quirofano',
                ['cita_id' => 1]
            );

            // Notificación de derivación UTI
            \App\Services\NotificationService::notify(
                $user->id,
                'derivacion',
                'Ingreso UTI',
                'Paciente Ana Martínez ingresado a UTI desde Emergencia',
                '/uti-operativa',
                ['admission_id' => 1]
            );
        }

        $this->command->info('Notificaciones de demostración creadas para ' . $users->count() . ' usuarios');
    }
}
