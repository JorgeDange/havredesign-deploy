<x-mail::message>
# Mensagem recebida

Olá {{ $message->name }},

Recebemos a sua mensagem e a nossa equipa responderá com a maior brevidade possível.

<x-mail::panel>
**Assunto:** {{ $message->subject }}<br>
{{ $message->message }}
</x-mail::panel>

Se preferir adiantar detalhes do seu projeto, pode responder a este e-mail ou falar connosco por WhatsApp.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
