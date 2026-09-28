<?php

/*
|--------------------------------------------------------------------------
| Caminho do painel de administração
|--------------------------------------------------------------------------
| Editável no `.env` (`ADMIN_PATH`) para não publicar a URL de backoffice.
|
| Isto é **obscurecimento**, não autorização: continua a valer `role:ADMIN`,
| `auth` e o rate limit do login. Serve para não andar a dar de carregar em
| `/admin` à procura de uma porta aberta.
|
| Depois de alterar o valor: `php artisan optimize:clear` (as rotas ficam
| guardadas em cache com o caminho antigo).
|
| Valor inválido (vazio, com barra, caracteres estranhos, reservado para uma
| página pública) volta a `admin` em vez de partir as rotas.
*/

$caminho = trim((string) env('ADMIN_PATH', 'admin'), '/');

$reservados = [
    'up', 'conta', 'entrar', 'registar', 'sair', 'portfolio', 'servicos',
    'orcamentos', 'sobre', 'processo', 'faq', 'contacto', 'agendar',
    'solicitar-projeto', 'politica-de-privacidade', 'termos-de-uso',
    'robots.txt', 'sitemap.xml',
];

return [
    'path' => preg_match('/^[a-z0-9][a-z0-9\-_]{2,31}$/i', $caminho) === 1
        && ! in_array($caminho, $reservados, true)
            ? $caminho
            : 'admin',
];
