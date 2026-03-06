<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CajaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_caja_view_loads_with_backend_data(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        DB::table('CAJA_FARMACIA')->insert([
            [
                'CODIGO' => 'MOV-001',
                'DETALLE' => 'Consulta general',
                'TOTAL' => 150.00,
                'ID_CAJA' => 'CJ1',
                'FECHA' => now(),
            ],
            [
                'CODIGO' => 'MOV-002',
                'DETALLE' => 'Anulación',
                'TOTAL' => -20.00,
                'ID_CAJA' => 'CJ1',
                'FECHA' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get(route('admin.caja.index'));

        $response->assertOk();
        $response->assertSee('Consulta general');
        $response->assertSee('S/ 150.00');
        $response->assertSee('S/ 20.00');
    }
}
