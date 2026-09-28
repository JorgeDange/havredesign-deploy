<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentBlackout;
use App\Models\AvailabilitySlot;
use App\Models\Setting;
use Carbon\Carbon;

/**
 * Regras da agenda (backend.md §7).
 * Fonte de verdade: `settings.agenda` (+ `availability_slots` como template semanal
 * quando existir; + `appointment_blackouts`; + marcações existentes).
 */
class AgendaService
{
    /**
     * Configuração no formato camelCase usado pelo front-end (AGENDA_CONFIG).
     *
     * @return array<string, mixed>
     */
    public function config(): array
    {
        $a = Setting::get('agenda', []);
        if (! is_array($a)) {
            $a = [];
        }

        $tipos = [];
        foreach (($a['tipos'] ?? []) as $code => $t) {
            $tipos[$code] = [
                'label' => $t['label'] ?? $code,
                'precisaEndereco' => (bool) ($t['precisa_endereco'] ?? ($t['precisaEndereco'] ?? false)),
                'nota' => $t['nota'] ?? null,
            ];
        }

        return [
            'horarios' => array_values($a['horarios'] ?? ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00']),
            'diasIndisponiveis' => array_map('intval', $a['dias_indisponiveis'] ?? [0, 6]),
            'horariosSabado' => array_values($a['horarios_sabado'] ?? []),
            'tipoPredefinido' => $a['tipo_predefinido'] ?? 'ONLINE',
            'tipos' => $tipos,
        ];
    }

    /**
     * Horários-base de uma data (sem marcações ainda).
     *
     * @return array<int, string>
     */
    public function horariosDoDia(string $date): array
    {
        $config = $this->config();
        $dia = Carbon::parse($date);

        if (in_array((int) $dia->format('w'), $config['diasIndisponiveis'], true)) {
            return [];
        }

        $templates = AvailabilitySlot::where('active', 1)
            ->where('weekday', (int) $dia->format('w'))
            ->orderBy('start_time')
            ->get();

        if ($templates->isNotEmpty()) {
            $horarios = [];
            foreach ($templates as $t) {
                $inicio = Carbon::parse($t->start_time);
                $fim = Carbon::parse($t->end_time);
                $passo = max(1, (int) $t->slot_minutes);
                while ($inicio->lt($fim)) {
                    $horarios[] = $inicio->format('H:i');
                    $inicio = $inicio->copy()->addMinutes($passo);
                }
            }

            return array_values(array_unique($horarios));
        }

        if ((int) $dia->format('w') === 6) {
            return $config['horariosSabado'] ?: [];
        }

        return $config['horarios'];
    }

    /**
     * Horários livres de uma data: base − ocupados − blackout − passados.
     *
     * @return array<int, string>
     */
    public function livres(string $date): array
    {
        $base = $this->horariosDoDia($date);
        if ($base === []) {
            return [];
        }

        if (AppointmentBlackout::whereDate('date', $date)->exists()) {
            return [];
        }

        // whereDate(): o cast 'date' grava "YYYY-MM-DD 00:00:00" no sqlite, e uma
        // comparação de strings nua falhava nesse driver (a MySQL converte).
        $ocupados = Appointment::whereDate('appt_date', $date)
            ->where('status', '!=', 'CANCELLED')
            ->pluck('appt_time')
            ->map(fn ($t) => substr((string) $t, 0, 5))
            ->all();

        $agora = now();
        $hoje = $agora->toDateString();

        return array_values(array_filter($base, function (string $h) use ($ocupados, $date, $agora, $hoje): bool {
            if (in_array($h, $ocupados, true)) {
                return false;
            }

            // Antecedência mínima: 1 hora (backend.md §7.5 - prazo a definir com o cliente).
            if ($date === $hoje && Carbon::parse($h)->lte($agora->copy()->addHour())) {
                return false;
            }

            return true;
        }));
    }

    /**
     * Disponibilidade de um mês: [data => horários livres] só para dias com vagas.
     *
     * @return array<string, array<int, string>>
     */
    public function mes(int $year, int $month): array
    {
        $primeiro = Carbon::create($year, $month, 1);
        $hoje = now()->startOfDay();
        $dias = [];

        for ($i = 0; $i < $primeiro->daysInMonth; $i++) {
            $dia = $primeiro->copy()->addDays($i);
            if ($dia->lt($hoje)) {
                continue;
            }

            $livres = $this->livres($dia->toDateString());
            if ($livres !== []) {
                $dias[$dia->toDateString()] = $livres;
            }
        }

        return $dias;
    }

    /**
     * Existe já uma marcação nesse dia/hora (qualquer estado - o índice UNIQUE é por tabela)?
     */
    public function ocupado(string $date, string $time): bool
    {
        return Appointment::whereDate('appt_date', $date)
            ->where('appt_time', $time)
            ->exists();
    }
}
