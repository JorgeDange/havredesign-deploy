<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * B10 — Definições do site (tabela settings) — backend.md §4.10.
 * Página única com um único POST: contacto, marca, redes, WhatsApp, agenda, o caso real
 * da página Processo e o QR Code de /networking.
 * A agenda é gravada por substituição parcial das chaves editáveis,
 * preservando o resto do objeto. Os tipos SITE e ONLINE também são editáveis
 * (rótulo + nota), já que a copy muda com frequência.
 */
class SettingController extends Controller
{
    /**
     * Chaves simples editadas no formulário, agrupadas pelo `group` da BD.
     *
     * @var array<string, list<string>>
     */
    private const GRUPOS = [
        'contact' => [
            'contact.email',
            'contact.phone_1',
            'contact.phone_2',
            'contact.phone_3',
            'contact.whatsapp',
            'contact.address_full',
            'contact.hours',
            'contact.hours_lines',
        ],
        'brand' => [
            'brand.name',
            'brand.legal_name',
            'brand.tagline',
        ],
        'social' => [
            'social.instagram',
            'social.facebook',
            'social.linkedin',
        ],
        'whatsapp' => [
            'whatsapp.number',
            'whatsapp.open_message',
        ],
        'process' => [
            'process.case_local',
        ],
    ];

    /**
     * Formato dos horários: uma linha `HH:MM` por valor.
     */
    private const REGEX_HORA = '/^([01]\d|2[0-3]):[0-5]\d$/';

    /**
     * Caminho (relativo a public/) do QR Code da página permanente /networking.
     * Fixo de propósito: o URL nunca muda, mesmo quando a imagem é substituída.
     */
    public const QR_CAMINHO = 'images/qrcode-networking.png';

    /**
     * Tipos de reunião aceites — OFFICE foi removido (sem atendimento em escritório).
     *
     * @var list<string>
     */
    private const CODIGOS_TIPO = ['SITE', 'ONLINE'];

