<x-mail::message>
# Novo contacto recebido

**Nome:** {{ $message->name }}
**E-mail:** {{ $message->email }}
**Telefone:** {{ $message->phone ?: '—' }}
**Assunto:** {{ $message->subject }}

<x-mail::panel>
{{ $message->message }}
</x-mail::panel>

_Registado a {{ $message->created_at?->format('d/m/Y H:i') }} · IP {{ $message->ip_address ?: '—' }}_

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
