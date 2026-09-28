<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentStatusMail;
use App\Models\Appointment;
use App\Services\AgendaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Estados possíveis de um agendamento (código => rótulo PT).
     *
     * @var array<string, string>
     */
    public const ESTADOS = [
        'PENDING' => 'Por confirmar',
        'CONFIRMED' => 'Confirmado',
        'CANCELLED' => 'Cancelado',
    ];

    public function __construct(private readonly AgendaService $agenda) {}

    /**
     * GET /<ADMIN_PATH>/agendamentos — lista com filtros ?status= e ?data= + contagens.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        if (! array_key_exists($status, self::ESTADOS)) {
            $status = '';
        }

        $data = (string) $request->query('data', '');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            $data = '';
        }

        $base = Appointment::query();
        if ($data !== '') {
            $base->whereDate('appt_date', $data);
        }

        $contagens = ['total' => (clone $base)->count()];
        foreach (self::ESTADOS as $codigo => $rotulo) {
            $contagens[$codigo] = (clone $base)->where('status', $codigo)->count();
        }

        $query = clone $base;
        if ($status !== '') {
            $query->where('status', $status);
        }

        return view('admin.agendamentos.index', [
            'agendamentos' => $query->orderBy('appt_date')->orderBy('appt_time')->paginate(20)->withQueryString(),
            'status' => $status,
            'data' => $data,
            'estados' => self::ESTADOS,
            'contagens' => $contagens,
            'tipos' => $this->tipos(),
        ]);
    }

    /**
     * GET /<ADMIN_PATH>/agendamentos/{appointment} — detalhe + formulário de estado.
     */
    public function show(Appointment $appointment): View
    {
        return view('admin.agendamentos.show', [
            'agendamento' => $appointment,
            'estados' => self::ESTADOS,
            'tipos' => $this->tipos(),
        ]);
    }

    /**
     * PUT /<ADMIN_PATH>/agendamentos/{appointment} — estado + nota/taxa.
     * Quando o estado MUDA, marca confirmed_at/cancelled_at e avisa o cliente.
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $dados = $request->validate(
            [
                'status' => ['required', 'string', Rule::in(array_keys(self::ESTADOS))],
                'fee_note' => ['nullable', 'string', 'max:500'],
            ],
            [],
            ['status' => 'estado', 'fee_note' => 'nota/taxa']
        );

        $novoEstado = (string) $dados['status'];
        $mudou = $novoEstado !== $appointment->status;

        $appointment->status = $novoEstado;
        $appointment->fee_note = $dados['fee_note'] ?? null;

        if ($mudou && $novoEstado === 'CONFIRMED') {
            $appointment->confirmed_at = now();
        }

        if ($mudou && $novoEstado === 'CANCELLED') {
            $appointment->cancelled_at = now();
        }

        $appointment->save();

        if ($mudou && $appointment->user_email) {
            try {
                Mail::to($appointment->user_email)->send(new AppointmentStatusMail($appointment, $novoEstado));
            } catch (\Throwable $e) {
                Log::error('appointment.status_mail_failed', [
                    'id' => $appointment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('agendamentos.show', $appointment)
            ->with('ok', 'Agendamento atualizado.');
    }

    /**
     * Rótulos PT dos tipos (SITE/ONLINE) a partir da configuração da agenda.
     *
     * @return array<string, array<string, mixed>>
     */
    private function tipos(): array
    {
        $tipos = $this->agenda->config()['tipos'];

        $predefinidos = [
            'SITE' => 'No local do cliente',
            'ONLINE' => 'Online',
        ];

        foreach ($predefinidos as $codigo => $rotulo) {
            if (empty($tipos[$codigo]['label'])) {
                $tipos[$codigo]['label'] = $rotulo;
            }
        }

        return $tipos;
    }
}
