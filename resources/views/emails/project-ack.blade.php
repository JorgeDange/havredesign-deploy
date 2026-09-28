<x-mail::message>
# Pedido de projeto recebido

Olá {{ $project->user_name }},

Recebemos o seu pedido e a nossa equipa vai analisá-lo em breve. Guarde a referência abaixo.

<x-mail::panel>
**Referência:** {{ $ref }}<br>
**Tipo:** {{ $project->project_type }}<br>
**Localização:** {{ $project->location ?: '—' }}<br>
**Orçamento:** {{ $project->budget ?: '—' }}<br>
**Prazo:** {{ $project->timeline ?: '—' }}
</x-mail::panel>

@if($project->attachments->isNotEmpty())
**Anexos recebidos:** {{ $project->attachments->pluck('original_name')->join(', ') }}
@endif

A seguir contactaremos consigo através de {{ $project->preferred_channel === 'phone' ? 'telefone' : ($project->preferred_channel === 'whatsapp' ? 'WhatsApp' : 'e-mail') }} para confirmar os detalhes e apresentar a nossa proposta.

<x-mail::button :url="route('home')">
Visitar o site
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
