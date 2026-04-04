<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\CajaSession;
use App\Models\PagoCuenta;
use App\Models\Paciente;
use App\Models\User;
use App\Models\Tarifa;
use Carbon\Carbon;

class CajaSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar pacientes existentes
        $pacientes = Paciente::limit(5)->get();
        
        if ($pacientes->isEmpty()) {
            $this->command->warn('No hay pacientes en la base de datos. Por favor, cree pacientes primero desde el módulo de Recepción.');
            $this->command->info('El módulo de caja está listo para usar pero necesita pacientes con cuentas pendientes.');
            return;
        }

        // Verificar que exista usuario caja
        $usuarioCaja = User::where('role', 'caja')->first() 
            ?? User::where('role', 'admin')->first();

        if (!$usuarioCaja) {
            $this->command->warn('No hay usuario con rol caja o admin. Creando usuario de prueba...');
            $usuarioCaja = User::create([
                'name' => 'Usuario Caja',
                'email' => 'caja@example.com',
                'password' => bcrypt('password'),
                'role' => 'caja',
                'is_active' => true,
            ]);
        }

        // Verificar tarifas existentes o crear algunas
        $tarifaConsulta = Tarifa::where('categoria', 'CONSULTA')->first();
        $tarifaEmergencia = Tarifa::where('categoria', 'EMERGENCIA')->first();

        if (!$tarifaConsulta) {
            $tarifaConsulta = Tarifa::create([
                'codigo' => 'CONS-GENERAL',
                'descripcion' => 'Consulta General',
                'categoria' => 'CONSULTA',
                'precio_particular' => 150.00,
                'precio_sis' => 0.00,
                'precio_eps' => 100.00,
                'activo' => true,
            ]);
        }

        if (!$tarifaEmergencia) {
            $tarifaEmergencia = Tarifa::create([
                'codigo' => 'EMG-BASE',
                'descripcion' => 'Atención de Emergencia',
                'categoria' => 'EMERGENCIA',
                'precio_particular' => 250.00,
                'precio_sis' => 0.00,
                'precio_eps' => 150.00,
                'activo' => true,
            ]);
        }

        // Crear sesión de caja abierta
        $cajaSession = CajaSession::create([
            'usuario_id' => $usuarioCaja->id,
            'fecha_apertura' => Carbon::now(),
            'monto_inicial' => 500.00,
            'estado' => 'abierta',
            'observaciones' => 'Sesión de prueba',
        ]);

        $this->command->info("Caja abierta: ID {$cajaSession->id}");

        // Crear cuentas de prueba
        foreach ($pacientes as $index => $paciente) {
            // Alternar entre diferentes tipos y estados
            if ($index % 3 === 0) {
                // Cuenta pagada (pre-pago, flujo normal)
                $cuenta = new CuentaCobro([
                    'paciente_ci' => $paciente->ci,
                    'tipo_atencion' => 'consulta_externa',
                    'estado' => 'pagado',
                    'total_calculado' => $tarifaConsulta->precio_particular,
                    'total_pagado' => $tarifaConsulta->precio_particular,
                    'saldo_pendiente' => 0,
                    'es_emergencia' => false,
                    'es_post_pago' => false,
                    'ci_nit_facturacion' => $paciente->ci,
                    'razon_social' => $paciente->nombre,
                    'caja_session_id' => $cajaSession->id,
                    'usuario_caja_id' => $usuarioCaja->id,
                ]);
                $cuenta->save(); // Esto genera el ID automáticamente

                $cuenta->detalles()->create([
                    'tipo_item' => 'servicio',
                    'tarifa_id' => $tarifaConsulta->id,
                    'descripcion' => $tarifaConsulta->descripcion,
                    'cantidad' => 1,
                    'precio_unitario' => $tarifaConsulta->precio_particular,
                    'subtotal' => $tarifaConsulta->precio_particular,
                ]);

                // Crear pago
                PagoCuenta::create([
                    'cuenta_cobro_id' => $cuenta->id,
                    'monto' => $tarifaConsulta->precio_particular,
                    'metodo_pago' => $index % 2 === 0 ? 'efectivo' : 'transferencia',
                    'usuario_id' => $usuarioCaja->id,
                    'caja_session_id' => $cajaSession->id,
                ]);

                $this->command->info("Cuenta pagada creada para {$paciente->nombre}");

            } elseif ($index % 3 === 1) {
                // Cuenta pendiente (emergencia post-pago)
                $cuenta = new CuentaCobro([
                    'paciente_ci' => $paciente->ci,
                    'tipo_atencion' => 'emergencia',
                    'estado' => 'pendiente',
                    'total_calculado' => $tarifaEmergencia->precio_particular + 150, // + medicamentos
                    'total_pagado' => 0,
                    'saldo_pendiente' => $tarifaEmergencia->precio_particular + 150,
                    'es_emergencia' => true,
                    'es_post_pago' => true,
                ]);
                $cuenta->save();

                // Servicio de emergencia
                $cuenta->detalles()->create([
                    'tipo_item' => 'servicio',
                    'tarifa_id' => $tarifaEmergencia->id,
                    'descripcion' => $tarifaEmergencia->descripcion,
                    'cantidad' => 1,
                    'precio_unitario' => $tarifaEmergencia->precio_particular,
                    'subtotal' => $tarifaEmergencia->precio_particular,
                ]);

                // Medicamentos
                $cuenta->detalles()->create([
                    'tipo_item' => 'medicamento',
                    'descripcion' => 'Paracetamol 500mg',
                    'cantidad' => 10,
                    'precio_unitario' => 15.00,
                    'subtotal' => 150.00,
                ]);

                $this->command->info("Cuenta emergencia pendiente creada para {$paciente->nombre}");

            } else {
                // Cuenta con pago parcial
                $total = 300.00;
                $pagado = 100.00;

                $cuenta = new CuentaCobro([
                    'paciente_ci' => $paciente->ci,
                    'tipo_atencion' => 'consulta_externa',
                    'estado' => 'parcial',
                    'total_calculado' => $total,
                    'total_pagado' => $pagado,
                    'saldo_pendiente' => $total - $pagado,
                    'es_emergencia' => false,
                    'es_post_pago' => false,
                    'ci_nit_facturacion' => $paciente->ci,
                    'razon_social' => $paciente->nombre,
                    'caja_session_id' => $cajaSession->id,
                    'usuario_caja_id' => $usuarioCaja->id,
                ]);
                $cuenta->save();

                $cuenta->detalles()->create([
                    'tipo_item' => 'servicio',
                    'tarifa_id' => $tarifaConsulta->id,
                    'descripcion' => $tarifaConsulta->descripcion,
                    'cantidad' => 1,
                    'precio_unitario' => $tarifaConsulta->precio_particular,
                    'subtotal' => $tarifaConsulta->precio_particular,
                ]);

                $cuenta->detalles()->create([
                    'tipo_item' => 'laboratorio',
                    'descripcion' => 'Examen de sangre',
                    'cantidad' => 1,
                    'precio_unitario' => 150.00,
                    'subtotal' => 150.00,
                ]);

                PagoCuenta::create([
                    'cuenta_cobro_id' => $cuenta->id,
                    'monto' => $pagado,
                    'metodo_pago' => 'efectivo',
                    'usuario_id' => $usuarioCaja->id,
                    'caja_session_id' => $cajaSession->id,
                ]);

                $this->command->info("Cuenta parcial creada para {$paciente->nombre}");
            }
        }

        $this->command->info('Seeder de Caja completado exitosamente.');
        $this->command->info("Resumen:");
        $this->command->info("- Cuentas pagadas: " . CuentaCobro::pagado()->count());
        $this->command->info("- Cuentas pendientes: " . CuentaCobro::pendiente()->count());
        $this->command->info("- Cuentas parciales: " . CuentaCobro::parcial()->count());
        $this->command->info("- Emergencias: " . CuentaCobro::emergencias()->count());
    }
}
