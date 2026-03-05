<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarifa;

class TarifaSeeder extends Seeder
{
    public function run(): void
    {
        // Servicios y Consultas
        $servicios = [
            ['CONS-001', 'Consulta Medicina General', 'SERVICIO', 150.00, null, 120.00, 'CONVENIO', 'TARIFARIO'],
            ['CONS-002', 'Consulta Especializada', 'SERVICIO', 200.00, null, 160.00, 'CONVENIO', 'TARIFARIO'],
            ['LAB-001', 'Hemograma Completo', 'SERVICIO', 45.00, null, 38.00, 'CONVENIO', 'TARIFARIO'],
            ['LAB-002', 'Perfil Lipídico', 'SERVICIO', 85.00, null, 72.00, 'CONVENIO', 'TARIFARIO'],
            ['IMG-001', 'Radiografía de Tórax', 'SERVICIO', 120.00, null, 100.00, 'CONVENIO', 'TARIFARIO'],
            ['LAB-003', 'Examen de Orina', 'SERVICIO', 35.00, null, 28.00, 'CONVENIO', 'TARIFARIO'],
            ['IMG-002', 'Ecografía Abdominal', 'SERVICIO', 180.00, null, 150.00, 'CONVENIO', 'TARIFARIO'],
            ['CONS-003', 'Consulta Pediatría', 'SERVICIO', 180.00, null, 140.00, 'CONVENIO', 'TARIFARIO'],
            ['CONS-004', 'Consulta Ginecología', 'SERVICIO', 200.00, null, 160.00, 'CONVENIO', 'TARIFARIO'],
            ['LAB-004', 'Prueba de Embarazo', 'SERVICIO', 25.00, null, 20.00, 'CONVENIO', 'TARIFARIO'],
        ];

        // Procedimientos
        $procedimientos = [
            ['PROC-001', 'Sutura simple', 'PROCEDIMIENTO', 180.00, null, 150.00, 'CONVENIO', 'TARIFARIO'],
            ['PROC-002', 'Extracción de uña', 'PROCEDIMIENTO', 220.00, null, 185.00, 'CONVENIO', 'TARIFARIO'],
            ['PROC-003', 'Drenaje de absceso', 'PROCEDIMIENTO', 280.00, null, 235.00, 'CONVENIO', 'TARIFARIO'],
            ['PROC-004', 'Curación compleja', 'PROCEDIMIENTO', 150.00, null, 120.00, 'CONVENIO', 'TARIFARIO'],
            ['PROC-005', 'Inmovilización de extremidad', 'PROCEDIMIENTO', 200.00, null, 160.00, 'CONVENIO', 'TARIFARIO'],
            ['PROC-006', 'Aplicación de yeso', 'PROCEDIMIENTO', 250.00, null, 200.00, 'CONVENIO', 'TARIFARIO'],
            ['PROC-007', 'Nebulización', 'PROCEDIMIENTO', 80.00, null, 60.00, 'CONVENIO', 'TARIFARIO'],
            ['PROC-008', 'Electrocardiograma', 'PROCEDIMIENTO', 120.00, null, 95.00, 'CONVENIO', 'TARIFARIO'],
        ];

        // Cirugías
        $cirugias = [
            ['CIR-001', 'Apendicectomía', 'CIRUGIA', 3500.00, null, 2800.00, 'CONVENIO', 'TARIFARIO'],
            ['CIR-002', 'Colecistectomía', 'CIRUGIA', 4200.00, null, 3400.00, 'CONVENIO', 'TARIFARIO'],
            ['CIR-003', 'Hernia inguinal', 'CIRUGIA', 3800.00, null, 3000.00, 'CONVENIO', 'TARIFARIO'],
            ['CIR-004', 'Cesárea', 'CIRUGIA', 4500.00, null, 3600.00, 'CONVENIO', 'TARIFARIO'],
            ['CIR-005', 'Histerectomía', 'CIRUGIA', 5500.00, null, 4400.00, 'CONVENIO', 'TARIFARIO'],
            ['CIR-006', 'Artroscopia de rodilla', 'CIRUGIA', 4800.00, null, 3800.00, 'CONVENIO', 'TARIFARIO'],
            ['CIR-007', 'Catarata (ojo)', 'CIRUGIA', 2200.00, null, 1800.00, 'CONVENIO', 'TARIFARIO'],
            ['CIR-008', 'Amigdalectomía', 'CIRUGIA', 2800.00, null, 2200.00, 'CONVENIO', 'TARIFARIO'],
        ];

        // Insertar datos
        foreach ($servicios as $servicio) {
            Tarifa::create([
                'codigo' => $servicio[0],
                'descripcion' => $servicio[1],
                'categoria' => $servicio[2],
                'precio_particular' => $servicio[3],
                'precio_sis' => $servicio[4],
                'precio_eps' => $servicio[5],
                'tipo_convenio_sis' => $servicio[6],
                'tipo_convenio_eps' => $servicio[7],
            ]);
        }

        foreach ($procedimientos as $procedimiento) {
            Tarifa::create([
                'codigo' => $procedimiento[0],
                'descripcion' => $procedimiento[1],
                'categoria' => $procedimiento[2],
                'precio_particular' => $procedimiento[3],
                'precio_sis' => $procedimiento[4],
                'precio_eps' => $procedimiento[5],
                'tipo_convenio_sis' => $procedimiento[6],
                'tipo_convenio_eps' => $procedimiento[7],
            ]);
        }

        foreach ($cirugias as $cirugia) {
            Tarifa::create([
                'codigo' => $cirugia[0],
                'descripcion' => $cirugia[1],
                'categoria' => $cirugia[2],
                'precio_particular' => $cirugia[3],
                'precio_sis' => $cirugia[4],
                'precio_eps' => $cirugia[5],
                'tipo_convenio_sis' => $cirugia[6],
                'tipo_convenio_eps' => $cirugia[7],
            ]);
        }
    }
}
