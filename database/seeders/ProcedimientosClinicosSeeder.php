<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcedimientosClinicosSeeder extends Seeder
{
    public function run(): void
    {
        $procedimientos = [

            // =========================
            // EMERGENCIA
            // =========================
            [
                'nombre' => 'Consulta de emergencia',
                'area' => 'Emergencia',
                'precio' => 120,
                'descripcion' => 'Evaluación médica inicial en el servicio de emergencia.',
            ],
            [
                'nombre' => 'Colocación de vía periférica',
                'area' => 'Emergencia',
                'precio' => 35,
                'descripcion' => 'Canalización venosa periférica para administración de medicamentos.',
            ],
            [
                'nombre' => 'Nebulización',
                'area' => 'Emergencia',
                'precio' => 45,
                'descripcion' => 'Administración de medicamentos por vía inhalatoria.',
            ],
            [
                'nombre' => 'Suturas simples',
                'area' => 'Emergencia',
                'precio' => 180,
                'descripcion' => 'Cierre de heridas simples con puntos.',
            ],
            [
                'nombre' => 'Curación de heridas',
                'area' => 'Emergencia',
                'precio' => 70,
                'descripcion' => 'Limpieza y tratamiento básico de heridas.',
            ],
            [
                'nombre' => 'Lavado gástrico',
                'area' => 'Emergencia',
                'precio' => 350,
                'descripcion' => 'Procedimiento para evacuación del contenido gástrico.',
            ],
            [
                'nombre' => 'Monitoreo cardíaco',
                'area' => 'Emergencia',
                'precio' => 90,
                'descripcion' => 'Control y vigilancia de signos cardíacos.',
            ],
            [
                'nombre' => 'Oxigenoterapia',
                'area' => 'Emergencia',
                'precio' => 60,
                'descripcion' => 'Administración de oxígeno suplementario.',
            ],

            // =========================
            // UTI
            // =========================
            [
                'nombre' => 'Internación en UTI por día',
                'area' => 'UTI',
                'precio' => 2500,
                'descripcion' => 'Uso de cama y monitoreo intensivo en terapia intensiva.',
            ],
            [
                'nombre' => 'Ventilación mecánica',
                'area' => 'UTI',
                'precio' => 1800,
                'descripcion' => 'Soporte respiratorio mecánico invasivo.',
            ],
            [
                'nombre' => 'Colocación de catéter central',
                'area' => 'UTI',
                'precio' => 950,
                'descripcion' => 'Inserción de acceso venoso central.',
            ],
            [
                'nombre' => 'Monitoreo hemodinámico',
                'area' => 'UTI',
                'precio' => 650,
                'descripcion' => 'Control avanzado de parámetros cardiovasculares.',
            ],
            [
                'nombre' => 'Aspiración de secreciones',
                'area' => 'UTI',
                'precio' => 120,
                'descripcion' => 'Extracción de secreciones respiratorias.',
            ],
            [
                'nombre' => 'Gasometría arterial',
                'area' => 'UTI',
                'precio' => 150,
                'descripcion' => 'Análisis de gases en sangre arterial.',
            ],
            [
                'nombre' => 'Balance hídrico',
                'area' => 'UTI',
                'precio' => 80,
                'descripcion' => 'Control de ingresos y egresos de líquidos.',
            ],

            // =========================
            // INTERNACION
            // =========================
            [
                'nombre' => 'Internación en sala general por día',
                'area' => 'Internacion',
                'precio' => 450,
                'descripcion' => 'Uso de cama hospitalaria en sala general.',
            ],
            [
                'nombre' => 'Control de signos vitales',
                'area' => 'Internacion',
                'precio' => 40,
                'descripcion' => 'Monitoreo de presión arterial, pulso y temperatura.',
            ],
            [
                'nombre' => 'Administración de medicamentos',
                'area' => 'Internacion',
                'precio' => 55,
                'descripcion' => 'Aplicación de medicamentos prescritos.',
            ],
            [
                'nombre' => 'Curación avanzada',
                'area' => 'Internacion',
                'precio' => 120,
                'descripcion' => 'Tratamiento especializado de heridas.',
            ],
            [
                'nombre' => 'Colocación de sonda Foley',
                'area' => 'Internacion',
                'precio' => 160,
                'descripcion' => 'Inserción de sonda urinaria.',
            ],
            [
                'nombre' => 'Colocación de sonda nasogástrica',
                'area' => 'Internacion',
                'precio' => 180,
                'descripcion' => 'Inserción de sonda para alimentación o drenaje.',
            ],
            [
                'nombre' => 'Baño asistido',
                'area' => 'Internacion',
                'precio' => 50,
                'descripcion' => 'Higiene asistida al paciente hospitalizado.',
            ],

            // =========================
            // CIRUGIA
            // =========================
            [
                'nombre' => 'Derecho de quirófano menor',
                'area' => 'Cirugia',
                'precio' => 1200,
                'descripcion' => 'Uso de quirófano para procedimientos menores.',
            ],
            [
                'nombre' => 'Derecho de quirófano mayor',
                'area' => 'Cirugia',
                'precio' => 3500,
                'descripcion' => 'Uso de quirófano para cirugías mayores.',
            ],
            [
                'nombre' => 'Apendicectomía',
                'area' => 'Cirugia',
                'precio' => 8500,
                'descripcion' => 'Cirugía para extracción del apéndice.',
            ],
            [
                'nombre' => 'Cesárea',
                'area' => 'Cirugia',
                'precio' => 12000,
                'descripcion' => 'Procedimiento quirúrgico para nacimiento.',
            ],
            [
                'nombre' => 'Colecistectomía',
                'area' => 'Cirugia',
                'precio' => 14000,
                'descripcion' => 'Extracción quirúrgica de la vesícula biliar.',
            ],
            [
                'nombre' => 'Sutura quirúrgica compleja',
                'area' => 'Cirugia',
                'precio' => 450,
                'descripcion' => 'Cierre avanzado de heridas quirúrgicas.',
            ],
            [
                'nombre' => 'Anestesia general',
                'area' => 'Cirugia',
                'precio' => 2200,
                'descripcion' => 'Administración de anestesia general.',
            ],
            [
                'nombre' => 'Anestesia raquídea',
                'area' => 'Cirugia',
                'precio' => 1600,
                'descripcion' => 'Bloqueo anestésico espinal.',
            ],
        ];

        DB::table('procedimientos')->insert($procedimientos);
    }
}
