<x-mail::message>
# Novo pedido de projeto

**Referência:** {{ $project->id }}

<x-mail::panel>
**Cliente:** {{ $project->user_name }} ({{ $project->user_email }} · {{ $project->user_phone }})<br>
**Localização:** {{ $project->location ?: '—' }}<br>
**Tipo:** {{ $project->project_type }}<br>
**Serviço pretendido:** {{ $project->service?->title ?: '—' }}<br>
**Solução pretendida:** {{ $project->solution?->name ?: '—' }}<br>
**Área:** {{ $project->area_approx ? $project->area_approx . ' m²' : '—' }}<br>
**Estado do projeto:** {{ $project->project_stage ?: '—' }}<br>
**Orçamento:** {{ $project->budget ?: '—' }}<br>
**Prazo:** {{ $project->timeline ?: '—' }}<br>
**Segmento:** {{ $project->segment ?: '—' }}<br>
**Resposta preferida:** {{ $project->preferred_channel }} · {{ $project->preferred_time ?: 'horário indiferente' }}
</x-mail::panel>

**Descrição**

<x-mail::panel>
{{ $project->description }}
</x-mail::panel>

@if($project->attachments->isNotEmpty())
**Anexos**

@foreach($project->attachments as $a)
- {{ $a->original_name }} ({{ number_format($a->size_bytes / 1024, 0) }} KB) — guardado em `{{ $a->path }}`
@endforeach
@endif

_Registado a {{ $project->created_at?->format('d/m/Y H:i') }} · IP {{ $project->ip_address ?: '—' }} · Estado: {{ $project->status }}

{{ config('app.name') }}
</x-mail::message>
