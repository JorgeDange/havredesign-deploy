# HAVREDESIGN — Site institucional

Site institucional da **HAVREDESIGN — Arquitetura e Construção, Lda.** (arquitetura, design de
interiores e consultoria técnica, Luanda/Talatona). Este repositório contém a aplicação **Laravel 12
+ Blade** que substituiu o site estático original, com formulários reais, agenda de reuniões,
autenticação, área do cliente e painel de administração.

- **Domínio canónico:** `https://www.havredesign.ao`
- **Painel:** `https://www.havredesign.ao/{ADMIN_PATH}` — no projeto `gestao-havre`
  (definido em `.env`; o valor por omissão do código é `admin`)
- **Administrador inicial:** `admin@havredesign.com` (password criada pelo *seeder* a partir de `ADMIN_PASSWORD`)

> Documentação completa do projeto (auditoria, especificação, plano e histórico de decisões) vive no
> repositório de trabalho do projeto: `laravel.md`, `backend.md`, `plan.md`, `memoria.md`,
> `resumo.md` e o guia de publicação `produ.md`.

---

## Índice

1. [Stack](#1-stack)
2. [Funcionalidades](#2-funcionalidades)
3. [Requisitos](#3-requisitos)
4. [Instalação local](#4-instalação-local)
5. [Configuração (.env)](#5-configuracao-env)
6. [Estrutura do projeto](#6-estrutura-do-projeto)
7. [Rotas principais](#7-rotas-principais)
8. [Base de dados](#8-base-de-dados)
9. [Testes e qualidade](#9-testes-e-qualidade)
10. [Publicação (hosting partilhado)](#10-publicação-hosting-partilhado)
11. [Segurança](#11-segurança)
12. [Operação e manutenção](#12-operação-e-manutenção)
13. [Pendências conhecidas](#13-pendências-conhecidas)
14. [Licença](#14-licença)

---

## 1. Stack

| Camada | Tecnologia |
|---|---|
| Framework | **Laravel 12** (PHP **8.2+**) |
| Vistas | **Blade** + Tailwind CSS (CDN, sem *build* de CSS) |
| Base de dados | **MySQL 8** (utf8mb4/InnoDB) — testes em **SQLite** em memória |
| Autenticação | Laravel Breeze (URIs em PT: `/entrar`, `/registar`, …) |
| Fila/e-mails | **Síncronos** (`QUEUE_CONNECTION=sync` em produção) — sem *worker*; nenhum e-mail é enfileirado |
| Sessões/cache | Em base de dados (compatível com hosting partilhado) |
| Imagens | `zoker/responsive-images` → `<picture>` + `srcset` + WebP |
| Testes | PHPUnit 11 (`php artisan test`) |
| *Style* | Laravel Pint |

## 2. Funcionalidades

**Site público**
- Páginas: Início, Sobre, Serviços, HAVRE Soluções (`/orcamentos`), Portefólio (+ detalhe),
  Processo (5 etapas), FAQ, Contacto, Solicitar Projeto, Agendar Conversa, Política de Privacidade,
  Termos de Uso e **`/networking`** (página permanente do QR Code, editável no painel).
- Formulários reais com CSRF, *honeypot*, validação em PT e *rate limit*:
  pedido de orçamento (com até 5 anexos), contacto e agendamento de reunião.
- E-mails transacionais (ack + notificação interna) — hoje escritos em `log` até haver SMTP.
- SEO: `title`/`description` por página, `/robots.txt` e `/sitemap.xml` gerados por rota,
  301 administráveis para as URLs antigas, imagens responsivas (`srcset` + WebP + `lazy`).

**Área do cliente (`/conta`)**
- Registo/login/recuperação de palavra-passe, pedidos e agendamentos do utilizador autenticado.

**Painel de administração (`/{ADMIN_PATH}`)**
- Painel com contagens; CRUD de serviços (com «inclui»), portefólio + galeria e soluções;
- **Pedidos** com estado, cliente, projeto e **anexos (Ver em modal / Baixar)**;
- Agendamentos (alteração de estado envia e-mail), mensagens de contacto, testemunhos;
- Definições (contactos, marca, redes, agenda, «Caso real» e **QR de `/networking`**);
- Utilizadores e papéis (`ADMIN`/`USER`).

## 3. Requisitos

- PHP **8.2+** com: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`,
  `fileinfo`, `curl`, `zip`, `zlib`, `dom`, **`gd`** (obrigatório para o WebP), `exif` (recomendado).
- MySQL 8+ (ou MariaDB 10.4+), `utf8mb4`.
- Composer 2 (só para instalação local/atualizações).
- HTTPS em produção (`SESSION_SECURE_COOKIE=true`).

## 4. Instalação local

```bash
composer install
cp .env.example .env
php artisan key:generate
# preencher DB_DATABASE / DB_USERNAME / DB_PASSWORD no .env
php artisan migrate --seed
php artisan storage:link
php artisan serve          # http://127.0.0.1:8000
```

Não há passo de *build* (Tailwind por CDN, assets estáticos em `public/`).
Não é preciso `npm install` para executar o site.

**Conta de administrador (criada pelo *seeder*):** `admin@havredesign.com` com a password de
`ADMIN_PASSWORD` no `.env` (se vazia, o *seeder* gera uma aleatória e mostra-a uma vez).

## 5. Configuração (.env)

| Variável | O que controla |
|---|---|
| `APP_URL` | Domínio canónico (sitemap, links absolutos, QR) — em produção `https://www.havredesign.ao` |
| `APP_KEY` | Gerar com `php artisan key:generate` (nunca reutilizar a de outro ambiente) |
| `APP_DEBUG` | `false` em produção |
| `DB_*` | Nome/utilizador/password da base de dados |
| `ADMIN_PATH` | Caminho do painel (no projeto `gestao-havre`; evite nomes previsíveis como `admin`) — após mudar: `optimize:clear` |
| `ADMIN_PASSWORD` | Password do admin usada pelo *seeder* (só 1.ª execução) |
| `SESSION_SECURE_COOKIE` | `true` em produção (cookie só por HTTPS) |
| `MAIL_MAILER` / `MAIL_HOST` / `MAIL_USERNAME` / `MAIL_PASSWORD` | SMTP — enquanto não existir, usar `MAIL_MAILER=log` |
| `QUEUE_CONNECTION` | Produção: `sync` (não há *worker*) — nenhum `Mailable` usa fila, nada fica pendente |
| `CACHE_STORE` / `SESSION_DRIVER` | `database` (compatível com hosting partilhado) |
| `RESPONSIVE_IMAGES_*` | Disco `web`, pasta `responsive-images` e `QUEUE=false` |

Template de produção: **`.env.production.example`** (copiar para `.env` na hospedagem e preencher).
Os ficheiros `.env*` preenchidos **nunca** são versionados (`.gitignore`).

## 6. Estrutura do projeto

```
app/Http/Controllers/        # públicos (Site*), Auth (Breeze) e Admin/*
app/Http/Requests/           # validação em PT dos formulários
app/Models/                  # 15 models Eloquent (UUID via trait HasUuid)
app/Mail/                    # e-mails de contacto/pedido/agendamento
config/admin.php             # ADMIN_PATH (caminho do painel)
database/migrations/         # schema + reconciliação idempotente
database/seeders/            # serviços, portefólio, soluções, settings, redirects, admin
public/                      # DOCUMENT ROOT: assets, imagens, QR, .htaccess
resources/views/             # layouts, partials, home, pages, project, booking, admin/*, emails
routes/web.php               # público + autenticação/área do cliente
routes/admin.php             # todas as rotas do painel (auth + role:ADMIN)
storage/app/private/         # anexos dos pedidos (disco privado, nunca público)
tests/                       # 19 classes PHPUnit (inclui as 6 do Breeze)
```

## 7. Rotas principais

- **Públicas:** `/`, `/sobre`, `/servicos`, `/orcamentos`, `/portfolio(/{slug})`, `/processo`,
  `/faq`, `/contacto`, `/solicitar-projeto`, `/agendar`, `/politica-de-privacidade`,
  `/termos-de-uso`, **`/networking`** (permanente — nunca redirecionar).
- **Autenticação:** `/entrar`, `/registar`, `/sair`, `/recuperar-palavra-passe`, `/conta`.
- **SEO:** `/robots.txt`, `/sitemap.xml`.
- **Painel** (prefixo `ADMIN_PATH`, exige `auth` + papel `ADMIN`):
  `/`, `servicos`, `portfolio`, `solucoes`, `pedidos` (+ `…/anexos/{anexo}` e `…/ver`),
  `agendamentos`, `mensagens`, `testemunhos`, `definicoes`, `utilizadores`.

## 8. Base de dados

23 tabelas (InnoDB/utf8mb4). Principais: `users` (papel `ADMIN`/`USER`), `services` +
`service_includes`, `portfolio_items` + `portfolio_gallery`, `solutions`, `project_requests` +
`attachments` (anexos no disco privado), `contact_messages`, `appointments`, `settings`
(pares chave/valor: contactos, marca, agenda, caso real, QR), `redirects` (301 administráveis).

Migrations **idempotentes** (seeder e *reconcile* seguros para repetir):

```bash
php artisan migrate --force     # a cada deploy
php artisan db:seed --force     # SÓ na 1.ª publicação (regride settings editados)
```

## 9. Testes e qualidade

```bash
php artisan test          # 127 testes / 551 asserções (SQLite em memória)
vendor/bin/pint           # formatação (Laravel Pint)
php artisan view:cache     # valida as vistas
```

Cobertura: páginas públicas, SEO/301, formulários e anexos, agenda, definições, imagens
responsivas, `/networking`, **anexos do painel (ver/baixar)**, controlo de acesso (guest 302 /
`USER` 403 / `ADMIN` 200), cabeçalhos de segurança e as classes do Breeze.
Os testes correm sempre em SQLite mesmo com `config:cache` ativo (`APP_CONFIG_CACHE` próprio no
`phpunit.xml`).

## 10. Publicação (hosting partilhado)

Requisitos: PHP 8.2+ com as extensões do §3, MySQL 8, HTTPS e **document root = `public/`**.

```bash
cp .env.production.example .env      # e preencher DB_*/APP_URL/ADMIN_PASSWORD/SMTP
php artisan key:generate
composer install --no-dev --optimize-autoloader   # ou subir vendor/
chmod -R 775 storage bootstrap/cache
php artisan migrate --force
php artisan db:seed --force          # SÓ na 1.ª publicação
php artisan storage:link             # ou copiar storage/app/public para public_html/storage
php artisan optimize                 # config + rotas + vistas
```

**Verificações rápidas:** `/` 200 · `/networking` 200 **sem** `Location` · `/sitemap.xml` contém
`/networking` · `/{ADMIN_PATH}` faz *login*.

**Rotina de atualização:** `php artisan down` → subir ficheiros → `composer install --no-dev` (se
mudou `composer.lock`) → `migrate --force` → `optimize:clear && optimize` → `php artisan up`.
Fazer *backup* da BD antes de qualquer *migrate* (`mysqldump`).

O guia detalhado (layout na hospedagem, permissões, opção `public_html`, *rollback*) está em
`produ.md` no repositório do projeto.

### Servidor sem terminal

Se a hospedagem não disponibilizar consola (SSH/terminal), os comandos acima executam-se por um
**script temporário** acedido no navegador — corre dentro do próprio PHP (não usa `shell_exec`).

Criar `public/deploy.php` pelo *File Manager* e abrir uma vez por comando:

> **Layout com `public_html` fixo** (docroot do domínio = `public_html`, código fora — ver
> `produ.md` §4 *Opção B*): o `deploy.php` fica em `public_html/` e os dois `require` passam a
> `require __DIR__ . '/../app/vendor/autoload.php';` e
> `$app = require __DIR__ . '/../app/bootstrap/app.php';` (pasta `app/` = código, fora do alcance
> do navegador), acrescentados de `$app->usePublicPath(__DIR__);` — senão `public_path()` aponta
> para `~/app/public/` e as variantes WebP e os *uploads* do QR são gravados fora do *web root*.
> O mesmo ajuste — `__DIR__.'/../` → `__DIR__.'/../app/` nas **3** ocorrências + `usePublicPath` —
> tem de ser feito no `public_html/index.php`.

```php
<?php
// TEMPORÁRIO — apagar imediatamente após o deploy
if (!isset($_GET['chave']) || $_GET['chave'] !== 'TROQUE-ESTE-VALOR') {
    http_response_code(404);
    exit;
}
$permitidos = ['key:generate', 'migrate --force', 'db:seed --force', 'optimize', 'optimize:clear', 'storage:link'];
$cmd = $_GET['cmd'] ?? '';
if (!in_array($cmd, $permitidos, true)) {
    exit('comando nao permitido');
}
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo '<pre>' . htmlspecialchars(Illuminate\Support\Facades\Artisan::call($cmd)) . '</pre>';
echo 'OK: ' . $cmd;
```

Sequência (ex.: `https://www.havredesign.ao/deploy.php?chave=…&cmd=key:generate`):

1. Subir ficheiros e criar `.env` (de `.env.production.example`) pelo *File Manager*.
2. Permissões `775` em `storage/` e `bootstrap/cache/`.
3. `cmd=key:generate` → `cmd=migrate --force` → (`cmd=db:seed --force` **só** na 1.ª publicação).
4. `cmd=storage:link` → `cmd=optimize`.
5. **Apagar `public/deploy.php`** e verificar `/`, `/networking`, `/{ADMIN_PATH}`.

Notas:
- O ficheiro dá acesso a operações administrativas a quem souber a URL — usar `chave` longa e
  única por ambiente e **nunca deixá-lo no ar**; `.env` tem de ser gravável pelo PHP.
- Se `storage:link` falhar (symlink bloqueado pela hospedagem), copiar `storage/app/public` para
  `public_html/storage` pelo *File Manager* (o README do Laravel documenta esta alternativa).
- Alternativa sem script: um *cron job* único no painel de controlo
  (`php -f /home/USER/public_html/artisan -- migrate --force`), removido a seguir.

## 11. Segurança

- Rotas do painel com `auth` + `role:ADMIN`; caminho do painel configurável (`ADMIN_PATH`) fora de
  *wordlists* — *security through obscurity* que **complementa**, não substitui, autorização.
- Formulários: CSRF, *honeypot*, *throttle* (inclusive no login), validação server-side.
- Anexos dos pedidos no **disco privado** (`storage/app/private`), servidos apenas pelas rotas do
  painel com sessão de administrador (404 se o anexo não pertencer ao pedido).
- `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, cabeçalhos de segurança globais
  (nosniff, SAMEORIGIN, Referrer-Policy, Permissions-Policy, COOP, HSTS em produção).
- `.env` fora do Git (`chmod 600` em produção); *seeds* nunca imprimem segredos.
- E-mails sem *queue* (nada fica pendente sem *worker*).

## 12. Operação e manutenção

- **Logs:** `storage/logs/laravel.log` (`LOG_LEVEL=warning`).
- **Caches:** limpar com `optimize:clear` sempre que o `.env` mudar; recriar com `optimize`.
- **Imagens WebP:** variantes geradas sob procura em `public/responsive-images/` (regeneráveis;
  fora do Git). Discos graváveis: `storage/`, `bootstrap/cache/`, `public/images/`,
  `public/responsive-images/`.
- **QR de `/networking`:** substituível em *Definições → Networking* (grava sempre em
  `public/images/qrcode-networking.png` — o URL nunca muda).
- **Backups:** BD (`mysqldump`) + `public/images/` + `storage/app/private/` (anexos de clientes).

## 13. Pendências conhecidas

- **SMTP real** por configurar (hoje `MAIL_MAILER=log`).
- Compressão dos originais de imagem (o WebP responsivo já é gerado).
- Submissão do `sitemap.xml` no Google Search Console (pós-publicação).
- Validação jurídica dos textos de Política de Privacidade/Termos e aprovação dos testemunhos.
- Confirmação do conteúdo do QR (`https://www.havredesign.ao/networking`) e *scan* em telemóvel.

## 14. Licença

Projeto **proprietário** — desenvolvido para a HAVREDESIGN; todos os direitos reservados ao
cliente. Construído sobre [Laravel](https://laravel.com) (licença MIT) e
[Breeze](https://laravel.com/docs/blade) — ver `composer.json`.
