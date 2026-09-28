<?php

namespace App\Http\Requests;

use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * POST /agendar - backend.md §7 (tabela appointments).
 */
class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'homepage' => ['nullable', 'max:0'],
            'type' => ['required', 'in:SITE,ONLINE'],
            'address' => ['required_if:type,SITE', 'nullable', 'string', 'max:500'],
            'appt_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'appt_time' => ['required', 'date_format:H:i'],
            'user_name' => ['required', 'string', 'max:150'],
            'user_email' => ['required', 'email', 'max:255'],
            'user_phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Regras da agenda que não existem no validador padrão.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $agenda = app(AgendaService::class);
                $date = $this->input('appt_date');
                $time = $this->input('appt_time');

                if (! is_string($date) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                    return;
                }

                $config = $agenda->config();
                $dia = Carbon::parse($date);

                if (in_array((int) $dia->format('w'), $config['diasIndisponiveis'], true)) {
                    $validator->errors()->add('appt_date', 'Não é possível marcar neste dia. Escolha outro dia.');
                }

                if ($validator->errors()->has('appt_date')) {
                    return; // já há erro de data - não acumular erros de horário
                }

                if ($agenda->ocupado($date, (string) $time)) {
                    $validator->errors()->add('appt_time', 'Este horário já está ocupado. Escolha outro horário.');
                }

                if ($validator->errors()->has('appt_time')) {
                    return;
                }

                if (is_string($time) && preg_match('/^\d{2}:\d{2}$/', $time)
                    && ! in_array($time, $agenda->livres($date), true)) {
                    $validator->errors()->add('appt_time', 'Este horário não está disponível para a data escolhida.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => 'tipo de reunião',
            'address' => 'endereço',
            'appt_date' => 'data',
            'appt_time' => 'horário',
            'user_name' => 'nome',
            'user_email' => 'e-mail',
            'user_phone' => 'telefone',
            'notes' => 'observações',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'homepage.max' => 'Campo inválido.',
            'type.in' => 'Tipo de reunião inválido.',
            'address.required_if' => 'Informe o endereço do local para a visita.',
            'appt_date.date_format' => 'Data inválida.',
            'appt_date.after_or_equal' => 'Não é possível marcar no passado.',
            'appt_time.date_format' => 'Horário inválido.',
        ];
    }
}
