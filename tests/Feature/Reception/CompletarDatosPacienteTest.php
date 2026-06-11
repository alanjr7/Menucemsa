<?php

namespace Tests\Feature\Reception;

use App\Models\Emergency;
use App\Models\Evaluacion;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompletarDatosPacienteTest extends TestCase
{
    use RefreshDatabase;

    private function receptionUser(): User
    {
        return User::factory()->create(['role' => 'reception', 'is_active' => true]);
    }

    /** Crea un paciente temporal con una emergencia activa y sus registros hijos. */
    private function emergenciaTemporal(User $user): array
    {
        $temp = Paciente::crearTemporal(['nombre' => 'Paciente Temporal', 'sexo' => 'M']);

        $emergency = Emergency::crearConCodigo([
            'paciente_id'     => $temp->id,
            'user_id'         => $user->id,
            'status'          => 'recibido',
            'tipo_ingreso'    => 'general',
            'destino_inicial' => 'observacion',
            'symptoms'        => 'Dolor abdominal',
            'admission_date'  => now(),
        ]);

        return [$temp, $emergency];
    }

    public function test_promueve_paciente_temporal_cuando_el_ci_es_nuevo(): void
    {
        $user = $this->receptionUser();
        [$temp, $emergency] = $this->emergenciaTemporal($user);

        $response = $this->actingAs($user)->postJson(route('reception.completar-datos-paciente.store'), [
            'emergency_id' => $emergency->id,
            'ci'           => 99887766,
            'nombres'      => 'Juan',
            'apellidos'    => 'Pérez',
            'sexo'         => 'Masculino',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        // El mismo registro temporal fue promovido in-place (no se crea otro paciente).
        $this->assertDatabaseHas('pacientes', [
            'id'        => $temp->id,
            'ci'        => 99887766,
            'is_temp'   => false,
            'temp_code' => null,
        ]);
        $this->assertEquals(1, Paciente::count());

        // Se abrió un episodio real y la emergencia quedó vinculada.
        $emergency->refresh();
        $this->assertNotNull($emergency->episodio_id);
        $this->assertEquals($temp->id, $emergency->paciente_id);
    }

    public function test_fusiona_con_paciente_existente_cuando_el_ci_ya_esta_registrado(): void
    {
        $user = $this->receptionUser();
        [$temp, $emergency] = $this->emergenciaTemporal($user);

        $existente = Paciente::create([
            'ci'      => 12345678,
            'nombre'  => 'María Gómez',
            'sexo'    => 'F',
            'is_temp' => false,
        ]);

        // Registros hijos colgando del paciente temporal.
        $evaluacion = Evaluacion::create([
            'paciente_id'  => $temp->id,
            'area'         => 'emergencia',
            'user_id'      => $user->id,
            'observaciones' => 'Evaluación inicial',
        ]);

        $cuentaId = 'CC-TEST-1';
        DB::table('cuenta_cobros')->insert([
            'id'              => $cuentaId,
            'paciente_id'     => $temp->id,
            'tipo_atencion'   => 'emergencia',
            'referencia_id'   => $emergency->id,
            'referencia_type' => Emergency::class,
            'estado'          => 'pendiente',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // FK restrictiva: sin re-apuntar, el borrado del temporal fallaría.
        DB::table('almacen_entregas_paciente')->insert([
            'paciente_id'  => $temp->id,
            'entregado_por' => $user->id,
            'origen'       => 'emergencia',
            'referencia_id' => $emergency->id,
            'cantidad'     => 1,
            'fecha_entrega' => now(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('reception.completar-datos-paciente.store'), [
            'emergency_id'         => $emergency->id,
            'ci'                   => 12345678,
            'paciente_existente_id' => $existente->id,
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        // El paciente temporal fue eliminado (no quedó huérfano ni duplicado).
        $this->assertDatabaseMissing('pacientes', ['id' => $temp->id]);
        $this->assertEquals(1, Paciente::count());

        // Todo se re-apuntó al paciente existente.
        $this->assertDatabaseHas('emergencies', [
            'id'          => $emergency->id,
            'paciente_id' => $existente->id,
        ]);
        $this->assertDatabaseHas('evaluaciones', [
            'id'          => $evaluacion->id,
            'paciente_id' => $existente->id,
        ]);
        $this->assertDatabaseHas('cuenta_cobros', [
            'id'          => $cuentaId,
            'paciente_id' => $existente->id,
        ]);
        $this->assertDatabaseHas('almacen_entregas_paciente', [
            'paciente_id' => $existente->id,
        ]);

        // Se abrió/reutilizó un episodio para el paciente existente.
        $emergency->refresh();
        $this->assertNotNull($emergency->episodio_id);
        $this->assertDatabaseHas('episodios', [
            'id'          => $emergency->episodio_id,
            'paciente_id' => $existente->id,
            'estado'      => 'abierto',
        ]);
    }

    public function test_rechaza_si_la_emergencia_ya_tiene_paciente_definitivo(): void
    {
        $user = $this->receptionUser();

        $definitivo = Paciente::create([
            'ci'      => 55667788,
            'nombre'  => 'Pedro Real',
            'sexo'    => 'M',
            'is_temp' => false,
        ]);

        $emergency = Emergency::crearConCodigo([
            'paciente_id'    => $definitivo->id,
            'user_id'        => $user->id,
            'status'         => 'recibido',
            'tipo_ingreso'   => 'general',
            'symptoms'       => 'Control',
            'admission_date' => now(),
        ]);

        $this->actingAs($user)->postJson(route('reception.completar-datos-paciente.store'), [
            'emergency_id' => $emergency->id,
            'ci'           => 55667788,
        ])->assertStatus(422)->assertJson(['success' => false]);
    }
}
