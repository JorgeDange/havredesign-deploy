<x-mail::message>
# Novo pedido de agendamento

<x-mail::panel>
**Cliente:** {{ $appointment->user_name }} ({{ $appointment->user_email }} · {{ $appointment->user_phone }})<br>
**Data:** {{ \Carbon\Carbon::parse($appointment->appt_date)->format('d/m/Y') }} às {{ $hora }}<br>
**Tipo:** {{ $tipoLabel }}<br>
@if($appointment->address)
**Endereço:** {{ $appointment->address }}<br>
@endif
@if($appointment->notes)
**Observações:** {{ $appointment->notes }}<br>
@endif
**Estado:** {{ $appointment->status }} · fuso {{ $appointment->timezone }}
</x-mail::panel>

_Registado a {{ $appointment->created_at?->format('d/m/Y H:i') }}_

{{ config('app.name') }}
</x-mail::message>
