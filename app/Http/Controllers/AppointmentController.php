<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Mail\AppointmentAckMail;
use App\Mail\AppointmentReceivedMail;
use App\Models\Appointment;
use App\Services\AgendaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function __construct(private readonly AgendaService $agenda) {}

    /**
     * GET /agendar/disponibilidade?mes=YYYY-MM — slots livres por dia do mês.
     */
    public function availability(Request $request): JsonResponse
    {
        $mes = (string) $request->query('mes', now()->format('Y-m'));

        if (! preg_match('/^(\d{4})-(\d{2})$/', $mes, $m)) {
            return response()->json(['error' => 'Formato de mês inválido.'], 422);
        }

        $year = (int) $m[1];
        $month = (int) $m[2];

        if ($month < 1 || $month > 12) {
            return response()->json(['error' => 'Mês inválido.'], 422);
        }

        return response()->json([
            'mes' => $mes,
            'dias' => $this->agenda->mes($year, $month),
        ]);
    }

    /**
     * POST /agendar — valida e grava a marcação.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $agenda = $this->agenda;
        $data = $request->validated();

        $appointment = Appointment::create([
            'user_id' => auth()->id(),
            'user_name' => trim($data['user_name']),
            'user_email' => trim($data['user_email']),
            'user_phone' => trim($data['user_phone']),
            'type' => $data['type'],
            'address' => $data['type'] === 'SITE' ? ($data['address'] ?? null) : null,
            'appt_date' => $data['appt_date'],
            'appt_time' => $data['appt_time'],
            'notes' => $data['notes'] ?? null,
            'timezone' => 'Africa/Luanda',
            'status' => 'PENDING',
        ]);

        try {
            Mail::to(config('mail.admin.address'))->send(new AppointmentReceivedMail($appointment));
        } catch (\Throwable $e) {
            Log::error('appointment.received_mail_failed', ['id' => $appointment->id, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to($appointment->user_email)->send(new AppointmentAckMail($appointment));
        } catch (\Throwable $e) {
            Log::error('appointment.ack_mail_failed', ['id' => $appointment->id, 'error' => $e->getMessage()]);
        }

        $config = $agenda->config();
        $label = $config['tipos'][$appointment->type]['label'] ?? $appointment->type;

        return redirect()
            ->route('agendar')
            ->with('success', 'Agendamento pedido com sucesso! Iremos confirmar por e-mail.')
            ->with('appointment_submitted', [
                'cliente' => $appointment->user_name.' ('.$appointment->user_email.')',
                'data' => \Carbon\Carbon::parse($appointment->appt_date)->format('d/m/Y'),
                'hora' => substr((string) $appointment->appt_time, 0, 5),
                'tipo' => $label,
                'local' => $appointment->type === 'ONLINE'
                    ? 'Online'
                    : (string) $appointment->address,
                'estado' => 'Por confirmar',
            ]);
    }
}
