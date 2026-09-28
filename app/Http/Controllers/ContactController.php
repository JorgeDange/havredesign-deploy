<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactAckMail;
use App\Mail\ContactReceivedMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $message = ContactMessage::create([
            'name' => trim($request->string('name')->toString()),
            'email' => trim($request->string('email')->toString()),
            'phone' => $request->filled('phone') ? trim($request->string('phone')->toString()) : null,
            'subject' => trim($request->string('subject')->toString()),
            'message' => $request->string('message')->toString(),
            'privacy_consented_at' => null, // o formulário de contacto não tem caixa de consentimento (ui/).
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        // backend.md 6.3: falha de envio nunca perde o pedido - grava e regista no log.
        try {
            Mail::to(config('mail.from.address'))->send(new ContactReceivedMail($message));
        } catch (\Throwable $e) {
            Log::error('contact.received_mail_failed', ['id' => $message->id, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to($message->email)->send(new ContactAckMail($message));
        } catch (\Throwable $e) {
            Log::error('contact.ack_mail_failed', ['id' => $message->id, 'error' => $e->getMessage()]);
        }

        return redirect()
            ->route('contacto')
            ->with('success', 'Mensagem enviada com sucesso. Entraremos em contacto o mais breve possível.');
    }
}
