<x-mail::message>
# Pedido de agendamento

Olá {{ $appointment->user_name }},

Recebemos o seu pedido de agendamento. Iremos confirmá-lo por e-mail assim que possível.

<x-mail::panel>
**Data:** {{ \Carbon\Carbon::parse($appointment->appt_date)->format('d/m/Y') }} às {{ $hora }}<br>
**Tipo:** {{ $tipoLabel }}<br>
@if($appointment->address)
**Endereço:** {{ $appointment->address }}<br>
@endif
**Cliente:** {{ $appointment->user_name }} ({{ $appointment->user_email }} · {{ $appointment->user_phone }})<br>
**Estado:** Por confirmar
</x-mail::panel>

Se precisar de alterar alguma coisa, responda a este e-mail ou fale connosco por WhatsApp.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
