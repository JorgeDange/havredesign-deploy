<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\ProjectRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectRequestController extends Controller
{
    /**
     * Estados possíveis de um pedido de orçamento (código => rótulo PT).
     *
     * @var array<string, string>
     */
    public const ESTADOS = [
        'NEW' => 'Novo',
        'IN_REVIEW' => 'Em análise',
        'APPROVED' => 'Aprovado',
        'IN_PROGRESS' => 'Em curso',
        'COMPLETED' => 'Concluído',
        'REJECTED' => 'Recusado',
    ];

    /**
     * GET /<ADMIN_PATH>/pedidos — lista com filtro ?status= e contagens por aba.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        if (! array_key_exists($status, self::ESTADOS)) {
            $status = '';
        }

        $query = ProjectRequest::query();
        if ($status !== '') {
            $query->where('status', $status);
        }

        $contagens = ['total' => ProjectRequest::count()];
        foreach (self::ESTADOS as $codigo => $rotulo) {
            $contagens[$codigo] = ProjectRequest::where('status', $codigo)->count();
        }

        return view('admin.pedidos.index', [
            'pedidos' => $query->withCount('attachments')->latest()->paginate(20)->withQueryString(),
            'status' => $status,
            'estados' => self::ESTADOS,
            'contagens' => $contagens,
        ]);
    }

    /**
     * GET /<ADMIN_PATH>/pedidos/{projectRequest} — detalhe + anexos (ficheiros
     * privados, servidos pelas rotas download/preview abaixo).
     */
    public function show(ProjectRequest $projectRequest): View
    {
        return view('admin.pedidos.show', [
            'pedido' => $projectRequest,
            'estados' => self::ESTADOS,
            'anejos' => Attachment::where('request_id', $projectRequest->getKey())->get(),
        ]);
    }

    /**
     * GET /<ADMIN_PATH>/pedidos/{projectRequest}/anexos/{attachment} — download.
     */
    public function download(ProjectRequest $projectRequest, Attachment $attachment): StreamedResponse
    {
        return Storage::disk('local')->download(
            $this->caminhoDoAnexo($projectRequest, $attachment),
            $this->nomeSeguro($attachment),
            ['Content-Type' => $attachment->mime_type ?: 'application/octet-stream']
        );
    }

    /**
     * GET /<ADMIN_PATH>/pedidos/{projectRequest}/anexos/{attachment}/ver —
     * visualização em separador novo (PDF/imagem); os restantes caem em download.
     */
    public function preview(ProjectRequest $projectRequest, Attachment $attachment): StreamedResponse
    {
        if (! $attachment->visualizavel()) {
            return $this->download($projectRequest, $attachment);
        }

        return Storage::disk('local')->response(
            $this->caminhoDoAnexo($projectRequest, $attachment),
            $this->nomeSeguro($attachment),
            ['Content-Type' => $attachment->mime_type ?: 'application/octet-stream'],
            'inline'
        );
    }

    /**
     * Caminho no disco privado, validado: o anexo tem de pertencer ao pedido
     * e o ficheiro tem de existir (404 nos dois casos).
     */
    private function caminhoDoAnexo(ProjectRequest $projectRequest, Attachment $attachment): string
    {
        abort_unless($attachment->request_id === $projectRequest->getKey(), 404);
        abort_unless(Storage::disk('local')->exists($attachment->path), 404);

        return $attachment->path;
    }

    /** Nome de descarga sem caracteres que rebentam o cabeçalho Content-Disposition. */
    private function nomeSeguro(Attachment $attachment): string
    {
        $nome = trim(str_replace(["\r", "\n", '%', '/', '\\'], '-', (string) $attachment->original_name));

        return $nome !== '' ? $nome : 'anexo';
    }

    /**
     * PUT /<ADMIN_PATH>/pedidos/{projectRequest} — altera o estado do pedido.
     */
    public function update(Request $request, ProjectRequest $projectRequest): RedirectResponse
    {
        $dados = $request->validate(
            [
                'status' => ['required', 'string', Rule::in(array_keys(self::ESTADOS))],
            ],
            [],
            ['status' => 'estado']
        );

        $projectRequest->update(['status' => $dados['status']]);

        return redirect()
            ->route('pedidos.show', $projectRequest)
            ->with('ok', 'Estado do pedido atualizado.');
    }
}
