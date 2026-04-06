<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Strip subdirectory prefix from REQUEST_URI and SCRIPT_NAME so Laravel's
// compiled route matcher resolves paths correctly when deployed under a
// subdirectory (e.g. /school-system-demo). Without this, the trailing-slash
// trimming in CompiledRouteCollection breaks pathInfo detection for the root route.
$subdir = '/school-system-demo';
if (isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], $subdir)) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($subdir)) ?: '/';
}
if (isset($_SERVER['SCRIPT_NAME']) && str_starts_with($_SERVER['SCRIPT_NAME'], $subdir)) {
    $_SERVER['SCRIPT_NAME'] = substr($_SERVER['SCRIPT_NAME'], strlen($subdir));
}
if (isset($_SERVER['PHP_SELF']) && str_starts_with($_SERVER['PHP_SELF'], $subdir)) {
    $_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], strlen($subdir));
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
