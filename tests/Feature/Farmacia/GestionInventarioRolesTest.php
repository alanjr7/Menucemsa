<?php

namespace Tests\Feature\Farmacia;

use App\Models\AlmacenCatalogo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Mínimo privilegio en inventarios y ventas: Editar / Eliminar / Anular son
 * operaciones administrativas — SOLO admin|administrador. Farmacia y
 * almacenista consultan, registran y venden, pero no alteran el catálogo ni
 * anulan dinero. La restricción vive en las RUTAS/controlador (la vista solo
 * oculta los botones: eso es cosmético, no seguridad).
 */
class GestionInventarioRolesTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rol): User
    {
        return User::factory()->create(['role' => $rol, 'is_active' => true]);
    }

    private function catalogo(): AlmacenCatalogo
    {
        return AlmacenCatalogo::create([
            'nombre' => 'Amoxicilina 500mg',
            'tipo' => 'medicamento',
            'unidad_medida' => 'unidades',
            'activo' => true,
            'requiere_receta' => false,
        ]);
    }

    // --- Farmacia: inventario (PUT/DELETE) ---

    public function test_farmacia_y_almacenista_no_editan_ni_eliminan_inventario(): void
    {
        $cat = $this->catalogo();

        foreach (['farmacia', 'almacenista'] as $rol) {
            $this->actingAs($this->usuario($rol))
                ->putJson("/farmacia/inventario/{$cat->id}", ['nombre' => 'Cambiado'])
                ->assertStatus(403);

            $this->actingAs($this->usuario($rol))
                ->deleteJson("/farmacia/inventario/{$cat->id}")
                ->assertStatus(403);
        }

        // El catálogo no fue tocado por los intentos bloqueados
        $this->assertSame('Amoxicilina 500mg', $cat->fresh()->nombre);
    }

    public function test_admin_si_puede_editar_inventario(): void
    {
        $cat = $this->catalogo();

        $res = $this->actingAs($this->usuario('admin'))
            ->putJson("/farmacia/inventario/{$cat->id}", [
                'nombre' => 'Amoxicilina 500mg cáps.',
                'codigo_barras' => 'ABC-123',
                'categoria' => 'Medicamento',
            ]);

        // No 403: la ruta deja pasar al admin (el resultado depende del controlador)
        $this->assertNotSame(403, $res->status());
    }

    // --- Almacén central: edit/update/destroy ---

    public function test_almacenista_no_edita_ni_desactiva_catalogo_central(): void
    {
        $cat = $this->catalogo();
        $almacenista = $this->usuario('almacenista');

        $this->actingAs($almacenista)
            ->get("/admin/almacen-medicamentos/{$cat->id}/edit")
            ->assertStatus(403);

        $this->actingAs($almacenista)
            ->put("/admin/almacen-medicamentos/{$cat->id}", ['nombre' => 'Hackeado'])
            ->assertStatus(403);

        $this->actingAs($almacenista)
            ->delete("/admin/almacen-medicamentos/{$cat->id}")
            ->assertStatus(403);

        $cat->refresh();
        $this->assertSame('Amoxicilina 500mg', $cat->nombre);
        $this->assertTrue((bool) $cat->activo);
    }

    public function test_almacenista_conserva_sus_funciones_de_inventario(): void
    {
        // La restricción es quirúrgica: el almacenista sigue viendo el almacén
        // central y el detalle del ítem (su trabajo diario no se rompe).
        $cat = $this->catalogo();
        $almacenista = $this->usuario('almacenista');

        $this->actingAs($almacenista)->get('/admin/almacen-medicamentos')->assertOk();
        $this->actingAs($almacenista)->get("/admin/almacen-medicamentos/{$cat->id}")->assertOk();
    }

    public function test_admin_si_accede_a_editar_catalogo_central(): void
    {
        $cat = $this->catalogo();

        $this->actingAs($this->usuario('admin'))
            ->get("/admin/almacen-medicamentos/{$cat->id}/edit")
            ->assertOk();
    }
}
