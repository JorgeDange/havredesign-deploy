<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    /**
     * Estados possíveis de uma mensagem de contacto (código => rótulo PT).
     *
     * @var array<string, string>
     */
    public const ESTADOS = [
        'new' => 'Nova',
        'replied' => 'Respondida',
        'closed' => 'Fechada',
    ];

    /**
     * GET /<ADMIN_PATH>/mensagens — lista com filtro ?status= e contagens por aba.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        if (! array_key_exists($status, self::ESTADOS)) {
            $status = '';
        }

        $query = ContactMessage::query();
        if ($status !== '') {
            $query->where('status', $status);
        }

        $contagens = ['total' => ContactMessage::count()];
        foreach (self::ESTADOS as $codigo => $rotulo) {
            $contagens[$codigo] = ContactMessage::where('status', $codigo)->count();
        }

        return view('admin.mensagens.index', [
            'mensagens' => $query->latest()->paginate(20)->withQueryString(),
            'status' => $status,
            'estados' => self::ESTADOS,
            'contagens' => $contagens,
        ]);
    }

    /**
     * GET /<ADMIN_PATH>/mensagens/{contactMessage} — mensagem integral.
     */
    public function show(ContactMessage $contactMessage): View
    {
        return view('admin.mensagens.show', [
            'mensagem' => $contactMessage,
            'estados' => self::ESTADOS,
        ]);
    }

    /**
     * PUT /<ADMIN_PATH>/mensagens/{contactMessage} — altera o estado (new/replied/closed).
     */
    public function update(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $dados = $request->validate(
            [
                'status' => ['required', 'string', Rule::in(array_keys(self::ESTADOS))],
            ],
            [],
            ['status' => 'estado']
        );

        $contactMessage->update(['status' => $dados['status']]);

        return redirect()
            ->route('mensagens.show', $contactMessage)
            ->with('ok', 'Estado da mensagem atualizado.');
    }

    /**
     * DELETE /<ADMIN_PATH>/mensagens/{contactMessage} — apaga a mensagem.
     */
    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()
            ->route('mensagens.index')
            ->with('ok', 'Mensagem apagada.');
    }
}
