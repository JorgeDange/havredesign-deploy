<?php

namespace Tests\Feature;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * B12.1 — POST /agendar + GET /agendar/disponibilidade (backend.md §7).
 */
class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Data futura em dia útil (a agenda recusa domingo=0 e sábado=6 por omissão).
     */
    private function diaUtil(): string
    {
        $d = Carbon::parse('today')->addDays(14);

        while (in_array((int) $d->format('w'), [0, 6], true)) {
            $d->addDay();
        }

        return $d->toDateString();
    }

    private function sabado(): string
    {
        $d = Carbon::parse('today')->addDay();

        while ((int) $d->format('w') !== 6) {
            $d->addDay();
        }

        return $d->toDateString();
    }

    private function payload(array $extra = []): array
    {
        return array_merge([
            'type' => 'ONLINE',
            'appt_date' => $this->diaUtil(),
            'appt_time' => '10:00',
            'user_name' => 'Ana Cliente',
            'user_email' => 'ana@example.com',
            'user_phone' => '+244 922 111 222',
        ], $extra);
    }

    public function test_booking_page_returns_ok(): void
    {
        $this->get('/agendar')->assertStatus(200);
    }

    public function test_valid_appointment_is_persisted_as_pending(): void
    {
        $response = $this->post('/agendar', $this->payload());

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success')
            ->assertSessionHas('appointment_submitted')
            ->assertRedirect(route('agendar'));

        $agendamento = Appointment::sole();
        $this->assertSame('PENDING', $agendamento->status);
        $this->assertSame('Africa/Luanda', $agendamento->timezone);
        $this->assertSame('10:00', substr((string) $agendamento->appt_time, 0, 5));
    }

    public function test_past_date_is_rejected(): void
    {
        $response = $this->post('/agendar', $this->payload([
            'appt_date' => Carbon::yesterday()->toDateString(),
        ]));

        $response->assertSessionHasErrors('appt_date');
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_weekend_date_is_rejected(): void
    {
        $response = $this->post('/agendar', $this->payload([
            'appt_date' => $this->sabado(),
        ]));

        $response->assertSessionHasErrors('appt_date');
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_duplicate_slot_is_rejected(): void
    {
        $data = $this->payload();
        $this->post('/agendar', $data)->assertSessionHasNoErrors();

        $this->post('/agendar', $data)->assertSessionHasErrors('appt_time');
        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_slot_outside_the_published_schedule_is_rejected(): void
    {
        $response = $this->post('/agendar', $this->payload(['appt_time' => '23:30']));

        $response->assertSessionHasErrors('appt_time');
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_site_visit_requires_an_address(): void
    {
        $response = $this->post('/agendar', $this->payload(['type' => 'SITE', 'address' => '']));

        $response->assertSessionHasErrors('address');
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_invalid_meeting_type_is_rejected(): void
    {
        $response = $this->post('/agendar', $this->payload(['type' => 'SMOKE_SIGNAL']));

        $response->assertSessionHasErrors('type');
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_office_meeting_type_is_rejected(): void
    {
        // O tipo OFFICE foi removido — não há atendimento em escritório.
        $response = $this->post('/agendar', $this->payload(['type' => 'OFFICE']));

        $response->assertSessionHasErrors('type');
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_only_site_and_online_types_are_offered(): void
    {
        $html = (string) $this->get('/agendar')->getContent();

        $this->assertStringContainsString("selectType('SITE')", $html);
        $this->assertStringContainsString("selectType('ONLINE')", $html);
        $this->assertStringNotContainsString("selectType('OFFICE')", $html);
        $this->assertStringNotContainsString('Reunião no Escritório', $html);
        $this->assertStringNotContainsString('reunião no escritório', $html);
    }

    public function test_availability_endpoint_returns_free_slots_for_the_month(): void
    {
        $mes = Carbon::parse('today')->addMonthNoOverflow()->startOfMonth()->format('Y-m');

        $response = $this->getJson('/agendar/disponibilidade?mes='.$mes);

        $response->assertStatus(200)->assertJsonStructure(['mes', 'dias']);

        // Domingos e sábados nunca aparecem (diasIndisponiveis = [0, 6]).
        foreach (array_keys($response->json('dias')) as $data) {
            $this->assertNotContains((int) Carbon::parse($data)->format('w'), [0, 6]);
        }
    }

    public function test_availability_endpoint_rejects_a_malformed_month(): void
    {
        $this->getJson('/agendar/disponibilidade?mes=banana')->assertStatus(422);
        $this->getJson('/agendar/disponibilidade?mes=2026-13')->assertStatus(422);
    }

    public function test_taken_slots_disappear_from_availability(): void
    {
        $data = $this->payload();
        $this->post('/agendar', $data)->assertSessionHasNoErrors();

        $mes = Carbon::parse($data['appt_date'])->format('Y-m');
        $dias = $this->getJson('/agendar/disponibilidade?mes='.$mes)->json('dias');

        $this->assertArrayHasKey($data['appt_date'], $dias);
        $this->assertNotContains($data['appt_time'], $dias[$data['appt_date']]);
        $this->assertContains('09:00', $dias[$data['appt_date']]);
    }
}
