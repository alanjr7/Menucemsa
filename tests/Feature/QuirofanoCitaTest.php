<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuirofanoCitaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_quirofano_cita_using_standard_fields(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('quirofano.store'), [
            'patient_name' => 'Juan Perez',
            'procedure_name' => 'Apendicectomía',
            'surgeon_name' => 'Dr. Gómez',
            'scheduled_at' => '2026-03-20 10:30:00',
            'operating_room' => 'QX-01',
        ]);

        $response->assertRedirect(route('quirofano.index'));

        $this->assertDatabaseHas('quirofano_citas', [
            'patient_name' => 'Juan Perez',
            'procedure_name' => 'Apendicectomía',
            'operating_room' => 'QX-01',
        ]);
    }

    public function test_admin_can_create_quirofano_cita_using_alternative_payload_keys(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)
            ->postJson(route('quirofano.store'), [
                'paciente' => 'Ana Torres',
                'cirugia' => 'Colecistectomía',
                'medico' => 'Dra. Silva',
                'fecha' => '2026-03-21',
                'hora' => '14:00',
                'sala' => 'QX-02',
                'observaciones' => 'Paciente en ayunas',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Cita de quirófano creada correctamente.')
            ->assertJsonPath('data.patient_name', 'Ana Torres');

        $this->assertDatabaseHas('quirofano_citas', [
            'patient_name' => 'Ana Torres',
            'procedure_name' => 'Colecistectomía',
            'operating_room' => 'QX-02',
        ]);
    }
}
