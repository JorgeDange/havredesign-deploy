<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * POST /solicitar-projeto - backend.md 6.1 (tabela project_requests + attachments).
 */
class StoreProjectRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            // Honeypot.
            'homepage' => ['nullable', 'max:0'],
            'user_name' => ['required', 'string', 'max:150'],
            'user_email' => ['required', 'email', 'max:255'],
            'user_phone' => ['required', 'string', 'max:30'],
            // O ui/ chama "Localização do Projeto" e guarda em `location` (address fica livre).
            'location' => ['nullable', 'string', 'max:255'],
            'project_type' => ['required', 'string', 'max:100'],
            'area_approx' => ['nullable', 'string', 'max:50'],
            'project_stage' => ['nullable', 'string', 'max:60'],
            'budget' => ['nullable', 'string', 'max:100'],
            'timeline' => ['nullable', 'string', 'max:100'],
            // Serviço/solução alternativos (pergunta 6 em aberto): ambos opcionais por agora.
            'service_id' => ['nullable', 'string', Rule::exists('services', 'id')],
            'solution_id' => ['nullable', 'string', Rule::exists('solutions', 'id')],
            'segment' => ['nullable', Rule::in([
                'Investimento imobiliário residencial',
                'Habitação própria',
                'Comércio e serviços',
                'Outro',
            ])],
            'preferred_channel' => ['required', Rule::in(['email', 'phone', 'whatsapp'])],
            'preferred_time' => ['nullable', 'string', 'max:60'],
            'description' => ['required', 'string', 'max:10000'],
            'privacy' => ['accepted'],
            // 10 MB/ficheiro, 5 no total (texto do ui/ e backend.md 4.7).
            'projectFiles' => ['nullable', 'array', 'max:5'],
            'projectFiles.*' => ['file', 'max:10240', 'extensions:pdf,jpg,jpeg,png,webp,zip,dwg'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_name' => 'nome completo',
            'user_email' => 'e-mail',
            'user_phone' => 'telefone',
            'location' => 'localização do projeto',
            'project_type' => 'tipo de projeto',
            'area_approx' => 'área aproximada',
            'project_stage' => 'estado do projeto',
            'budget' => 'orçamento previsto',
            'timeline' => 'prazo desejado',
            'service_id' => 'serviço pretendido',
            'solution_id' => 'solução pretendida',
            'segment' => 'segmento',
            'preferred_channel' => 'canal preferido para resposta',
            'preferred_time' => 'horário preferido',
            'description' => 'descrição do projeto',
            'privacy' => 'consentimento de privacidade',
            'projectFiles' => 'anexos',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'homepage.max' => 'Campo inválido.',
            'privacy.accepted' => 'É necessário autorizar o tratamento dos seus dados pessoais.',
            'projectFiles.max' => 'Máximo de 5 anexos.',
            'projectFiles.*.max' => 'Cada anexo pode ter no máximo 10 MB.',
            'projectFiles.*.extensions' => 'Formatos aceites: PDF, JPG, JPEG, PNG, WEBP, ZIP ou DWG.',
        ];
    }
}
