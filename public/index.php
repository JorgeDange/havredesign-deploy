<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Layout hibrido:
//  - Opção A: document root = public/ dentro da aplicacao (../app/artisan NAO existe, ../artisan existe)
//  - Opção B: document root fixo (public_html/) com a aplicacao em ../app/ (la existe artisan)
// Assim um "git pull" nunca parte o index.php nem exige edicoes manuais pos-deploy.
$raiz = is_file(__DIR__ . '/../app/artisan') ? __DIR__ . '/../app/' : __DIR__ . '/../';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $raiz . 'storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $raiz . 'vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $raiz . 'bootstrap/app.php';

// Garante que public_path() e sempre a pasta do index.php (necessario na Opção B,
// inofensivo na Opção A): variantes WebP e uploads caem sempre no web root.
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
