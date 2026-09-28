<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Services\AgendaService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $hora;

    public string $tipoLabel;

    public function __construct(public Appointment $appointment)
    {
        $config = app(AgendaService::class)->config();
        $this->hora = substr((string) $appointment->appt_time, 0, 5);
        $this->tipoLabel = $config['tipos'][$appointment->type]['label'] ?? $appointment->type;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Novo pedido de agendamento: '.$this->tipoLabel.' ('.$this->hora.')',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.appointment-received');
    }
}