    public function index(): View
    {
        $qrCaminho = (string) (Setting::get('networking.qr_image') ?: self::QR_CAMINHO);

        return view('admin.definicoes.index', [
            'valores' => Setting::many(array_merge(...array_values(self::GRUPOS))),
            'agenda' => self::agenda(),
            'qrCaminho' => $qrCaminho,
            'qrExiste' => file_exists(public_path($qrCaminho)),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validador = Validator::make(
            $request->all(),
            self::REGRAS,
            self::MENSAGENS,
            self::ATRIBUTOS
        );

        $validador->after(function (ValidatorContract $validacao) use ($request): void {
            foreach (['agenda.horarios', 'agenda.horarios_sabado'] as $campo) {
                foreach ($this->linhas($request->input($campo)) as $linha) {
                    if (preg_match(self::REGEX_HORA, $linha) !== 1) {
                        $validacao->errors()->add(
                            $campo,
                            'Horário «'.$linha.'» inválido. Use o formato HH:MM (ex.: 09:30).'
                        );
                        break;
                    }
                }
            }

            foreach (array_keys((array) $request->input('agenda.tipos', [])) as $codigo) {
                if (! in_array($codigo, self::CODIGOS_TIPO, true)) {
                    $validacao->errors()->add('agenda.tipos', 'Tipo de reunião inválido.');
                    break;
                }
            }
        });

        if ($validador->fails()) {
            return redirect()
                ->route('definicoes.index')
                ->withErrors($validador)
                ->withInput();
        }

        foreach (self::GRUPOS as $grupo => $chaves) {
            foreach ($chaves as $chave) {
                Setting::set($chave, trim((string) $request->input($chave, '')), $grupo);
            }
        }

        $agenda = self::agenda();
        $agenda['horarios'] = $this->linhas($request->input('agenda.horarios'));
        $agenda['horarios_sabado'] = $this->linhas($request->input('agenda.horarios_sabado'));
        $agenda['dias_indisponiveis'] = array_values(array_map(
            'intval',
            (array) $request->input('agenda.dias_indisponiveis', [])
        ));
        $agenda['tipo_predefinido'] = (string) $request->input('agenda.tipo_predefinido', 'ONLINE');
        $agenda['tipos'] = $this->tiposEditados($request, (array) ($agenda['tipos'] ?? []));

        Setting::set('agenda', $agenda, 'agenda');

        // QR Code de /networking — escrito no mesmo caminho sempre, para o URL nunca mudar.
        if ($request->hasFile('qr_image')) {
            $caminho = $request->file('qr_image')->storeAs(
                dirname(self::QR_CAMINHO),
                basename(self::QR_CAMINHO),
                'web'
            );

            if (is_string($caminho) && $caminho !== '') {
                Setting::set('networking.qr_image', $caminho, 'networking');
            }
        }

        return redirect()
            ->route('definicoes.index')
            ->with('ok', 'Definições atualizadas.');
    }

    /**
     * Objeto agenda atual (sempre um array).
     *
     * @return array<string, mixed>
     */
    private static function agenda(): array
    {
        $agenda = Setting::get('agenda', []) ?: [];

        return is_array($agenda) ? $agenda : [];
    }

    /**
     * Tipos de reunião submetidos, mesclados sobre os atuais.
     *
     * `precisa_endereco` não é editável: é semântica (só a visita ao local
     * precisa de morada) e não pode ser alterada por acidente no painel.
     *
     * @return array<string, array<string, mixed>>
     */
    private function tiposEditados(Request $request, array $existentes): array
    {
        $tipos = [];

        foreach (self::CODIGOS_TIPO as $codigo) {
            $atual = is_array($existentes[$codigo] ?? null) ? $existentes[$codigo] : [];

            $rotulo = $request->input("agenda.tipos.{$codigo}.label", $atual['label'] ?? $codigo);
            $nota = $request->input("agenda.tipos.{$codigo}.nota", $atual['nota'] ?? '');

            $tipos[$codigo] = [
                'label' => trim((string) $rotulo),
                'precisa_endereco' => $codigo === 'SITE',
                'nota' => trim((string) $nota),
            ];
        }

        return $tipos;
    }

    /**
     * Uma linha por valor — texto vazio devolve lista vazia.
     *
     * @return list<string>
     */
    private function linhas(mixed $texto): array
    {
        if (! is_string($texto) || trim($texto) === '') {
            return [];
        }

        $partes = preg_split('/\r\n|\r|\n/', $texto) ?: [];

        return array_values(array_filter(
            array_map('trim', $partes),
            static fn (string $linha): bool => $linha !== ''
        ));
    }

    /** @var array<string, list<string>> */
    private const REGRAS = [
        'contact.email' => ['nullable', 'email', 'max:255'],
        'contact.phone_1' => ['nullable', 'string', 'max:30'],
        'contact.phone_2' => ['nullable', 'string', 'max:30'],
        'contact.phone_3' => ['nullable', 'string', 'max:30'],
        'contact.whatsapp' => ['nullable', 'string', 'max:30'],
        'contact.address_full' => ['nullable', 'string', 'max:300'],
        'contact.hours' => ['nullable', 'string', 'max:200'],
        'contact.hours_lines' => ['nullable', 'string', 'max:500'],
        'brand.name' => ['nullable', 'string', 'max:120'],
        'brand.legal_name' => ['nullable', 'string', 'max:200'],
        'brand.tagline' => ['nullable', 'string', 'max:255'],
        'social.instagram' => ['nullable', 'url', 'max:255'],
        'social.facebook' => ['nullable', 'url', 'max:255'],
        'social.linkedin' => ['nullable', 'url', 'max:255'],
        'whatsapp.number' => ['nullable', 'string', 'max:30'],
        'whatsapp.open_message' => ['nullable', 'string', 'max:1000'],
        'process.case_local' => ['nullable', 'string', 'max:255'],
        'qr_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        'agenda.horarios' => ['required', 'string'],
        'agenda.horarios_sabado' => ['nullable', 'string'],
        'agenda.dias_indisponiveis' => ['nullable', 'array'],
        'agenda.dias_indisponiveis.*' => ['integer', 'between:0,6'],
        'agenda.tipo_predefinido' => ['required', 'in:SITE,ONLINE'],
        'agenda.tipos' => ['required', 'array'],
        'agenda.tipos.*.label' => ['required', 'string', 'max:120'],
        'agenda.tipos.*.nota' => ['nullable', 'string', 'max:300'],
    ];

    /** @var array<string, string> */
    private const MENSAGENS = [
        'contact.email.email' => 'O e-mail introducido não é válido.',
        'social.instagram.url' => 'O endereço do Instagram tem de ser um URL válido (ex.: https://instagram.com/…).',
        'social.facebook.url' => 'O endereço do Facebook tem de ser um URL válido (ex.: https://facebook.com/…).',
        'social.linkedin.url' => 'O endereço do LinkedIn tem de ser um URL válido (ex.: https://linkedin.com/…).',
        'agenda.horarios.required' => 'Indique pelo menos um horário.',
        'agenda.tipo_predefinido.required' => 'Escolha o tipo de reunião pré-definido.',
        'agenda.tipo_predefinido.in' => 'Tipo de reunião inválido.',
        'agenda.dias_indisponiveis.*.integer' => 'Dia da semana inválido.',
        'agenda.dias_indisponiveis.*.between' => 'Dia da semana inválido.',
        'agenda.tipos.*.label.required' => 'O rótulo do tipo de reunião não pode ficar vazio.',
        'agenda.tipos.*.label.max' => 'O rótulo do tipo de reunião é demasiado longo.',
        'agenda.tipos.*.nota.max' => 'A nota do tipo de reunião é demasiado longa.',
        'qr_image.image' => 'O QR Code tem de ser uma imagem.',
        'qr_image.mimes' => 'Formato de imagem aceite: PNG, JPEG ou WebP.',
        'qr_image.max' => 'A imagem do QR Code não pode ultrapassar 4 MB.',
    ];

    /** @var array<string, string> */
    private const ATRIBUTOS = [
        'contact.email' => 'e-mail',
        'contact.phone_1' => 'telefone 1',
        'contact.phone_2' => 'telefone 2',
        'contact.phone_3' => 'telefone 3',
        'contact.whatsapp' => 'WhatsApp',
        'contact.address_full' => 'morada',
        'contact.hours' => 'horário (rodapé)',
        'contact.hours_lines' => 'horário (página de contacto)',
        'brand.name' => 'nome da marca',
        'brand.legal_name' => 'nome legal',
        'brand.tagline' => 'slogan',
        'social.instagram' => 'Instagram',
        'social.facebook' => 'Facebook',
        'social.linkedin' => 'LinkedIn',
        'whatsapp.number' => 'número de WhatsApp',
        'whatsapp.open_message' => 'mensagem inicial do WhatsApp',
        'process.case_local' => 'local do caso real',
        'qr_image' => 'imagem do QR Code',
        'agenda.horarios' => 'horários',
        'agenda.horarios_sabado' => 'horários de sábado',
        'agenda.dias_indisponiveis' => 'dias indisponíveis',
        'agenda.dias_indisponiveis.*' => 'dia da semana',
        'agenda.tipo_predefinido' => 'tipo de reunião pré-definido',
        'agenda.tipos.*.label' => 'rótulo do tipo de reunião',
        'agenda.tipos.*.nota' => 'nota do tipo de reunião',
    ];
}
