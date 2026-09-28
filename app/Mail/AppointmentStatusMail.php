<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Services\AgendaService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $hora;

    public string $tipoLabel;

    /**
     * @param string $novoEstado PENDING, CONFIRMED ou CANCELLED
     */
    public function __construct(public Appointment $appointment, public string $novoEstado)
    {
        $config = app(AgendaService::class)->config();
        $this->hora = substr((string) $appointment->appt_time, 0, 5);
        $this->tipoLabel = $config['tipos'][$appointment->type]['label'] ?? $appointment->type;
    }

    public function envelope(): Envelope
    {
        $assunto = match ($this->novoEstado) {
            'CONFIRMED' => 'O seu agendamento foi confirmado - HAVREDESIGN',
            'CANCELLED' => 'O seu agendamento foi cancelado - HAVREDESIGN',
            default => 'O seu agendamento foi atualizado - HAVREDESIGN',
        };

        return new Envelope(
            subject: $assunto,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.appointment-status');
    }
}
