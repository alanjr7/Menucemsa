<?php

namespace Database\Seeders;

use App\Models\AlmacenInventario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlmacenInventarioSeeder extends Seeder
{
    public function run(): void
    {
        $productos = $this->getProductosData();
        $total = count($productos);
        $creados = 0;

        foreach ($productos as $producto) {
            try {
                AlmacenInventario::create($producto);
                $creados++;
            } catch (\Exception $e) {
                $this->command->error("Error creando producto {$producto['codigo_activo']}: {$e->getMessage()}");
            }
        }

        $this->command->info("Total de productos de inventario creados: {$creados}/{$total}");
    }

    private function getProductosData(): array
    {
        return [
            // EQUIPO MÉDICO
            ['codigo_activo' => 'EQM001', 'nombre' => 'Monitor de Signos Vitales', 'precio' => 8500.00, 'cantidad' => 5, 'marca' => 'Welch Allyn', 'proveedor' => 'MediTech SRL', 'nro_factura' => 'F001-2024', 'numero_recibo' => 'R001-2024'],
            ['codigo_activo' => 'EQM002', 'nombre' => 'Electrocardiógrafo Portátil', 'precio' => 3200.00, 'cantidad' => 3, 'marca' => 'Schiller', 'proveedor' => 'MediTech SRL', 'nro_factura' => 'F002-2024', 'numero_recibo' => 'R002-2024'],
            ['codigo_activo' => 'EQM003', 'nombre' => 'Báscula Digital Adulto', 'precio' => 450.00, 'cantidad' => 8, 'marca' => 'Seca', 'proveedor' => 'Equipos Médicos Bolivia', 'nro_factura' => 'F003-2024', 'numero_recibo' => 'R003-2024'],
            ['codigo_activo' => 'EQM004', 'nombre' => 'Tensiómetro Aneróide', 'precio' => 120.00, 'cantidad' => 15, 'marca' => 'Omron', 'proveedor' => 'Equipos Médicos Bolivia', 'nro_factura' => 'F004-2024', 'numero_recibo' => 'R004-2024'],
            ['codigo_activo' => 'EQM005', 'nombre' => 'Estetoscopio Cardiológico', 'precio' => 85.00, 'cantidad' => 20, 'marca' => 'Littmann', 'proveedor' => 'Distribuidora Médica', 'nro_factura' => 'F005-2024', 'numero_recibo' => 'R005-2024'],
            ['codigo_activo' => 'EQM006', 'nombre' => 'Oxímetro de Pulso', 'precio' => 180.00, 'cantidad' => 12, 'marca' => 'Contec', 'proveedor' => 'Distribuidora Médica', 'nro_factura' => 'F006-2024', 'numero_recibo' => 'R006-2024'],
            ['codigo_activo' => 'EQM007', 'nombre' => 'Lámpara de Examen', 'precio' => 220.00, 'cantidad' => 6, 'marca' => 'Heine', 'proveedor' => 'MediTech SRL', 'nro_factura' => 'F007-2024', 'numero_recibo' => 'R007-2024'],
            ['codigo_activo' => 'EQM008', 'nombre' => 'Termómetro Digital', 'precio' => 25.00, 'cantidad' => 30, 'marca' => 'Braun', 'proveedor' => 'Farmacia Central', 'nro_factura' => 'F008-2024', 'numero_recibo' => 'R008-2024'],
            ['codigo_activo' => 'EQM009', 'nombre' => 'Negatoscopio Doble', 'precio' => 650.00, 'cantidad' => 4, 'marca' => 'Fujifilm', 'proveedor' => 'Equipos Médicos Bolivia', 'nro_factura' => 'F009-2024', 'numero_recibo' => 'R009-2024'],
            ['codigo_activo' => 'EQM010', 'nombre' => 'Mesa de Examen Médico', 'precio' => 1200.00, 'cantidad' => 3, 'marca' => 'Hill-Rom', 'proveedor' => 'MediTech SRL', 'nro_factura' => 'F010-2024', 'numero_recibo' => 'R010-2024'],

            // INSTRUMENTAL QUIRÚRGICO
            ['codigo_activo' => 'IQS011', 'nombre' => 'Juego de Bisturí Descartable', 'precio' => 45.00, 'cantidad' => 50, 'marca' => 'Swann-Morton', 'proveedor' => 'Distribuidora Quirúrgica', 'nro_factura' => 'F011-2024', 'numero_recibo' => 'R011-2024'],
            ['codigo_activo' => 'IQS012', 'nombre' => 'Pinzas Hemostáticas Curvas', 'precio' => 120.00, 'cantidad' => 25, 'marca' => 'Medicon', 'proveedor' => 'Distribuidora Quirúrgica', 'nro_factura' => 'F012-2024', 'numero_recibo' => 'R012-2024'],
            ['codigo_activo' => 'IQS013', 'nombre' => 'Tijeras Metzenbaum', 'precio' => 95.00, 'cantidad' => 15, 'marca' => 'Medicon', 'proveedor' => 'Instrumental Médico SRL', 'nro_factura' => 'F013-2024', 'numero_recibo' => 'R013-2024'],
            ['codigo_activo' => 'IQS014', 'nombre' => 'Portaagujas Mayo-Hegar', 'precio' => 75.00, 'cantidad' => 20, 'marca' => 'Aesculap', 'proveedor' => 'Instrumental Médico SRL', 'nro_factura' => 'F014-2024', 'numero_recibo' => 'R014-2024'],
            ['codigo_activo' => 'IQS015', 'nombre' => 'Separadores de Farabeuf', 'precio' => 110.00, 'cantidad' => 12, 'marca' => 'Medicon', 'proveedor' => 'Distribuidora Quirúrgica', 'nro_factura' => 'F015-2024', 'numero_recibo' => 'R015-2024'],
            ['codigo_activo' => 'IQS016', 'nombre' => 'Pinzas de Disección', 'precio' => 55.00, 'cantidad' => 30, 'marca' => 'Aesculap', 'proveedor' => 'Instrumental Médico SRL', 'nro_factura' => 'F016-2024', 'numero_recibo' => 'R016-2024'],
            ['codigo_activo' => 'IQS017', 'nombre' => 'Cánulas de Aspiración', 'precio' => 35.00, 'cantidad' => 40, 'marca' => 'Rüsch', 'proveedor' => 'Distribuidora Quirúrgica', 'nro_factura' => 'F017-2024', 'numero_recibo' => 'R017-2024'],
            ['codigo_activo' => 'IQS018', 'nombre' => 'Juego de Forceps', 'precio' => 85.00, 'cantidad' => 18, 'marca' => 'Medicon', 'proveedor' => 'Instrumental Médico SRL', 'nro_factura' => 'F018-2024', 'numero_recibo' => 'R018-2024'],
            ['codigo_activo' => 'IQS019', 'nombre' => 'Cucharillas de Lewin', 'precio' => 65.00, 'cantidad' => 22, 'marca' => 'Aesculap', 'proveedor' => 'Distribuidora Quirúrgica', 'nro_factura' => 'F019-2024', 'numero_recibo' => 'R019-2024'],
            ['codigo_activo' => 'IQS020', 'nombre' => 'Abrebocas de Jennings', 'precio' => 40.00, 'cantidad' => 25, 'marca' => 'Hu-Friedy', 'proveedor' => 'Instrumental Médico SRL', 'nro_factura' => 'F020-2024', 'numero_recibo' => 'R020-2024'],

            // MATERIAL DE LABORATORIO
            ['codigo_activo' => 'LAB021', 'nombre' => 'Microscopio Binocular', 'precio' => 2500.00, 'cantidad' => 2, 'marca' => 'Olympus', 'proveedor' => 'LabSupply SRL', 'nro_factura' => 'F021-2024', 'numero_recibo' => 'R021-2024'],
            ['codigo_activo' => 'LAB022', 'nombre' => 'Centrífuga de Mesa', 'precio' => 1800.00, 'cantidad' => 3, 'marca' => 'Eppendorf', 'proveedor' => 'LabSupply SRL', 'nro_factura' => 'F022-2024', 'numero_recibo' => 'R022-2024'],
            ['codigo_activo' => 'LAB023', 'nombre' => 'Baño María Digital', 'precio' => 420.00, 'cantidad' => 5, 'marca' => 'Julabo', 'proveedor' => 'Equipos de Laboratorio', 'nro_factura' => 'F023-2024', 'numero_recibo' => 'R023-2024'],
            ['codigo_activo' => 'LAB024', 'nombre' => 'Balanza Analítica', 'precio' => 950.00, 'cantidad' => 4, 'marca' => 'Sartorius', 'proveedor' => 'Equipos de Laboratorio', 'nro_factura' => 'F024-2024', 'numero_recibo' => 'R024-2024'],
            ['codigo_activo' => 'LAB025', 'nombre' => 'Agitador Magnético', 'precio' => 180.00, 'cantidad' => 8, 'marca' => 'Thermo Scientific', 'proveedor' => 'LabSupply SRL', 'nro_factura' => 'F025-2024', 'numero_recibo' => 'R025-2024'],
            ['codigo_activo' => 'LAB026', 'nombre' => 'Espectrofotómetro UV-Vis', 'precio' => 3200.00, 'cantidad' => 1, 'marca' => 'Hach', 'proveedor' => 'LabSupply SRL', 'nro_factura' => 'F026-2024', 'numero_recibo' => 'R026-2024'],
            ['codigo_activo' => 'LAB027', 'nombre' => 'Autoclave Eléctrico', 'precio' => 1500.00, 'cantidad' => 2, 'marca' => 'Tuttnauer', 'proveedor' => 'Equipos de Laboratorio', 'nro_factura' => 'F027-2024', 'numero_recibo' => 'R027-2024'],
            ['codigo_activo' => 'LAB028', 'nombre' => 'Cámara de Flujo Laminar', 'precio' => 2800.00, 'cantidad' => 1, 'marca' => 'Labconco', 'proveedor' => 'LabSupply SRL', 'nro_factura' => 'F028-2024', 'numero_recibo' => 'R028-2024'],
            ['codigo_activo' => 'LAB029', 'nombre' => 'Incubadora de CO2', 'precio' => 4200.00, 'cantidad' => 1, 'marca' => 'Panasonic', 'proveedor' => 'Equipos de Laboratorio', 'nro_factura' => 'F029-2024', 'numero_recibo' => 'R029-2024'],
            ['codigo_activo' => 'LAB030', 'nombre' => 'Pipetas Volumétricas Set', 'precio' => 120.00, 'cantidad' => 15, 'marca' => 'Brand', 'proveedor' => 'LabSupply SRL', 'nro_factura' => 'F030-2024', 'numero_recibo' => 'R030-2024'],

            // MOBILIARIO CLÍNICO
            ['codigo_activo' => 'MCL031', 'nombre' => 'Camilla Hospitalaria', 'precio' => 850.00, 'cantidad' => 6, 'marca' => 'Hill-Rom', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F031-2024', 'numero_recibo' => 'R031-2024'],
            ['codigo_activo' => 'MCL032', 'nombre' => 'Silla de Ruedas Estándar', 'precio' => 320.00, 'cantidad' => 8, 'marca' => 'Invacare', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F032-2024', 'numero_recibo' => 'R032-2024'],
            ['codigo_activo' => 'MCL033', 'nombre' => 'Bastón Adjustable', 'precio' => 45.00, 'cantidad' => 20, 'marca' => 'Drive Medical', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F033-2024', 'numero_recibo' => 'R033-2024'],
            ['codigo_activo' => 'MCL034', 'nombre' => 'Andador con Ruedas', 'precio' => 180.00, 'cantidad' => 10, 'marca' => 'Drive Medical', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F034-2024', 'numero_recibo' => 'R034-2024'],
            ['codigo_activo' => 'MCL035', 'nombre' => 'Muletas Axilares Par', 'precio' => 85.00, 'cantidad' => 15, 'marca' => 'Drive Medical', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F035-2024', 'numero_recibo' => 'R035-2024'],
            ['codigo_activo' => 'MCL036', 'nombre' => 'Carro de Curaciones', 'precio' => 450.00, 'cantidad' => 4, 'marca' => 'Harloff', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F036-2024', 'numero_recibo' => 'R036-2024'],
            ['codigo_activo' => 'MCL037', 'nombre' => 'Banco de Examen', 'precio' => 120.00, 'cantidad' => 12, 'marca' => 'Chattanooga', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F037-2024', 'numero_recibo' => 'R037-2024'],
            ['codigo_activo' => 'MCL038', 'nombre' => 'Estante de Acero Inoxidable', 'precio' => 280.00, 'cantidad' => 8, 'marca' => 'Harloff', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F038-2024', 'numero_recibo' => 'R038-2024'],
            ['codigo_activo' => 'MCL039', 'nombre' => 'Botiquín de Primeros Auxilios', 'precio' => 95.00, 'cantidad' => 25, 'marca' => 'First Aid Only', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F039-2024', 'numero_recibo' => 'R039-2024'],
            ['codigo_activo' => 'MCL040', 'nombre' => 'Tablero de Comunicación', 'precio' => 65.00, 'cantidad' => 18, 'marca' => 'Chattanooga', 'proveedor' => 'Mobiliario Médico', 'nro_factura' => 'F040-2024', 'numero_recibo' => 'R040-2024'],

            // EQUIPO DE DIAGNÓSTICO
            ['codigo_activo' => 'EDG041', 'nombre' => 'Ultrasonido Portátil', 'precio' => 15000.00, 'cantidad' => 1, 'marca' => 'SonoSite', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F041-2024', 'numero_recibo' => 'R041-2024'],
            ['codigo_activo' => 'EDG042', 'nombre' => 'Rayos X Digital', 'precio' => 45000.00, 'cantidad' => 1, 'marca' => 'Philips', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F042-2024', 'numero_recibo' => 'R042-2024'],
            ['codigo_activo' => 'EDG043', 'nombre' => 'Tomógrafo Computarizado', 'precio' => 85000.00, 'cantidad' => 1, 'marca' => 'GE Healthcare', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F043-2024', 'numero_recibo' => 'R043-2024'],
            ['codigo_activo' => 'EDG044', 'nombre' => 'Resonancia Magnética', 'precio' => 120000.00, 'cantidad' => 1, 'marca' => 'Siemens', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F044-2024', 'numero_recibo' => 'R044-2024'],
            ['codigo_activo' => 'EDG045', 'nombre' => 'Mamógrafo Digital', 'precio' => 35000.00, 'cantidad' => 1, 'marca' => 'Hologic', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F045-2024', 'numero_recibo' => 'R045-2024'],
            ['codigo_activo' => 'EDG046', 'nombre' => 'Densitómetro Óseo', 'precio' => 22000.00, 'cantidad' => 1, 'marca' => 'Hologic', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F046-2024', 'numero_recibo' => 'R046-2024'],
            ['codigo_activo' => 'EDG047', 'nombre' => 'Holter de 24 Horas', 'precio' => 2800.00, 'cantidad' => 2, 'marca' => 'CardioMem', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F047-2024', 'numero_recibo' => 'R047-2024'],
            ['codigo_activo' => 'EDG048', 'nombre' => 'Electroencefalógrafo', 'precio' => 8500.00, 'cantidad' => 1, 'marca' => 'Natus', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F048-2024', 'numero_recibo' => 'R048-2024'],
            ['codigo_activo' => 'EDG049', 'nombre' => 'Espirómetro Computarizado', 'precio' => 1200.00, 'cantidad' => 3, 'marca' => 'MIR', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F049-2024', 'numero_recibo' => 'R049-2024'],
            ['codigo_activo' => 'EDG050', 'nombre' => 'Audiómetro Clínico', 'precio' => 3200.00, 'cantidad' => 2, 'marca' => 'Interacoustics', 'proveedor' => 'TecnoMed SRL', 'nro_factura' => 'F050-2024', 'numero_recibo' => 'R050-2024'],

            // CONSUMIBLES MÉDICOS
            ['codigo_activo' => 'CSM051', 'nombre' => 'Jeringas Descartables 5ml', 'precio' => 0.85, 'cantidad' => 1000, 'marca' => 'BD', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F051-2024', 'numero_recibo' => 'R051-2024'],
            ['codigo_activo' => 'CSM052', 'nombre' => 'Agujas Hipodérmicas 21G', 'precio' => 0.45, 'cantidad' => 2000, 'marca' => 'BD', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F052-2024', 'numero_recibo' => 'R052-2024'],
            ['codigo_activo' => 'CSM053', 'nombre' => 'Guantes de Latex Talle M', 'precio' => 0.25, 'cantidad' => 5000, 'marca' => 'Semperit', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F053-2024', 'numero_recibo' => 'R053-2024'],
            ['codigo_activo' => 'CSM054', 'nombre' => 'Mascarillas Quirúrgicas', 'precio' => 0.35, 'cantidad' => 3000, 'marca' => '3M', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F054-2024', 'numero_recibo' => 'R054-2024'],
            ['codigo_activo' => 'CSM055', 'nombre' => 'Gasa Esterilizada 10x10', 'precio' => 0.15, 'cantidad' => 2000, 'marca' => 'Kendall', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F055-2024', 'numero_recibo' => 'R055-2024'],
            ['codigo_activo' => 'CSM056', 'nombre' => 'Esparadrapo 5cm x 5m', 'precio' => 2.50, 'cantidad' => 500, 'marca' => '3M', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F056-2024', 'numero_recibo' => 'R056-2024'],
            ['codigo_activo' => 'CSM057', 'nombre' => 'Algodón Hidrófilo 500g', 'precio' => 8.50, 'cantidad' => 100, 'marca' => 'Kendall', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F057-2024', 'numero_recibo' => 'R057-2024'],
            ['codigo_activo' => 'CSM058', 'nombre' => 'Solución Salina 500ml', 'precio' => 3.20, 'cantidad' => 800, 'marca' => 'Baxter', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F058-2024', 'numero_recibo' => 'R058-2024'],
            ['codigo_activo' => 'CSM059', 'nombre' => 'Alcohol Isopropílico 1L', 'precio' => 12.50, 'cantidad' => 200, 'marca' => 'Johnson & Johnson', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F059-2024', 'numero_recibo' => 'R059-2024'],
            ['codigo_activo' => 'CSM060', 'nombre' => 'Povidona Yodada 500ml', 'precio' => 18.00, 'cantidad' => 300, 'marca' => 'B Braun', 'proveedor' => 'Consumibles Médicos SRL', 'nro_factura' => 'F060-2024', 'numero_recibo' => 'R060-2024'],

            // EQUIPO DE REHABILITACIÓN
            ['codigo_activo' => 'ERH061', 'nombre' => 'Bicicleta Estática Médica', 'precio' => 650.00, 'cantidad' => 3, 'marca' => 'Monark', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F061-2024', 'numero_recibo' => 'R061-2024'],
            ['codigo_activo' => 'ERH062', 'nombre' => 'Cinta de Correr Terapéutica', 'precio' => 2800.00, 'cantidad' => 2, 'marca' => 'Woodway', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F062-2024', 'numero_recibo' => 'R062-2024'],
            ['codigo_activo' => 'ERH063', 'nombre' => 'Banda Elástica Terapéutica Set', 'precio' => 45.00, 'cantidad' => 25, 'marca' => 'TheraBand', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F063-2024', 'numero_recibo' => 'R063-2024'],
            ['codigo_activo' => 'ERH064', 'nombre' => 'Pelota Terapéutica 65cm', 'precio' => 35.00, 'cantidad' => 15, 'marca' => 'TheraBand', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F064-2024', 'numero_recibo' => 'R064-2024'],
            ['codigo_activo' => 'ERH065', 'nombre' => 'Paralelas Barras', 'precio' => 450.00, 'cantidad' => 4, 'marca' => 'Chattanooga', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F065-2024', 'numero_recibo' => 'R065-2024'],
            ['codigo_activo' => 'ERH066', 'nombre' => 'Escalera de Dedos', 'precio' => 120.00, 'cantidad' => 8, 'marca' => 'Chattanooga', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F066-2024', 'numero_recibo' => 'R066-2024'],
            ['codigo_activo' => 'ERH067', 'nombre' => 'Plataforma Vibratoria', 'precio' => 2200.00, 'cantidad' => 1, 'marca' => 'Power Plate', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F067-2024', 'numero_recibo' => 'R067-2024'],
            ['codigo_activo' => 'ERH068', 'nombre' => 'Ultrasound Terapéutico', 'precio' => 1800.00, 'cantidad' => 3, 'marca' => 'Chattanooga', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F068-2024', 'numero_recibo' => 'R068-2024'],
            ['codigo_activo' => 'ERH069', 'nombre' => 'TENS Unidad Portátil', 'precio' => 150.00, 'cantidad' => 12, 'marca' => 'Omron', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F069-2024', 'numero_recibo' => 'R069-2024'],
            ['codigo_activo' => 'ERH070', 'nombre' => 'Compresas Frías/Calientes', 'precio' => 25.00, 'cantidad' => 30, 'marca' => 'Chattanooga', 'proveedor' => 'Rehabilitación Médica', 'nro_factura' => 'F070-2024', 'numero_recibo' => 'R070-2024'],

            // EQUIPO DE EMERGENCIA
            ['codigo_activo' => 'EME071', 'nombre' => 'Desfibrilador Externo Automático', 'precio' => 2500.00, 'cantidad' => 4, 'marca' => 'Philips', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F071-2024', 'numero_recibo' => 'R071-2024'],
            ['codigo_activo' => 'EME072', 'nombre' => 'Carro de Paro', 'precio' => 1200.00, 'cantidad' => 2, 'marca' => 'Hill-Rom', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F072-2024', 'numero_recibo' => 'R072-2024'],
            ['codigo_activo' => 'EME073', 'nombre' => 'Bolsa Ambu Adulto', 'precio' => 85.00, 'cantidad' => 15, 'marca' => 'Ambu', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F073-2024', 'numero_recibo' => 'R073-2024'],
            ['codigo_activo' => 'EME074', 'nombre' => 'Laringoscopio Set', 'precio' => 320.00, 'cantidad' => 6, 'marca' => 'Welch Allyn', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F074-2024', 'numero_recibo' => 'R074-2024'],
            ['codigo_activo' => 'EME075', 'nombre' => 'Sistema de Aspiración Portátil', 'precio' => 450.00, 'cantidad' => 5, 'marca' => 'Laerdal', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F075-2024', 'numero_recibo' => 'R075-2024'],
            ['codigo_activo' => 'EME076', 'nombre' => 'Collar Cervical Rígido', 'precio' => 45.00, 'cantidad' => 25, 'marca' => 'Laerdal', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F076-2024', 'numero_recibo' => 'R076-2024'],
            ['codigo_activo' => 'EME077', 'nombre' => 'Férula de Inmovilización', 'precio' => 35.00, 'cantidad' => 30, 'marca' => 'Laerdal', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F077-2024', 'numero_recibo' => 'R077-2024'],
            ['codigo_activo' => 'EME078', 'nombre' => 'Máscara de Reanimación', 'precio' => 25.00, 'cantidad' => 40, 'marca' => 'Laerdal', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F078-2024', 'numero_recibo' => 'R078-2024'],
            ['codigo_activo' => 'EME079', 'nombre' => 'Juego de Vías Aéreas', 'precio' => 120.00, 'cantidad' => 12, 'marca' => 'Ambu', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F079-2024', 'numero_recibo' => 'R079-2024'],
            ['codigo_activo' => 'EME080', 'nombre' => 'Kit de Intubación', 'precio' => 180.00, 'cantidad' => 10, 'marca' => 'Welch Allyn', 'proveedor' => 'Equipos de Emergencia', 'nro_factura' => 'F080-2024', 'numero_recibo' => 'R080-2024'],

            // EQUIPO DE ODONTOLOGÍA
            ['codigo_activo' => 'ODN081', 'nombre' => 'Unidad Dental Completa', 'precio' => 4500.00, 'cantidad' => 2, 'marca' => 'Dental EZ', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F081-2024', 'numero_recibo' => 'R081-2024'],
            ['codigo_activo' => 'ODN082', 'nombre' => 'Motor de Baja Velocidad', 'precio' => 850.00, 'cantidad' => 4, 'marca' => 'NSK', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F082-2024', 'numero_recibo' => 'R082-2024'],
            ['codigo_activo' => 'ODN083', 'nombre' => 'Amalgamador Eléctrico', 'precio' => 320.00, 'cantidad' => 6, 'marca' => 'Dental EZ', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F083-2024', 'numero_recibo' => 'R083-2024'],
            ['codigo_activo' => 'ODN084', 'nombre' => 'Fotocuradora LED', 'precio' => 650.00, 'cantidad' => 5, 'marca' => 'Woodpecker', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F084-2024', 'numero_recibo' => 'R084-2024'],
            ['codigo_activo' => 'ODN085', 'nombre' => 'Radiografía Dental Digital', 'precio' => 2800.00, 'cantidad' => 2, 'marca' => 'Planmeca', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F085-2024', 'numero_recibo' => 'R085-2024'],
            ['codigo_activo' => 'ODN086', 'nombre' => 'Autoclave Dental', 'precio' => 1200.00, 'cantidad' => 3, 'marca' => 'Tuttnauer', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F086-2024', 'numero_recibo' => 'R086-2024'],
            ['codigo_activo' => 'ODN087', 'nombre' => 'Compresor de Aire Dental', 'precio' => 450.00, 'cantidad' => 4, 'marca' => 'Jun-Air', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F087-2024', 'numero_recibo' => 'R087-2024'],
            ['codigo_activo' => 'ODN088', 'nombre' => 'Succión de Saliva', 'precio' => 280.00, 'cantidad' => 6, 'marca' => 'Dental EZ', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F088-2024', 'numero_recibo' => 'R088-2024'],
            ['codigo_activo' => 'ODN089', 'nombre' => 'Juego de Instrumental Dental', 'precio' => 450.00, 'cantidad' => 8, 'marca' => 'Hu-Friedy', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F089-2024', 'numero_recibo' => 'R089-2024'],
            ['codigo_activo' => 'ODN090', 'nombre' => 'Lámpara Curing Light', 'precio' => 180.00, 'cantidad' => 10, 'marca' => 'Woodpecker', 'proveedor' => 'Equipos Dentales SRL', 'nro_factura' => 'F090-2024', 'numero_recibo' => 'R090-2024'],

            // EQUIPO DE OFTALMOLOGÍA
            ['codigo_activo' => 'OFT091', 'nombre' => 'Lámpara de Hendidura', 'precio' => 3200.00, 'cantidad' => 2, 'marca' => 'Haag-Streit', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F091-2024', 'numero_recibo' => 'R091-2024'],
            ['codigo_activo' => 'OFT092', 'nombre' => 'Queratómetro Automático', 'precio' => 1800.00, 'cantidad' => 2, 'marca' => 'Topcon', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F092-2024', 'numero_recibo' => 'R092-2024'],
            ['codigo_activo' => 'OFT093', 'nombre' => 'Tonómetro de No Contacto', 'precio' => 2500.00, 'cantidad' => 1, 'marca' => 'Topcon', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F093-2024', 'numero_recibo' => 'R093-2024'],
            ['codigo_activo' => 'OFT094', 'nombre' => 'Retinógrafo Digital', 'precio' => 8500.00, 'cantidad' => 1, 'marca' => 'Topcon', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F094-2024', 'numero_recibo' => 'R094-2024'],
            ['codigo_activo' => 'OFT095', 'nombre' => 'Campímetro Computarizado', 'precio' => 4200.00, 'cantidad' => 1, 'marca' => 'Haag-Streit', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F095-2024', 'numero_recibo' => 'R095-2024'],
            ['codigo_activo' => 'OFT096', 'nombre' => 'Oftalmoscopio Directo', 'precio' => 450.00, 'cantidad' => 4, 'marca' => 'Welch Allyn', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F096-2024', 'numero_recibo' => 'R096-2024'],
            ['codigo_activo' => 'OFT097', 'nombre' => 'Foróptero Digital', 'precio' => 1200.00, 'cantidad' => 3, 'marca' => 'Topcon', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F097-2024', 'numero_recibo' => 'R097-2024'],
            ['codigo_activo' => 'OFT098', 'nombre' => 'Proyector de Cartas', 'precio' => 650.00, 'cantidad' => 4, 'marca' => 'Topcon', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F098-2024', 'numero_recibo' => 'R098-2024'],
            ['codigo_activo' => 'OFT099', 'nombre' => 'Biomicroscopio', 'precio' => 1800.00, 'cantidad' => 2, 'marca' => 'Haag-Streit', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F099-2024', 'numero_recibo' => 'R099-2024'],
            ['codigo_activo' => 'OFT100', 'nombre' => 'Láser YAG', 'precio' => 15000.00, 'cantidad' => 1, 'marca' => 'Lumenis', 'proveedor' => 'Oftalmología Equipos', 'nro_factura' => 'F100-2024', 'numero_recibo' => 'R100-2024'],
        ];
    }
}
