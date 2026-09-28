<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Localizar a aplicacao automaticamente, sem depender do nome da pasta:
//  1) qualquer pasta irmã (ou a propria home) com vendor/ + bootstrap/app.php  -> app completa
//  2) qualquer pasta irmã com artisan                                          -> codigo sem vendor ainda
//  3) fallback classico: ../  (docroot = public/ dentro da app - Opção A)
// Se nada for encontrado, escreve no error_log a estrutura real da pasta para diagnosticar.
// candidatos: a home, depois a pasta "app" (a recomendada) e por fim as restantes
$candidatos = array_merge(
    [__DIR__ . '/..', __DIR__ . '/../app'],
    glob(__DIR__ . '/../{*,.[!.]*}', GLOB_BRACE) ?: []
);
$raiz = null;
foreach ($candidatos as $d) {
    if (is_dir($d) && is_file($d . '/vendor/autoload.php') && is_file($d . '/bootstrap/app.php')) {
        $raiz = rtrim($d, '/\\') . '/';
        break;
    }
}
if ($raiz === null) {
    foreach ($candidatos as $d) {
        if (is_dir($d) && is_file($d . '/artisan')) {
            $raiz = rtrim($d, '/\\') . '/';
            break;
        }
    }
}
if ($raiz === null) {
    $irmas = array_map('basename', glob(__DIR__ . '/../{*,.[!.]*}', GLOB_BRACE) ?: []);
    $publicas = array_map('basename', glob(__DIR__ . '/{*,.[!.]*}', GLOB_BRACE) ?: []);
    error_log('HAVRE nao encontrei a aplicacao. Pastas em ' . dirname(__DIR__) . ': [' . implode(', ', $irmas) . '] | em public_html: [' . implode(', ', $publicas) . ']');
    $raiz = __DIR__ . '/../';
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $raiz . 'storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $raiz . 'vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $raiz . 'bootstrap/app.php';

// public_path() tem de ser sempre a pasta do index.php (necessario quando o
// docroot esta fixo e a app vive noutra pasta; inofensivo na Opção A):
// variantes WebP e uploads caem sempre no web root.
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
