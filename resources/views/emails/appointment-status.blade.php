<x-mail::message>
@if ($novoEstado === 'CONFIRMED')
# Agendamento confirmado

Olá {{ $appointment->user_name }},

O seu agendamento foi **confirmado**.

<x-mail::panel>
**Data:** {{ \Carbon\Carbon::parse($appointment->appt_date)->format('d/m/Y') }} às {{ $hora }}<br>
**Tipo:** {{ $tipoLabel }}<br>
@if($appointment->type === 'SITE' && $appointment->address)
**Morada:** {{ $appointment->address }}<br>
@endif
@if($appointment->meeting_link)
**Ligação (reunião online):** {{ $appointment->meeting_link }}<br>
@endif
@if($appointment->fee_note)
**Nota/taxa:** {{ $appointment->fee_note }}<br>
@endif
**Cliente:** {{ $appointment->user_name }} ({{ $appointment->user_email }} · {{ $appointment->user_phone }})<br>
**Estado:** Confirmado
</x-mail::panel>

Se precisar de alterar alguma coisa, basta **responda a este email** e tratamos do resto.

Obrigado,<br>
{{ config('app.name') }}

@elseif ($novoEstado === 'CANCELLED')
# Agendamento cancelado

Olá {{ $appointment->user_name }},

Lamentamos comunicar que o seu agendamento foi **cancelado**.

<x-mail::panel>
**Data:** {{ \Carbon\Carbon::parse($appointment->appt_date)->format('d/m/Y') }} às {{ $hora }}<br>
**Tipo:** {{ $tipoLabel }}<br>
@if($appointment->type === 'SITE' && $appointment->address)
**Morada:** {{ $appointment->address }}<br>
@endif
@if($appointment->meeting_link)
**Ligação (reunião online):** {{ $appointment->meeting_link }}<br>
@endif
@if($appointment->fee_note)
**Nota/taxa:** {{ $appointment->fee_note }}<br>
@endif
**Cliente:** {{ $appointment->user_name }} ({{ $appointment->user_email }} · {{ $appointment->user_phone }})<br>
**Estado:** Cancelado
</x-mail::panel>

Se ainda pretender conversar connosco, pode pedir um novo horário que reagendamos consigo.

<x-mail::button :url="url('/agendar')">
Pedir novo horário
</x-mail::button>

Se preferir, responda a este email e ajudamos pessoalmente.

Obrigado,<br>
{{ config('app.name') }}

@else
# Agendamento atualizado

Olá {{ $appointment->user_name }},

O estado do seu agendamento foi atualizado.

<x-mail::panel>
**Data:** {{ \Carbon\Carbon::parse($appointment->appt_date)->format('d/m/Y') }} às {{ $hora }}<br>
**Tipo:** {{ $tipoLabel }}<br>
@if($appointment->type === 'SITE' && $appointment->address)
**Morada:** {{ $appointment->address }}<br>
@endif
@if($appointment->meeting_link)
**Ligação (reunião online):** {{ $appointment->meeting_link }}<br>
@endif
@if($appointment->fee_note)
**Nota/taxa:** {{ $appointment->fee_note }}<br>
@endif
**Cliente:** {{ $appointment->user_name }} ({{ $appointment->user_email }} · {{ $appointment->user_phone }})<br>
**Estado:** {{ ['PENDING' => 'Por confirmar', 'CONFIRMED' => 'Confirmado', 'CANCELLED' => 'Cancelado'][$appointment->status] ?? $appointment->status }}
</x-mail::panel>

Qualquer dúvida, responda a este email.

Obrigado,<br>
{{ config('app.name') }}
@endif
</x-mail::message>
