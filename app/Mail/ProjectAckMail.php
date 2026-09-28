<?php

namespace App\Mail;

use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProjectAckMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProjectRequest $project) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recebemos o seu pedido de projeto - HAVREDESIGN',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.project-ack', with: ['ref' => $this->ref()]);
    }

    public function ref(): string
    {
        return Str::upper(Str::substr($this->project->id, 0, 8));
    }
}
