<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\CitaQuirurgica;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuirofanoEquipamientoTest extends TestCase
{
    /**
     * Verifica que un modelo CitaQuirurgica pueda castear correctamente el array equipamientos_detalle
     */
    public function test_cita_quirurgica_castea_equipamientos_detalle(): void
    {
        $cita = new CitaQuirurgica();
        $equipos = [
            [
                'nombre' => 'Arco en C (C-Arm)',
                'precio' => 0.00,
                'precio_referencia' => 500.00,
                'cobrar_cuenta' => false,
                'en_paquete' => true,
            ],
            [
                'nombre' => 'Torre de lámparas',
                'precio' => 250.00,
                'precio_referencia' => 250.00,
                'cobrar_cuenta' => true,
                'en_paquete' => false,
            ]
        ];

        $cita->equipamientos_detalle = $equipos;
        $cita->equipamiento_nombre = 'Arco en C (Sin cobro - Ref: Bs. 500.00), Torre de lámparas (Bs. 250.00)';
        $cita->equipamiento_precio = 250.00;

        $this->assertIsArray($cita->equipamientos_detalle);
        $this->assertCount(2, $cita->equipamientos_detalle);
        $this->assertFalse($cita->equipamientos_detalle[0]['cobrar_cuenta']);
        $this->assertEquals(0.00, $cita->equipamientos_detalle[0]['precio']);
        $this->assertEquals(500.00, $cita->equipamientos_detalle[0]['precio_referencia']);
        $this->assertTrue($cita->equipamientos_detalle[1]['cobrar_cuenta']);
        $this->assertEquals(250.00, $cita->equipamientos_detalle[1]['precio']);
    }
}
