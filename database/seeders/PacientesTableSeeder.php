<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PacientesTableSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = [
            [
                'ci' => 123456789,
                'nombre' => 'Juan Carlos Pérez Rodríguez',
                'sexo' => 'Masculino',
                'direccion' => 'Av. Principal #123, Zona 1',
                'telefono' => 5551234,
                'correo' => 'juan.perez@email.com',
                'codigo_seguro' => 1001,
                'id_triage' => 'TRI004',
                'codigo_registro' => 'REG001'
            ],
            [
                'ci' => 234567890,
                'nombre' => 'María Elena García López',
                'sexo' => 'Femenino',
                'direccion' => 'Calle Secundaria #456, Zona 2',
                'telefono' => 5555678,
                'correo' => 'maria.garcia@email.com',
                'codigo_seguro' => 1002,
                'id_triage' => 'TRI003',
                'codigo_registro' => 'REG002'
            ],
            [
                'ci' => 345678901,
                'nombre' => 'Carlos Andrés Rodríguez Martínez',
                'sexo' => 'Masculino',
                'direccion' => 'Plaza Central #789, Zona 3',
                'telefono' => 5559012,
                'correo' => 'carlos.rodriguez@email.com',
                'codigo_seguro' => 1003,
                'id_triage' => 'TRI002',
                'codigo_registro' => 'REG003'
            ],
            [
                'ci' => 456789012,
                'nombre' => 'Ana Sofía Martínez Sánchez',
                'sexo' => 'Femenino',
                'direccion' => 'Av. Universidad #101, Zona 4',
                'telefono' => 5553456,
                'correo' => 'ana.martinez@email.com',
                'codigo_seguro' => 1004,
                'id_triage' => 'TRI004',
                'codigo_registro' => 'REG004'
            ],
            [
                'ci' => 567890123,
                'nombre' => 'Luis Fernando Sánchez Hernández',
                'sexo' => 'Masculino',
                'direccion' => 'Calle Comercio #202, Zona 5',
                'telefono' => 5557890,
                'correo' => 'luis.sanchez@email.com',
                'codigo_seguro' => 1005,
                'id_triage' => 'TRI001',
                'codigo_registro' => 'REG005'
            ],
            [
                'ci' => 678901234,
                'nombre' => 'Rosa Elena González Pérez',
                'sexo' => 'Femenino',
                'direccion' => 'Av. de la Salud #100, Zona 6',
                'telefono' => 5551111,
                'correo' => 'rosa.gonzalez@email.com',
                'codigo_seguro' => 1001,
                'id_triage' => 'TRI001',
                'codigo_registro' => 'REG006'
            ],
            [
                'ci' => 789012345,
                'nombre' => 'Pedro José Ramírez Díaz',
                'sexo' => 'Masculino',
                'direccion' => 'Calle Médicos #303, Zona 7',
                'telefono' => 5552222,
                'correo' => 'pedro.ramirez@email.com',
                'codigo_seguro' => 1006,
                'id_triage' => 'TRI003',
                'codigo_registro' => 'REG007'
            ],
            [
                'ci' => 890123456,
                'nombre' => 'Carmen Lucía Vargas Ruiz',
                'sexo' => 'Femenino',
                'direccion' => 'Av. Norte #500, Zona 8',
                'telefono' => 5553333,
                'correo' => 'carmen.vargas@email.com',
                'codigo_seguro' => 1007,
                'id_triage' => 'TRI004',
                'codigo_registro' => 'REG008'
            ],
            [
                'ci' => 901234567,
                'nombre' => 'Jorge Alberto Torres Silva',
                'sexo' => 'Masculino',
                'direccion' => 'Calle Este #404, Zona 9',
                'telefono' => 5554444,
                'correo' => 'jorge.torres@email.com',
                'codigo_seguro' => 1008,
                'id_triage' => 'TRI003',
                'codigo_registro' => 'REG009'
            ],
            [
                'ci' => 112233445,
                'nombre' => 'Diana Patricia Morales Castro',
                'sexo' => 'Femenino',
                'direccion' => 'Zona Industrial #606, Zona 10',
                'telefono' => 5555555,
                'correo' => 'diana.morales@email.com',
                'codigo_seguro' => 1009,
                'id_triage' => 'TRI004',
                'codigo_registro' => 'REG010'
            ],
            [
                'ci' => 223344556,
                'nombre' => 'Roberto Carlos Herrera López',
                'sexo' => 'Masculino',
                'direccion' => 'Av. Vecinal #707, Zona 11',
                'telefono' => 5556666,
                'correo' => 'roberto.herrera@email.com',
                'codigo_seguro' => 1010,
                'id_triage' => 'TRI004',
                'codigo_registro' => 'REG011'
            ],
            [
                'ci' => 334455667,
                'nombre' => 'María Fernanda Ortiz Gómez',
                'sexo' => 'Femenino',
                'direccion' => 'Calle Rehabilitación #808, Zona 12',
                'telefono' => 5557777,
                'correo' => 'maria.ortiz@email.com',
                'codigo_seguro' => 1011,
                'id_triage' => 'TRI003',
                'codigo_registro' => 'REG012'
            ],
            [
                'ci' => 445566778,
                'nombre' => 'Andrés Felipe Castillo Pérez',
                'sexo' => 'Masculino',
                'direccion' => 'Av. Niños #111, Zona 13',
                'telefono' => 5558888,
                'correo' => 'andres.castillo@email.com',
                'codigo_seguro' => 1012,
                'id_triage' => 'TRI004',
                'codigo_registro' => 'REG013'
            ],
            [
                'ci' => 556677889,
                'nombre' => 'Laura Valentina Méndez Díaz',
                'sexo' => 'Femenino',
                'direccion' => 'Calle Dentistas #909, Zona 14',
                'telefono' => 5559999,
                'correo' => 'lara.mendez@email.com',
                'codigo_seguro' => 1013,
                'id_triage' => 'TRI003',
                'codigo_registro' => 'REG014'
            ],
            [
                'ci' => 667788900,
                'nombre' => 'Santiago Alejandro Romero Martínez',
                'sexo' => 'Masculino',
                'direccion' => 'Plaza Central #1010, Zona 15',
                'telefono' => 5550000,
                'correo' => 'santiago.romero@email.com',
                'codigo_seguro' => 1014,
                'id_triage' => 'TRI003',
                'codigo_registro' => 'REG015'
            ],
            [
                'ci' => 778899011,
                'nombre' => 'Isabella Camila Navarro López',
                'sexo' => 'Femenino',
                'direccion' => 'Av. Laboratorios #1111, Zona 16',
                'telefono' => 5551212,
                'correo' => 'isabella.navarro@email.com',
                'codigo_seguro' => 1015,
                'id_triage' => 'TRI002',
                'codigo_registro' => 'REG016'
            ],
            [
                'ci' => 889900122,
                'nombre' => 'Diego Andrés Fuentes Rodríguez',
                'sexo' => 'Masculino',
                'direccion' => 'Calle Emergencia #1212, Zona 17',
                'telefono' => 5553434,
                'correo' => 'diego.fuentes@email.com',
                'codigo_seguro' => 1001,
                'id_triage' => 'TRI001',
                'codigo_registro' => 'REG017'
            ],
            [
                'ci' => 990011233,
                'nombre' => 'Valentina Sofía Herrera García',
                'sexo' => 'Femenino',
                'direccion' => 'Av. Maternidad #1313, Zona 18',
                'telefono' => 5555656,
                'correo' => 'valentina.herrera@email.com',
                'codigo_seguro' => 1002,
                'id_triage' => 'TRI002',
                'codigo_registro' => 'REG018'
            ],
            [
                'ci' => 001122334,
                'nombre' => 'Mateo Sebastián Vargas Sánchez',
                'sexo' => 'Masculino',
                'direccion' => 'Calle Quirúrgica #1414, Zona 19',
                'telefono' => 5557878,
                'correo' => 'mateo.vargas@email.com',
                'codigo_seguro' => 1003,
                'id_triage' => 'TRI001',
                'codigo_registro' => 'REG019'
            ],
            [
                'ci' => 122334455,
                'nombre' => 'Emily Gabriela Castillo Martínez',
                'sexo' => 'Femenino',
                'direccion' => 'Av. UCI #1515, Zona 20',
                'telefono' => 5559090,
                'correo' => 'emily.castillo@email.com',
                'codigo_seguro' => 1004,
                'id_triage' => 'TRI001',
                'codigo_registro' => 'REG020'
            ],
        ];

        DB::table('pacientes')->insert($pacientes);
    }
}
