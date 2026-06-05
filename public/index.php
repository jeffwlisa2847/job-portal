<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

define('ROOT_PATH', dirname(__DIR__));

if (!file_exists(ROOT_PATH . '/core/Router.php')) {
    die('<div style="font-family:sans-serif;max-width:640px;margin:60px auto;padding:30px;
         background:#fff3cd;border:2px solid #ffc107;border-radius:12px;">
    <h2 style="color:#856404;">⚠️ Setup Error — Wrong folder location</h2>
    <p>Cannot find core files. Your project must be at:</p>
    <pre style="background:#fff;padding:12px;border-radius:6px;font-size:13px;">
C:\\xampp\\htdocs\\job-portal\\core\\Router.php
C:\\xampp\\htdocs\\job-portal\\public\\index.php</pre>
    <p>Current path: <code>' . ROOT_PATH . '</code></p>
    <p><strong>Fix:</strong> Make sure the folder is named <strong>job-portal</strong>
    inside <strong>C:\\xampp\\htdocs\\</strong></p></div>');
}

$appConfig = require ROOT_PATH . '/config/app.php';
define('BASE_URL',  rtrim($appConfig['base_url'], '/'));
define('APP_NAME',  $appConfig['name']);
define('APP_ENV',   $appConfig['env']);
define('APP_DEBUG', $appConfig['debug']);

date_default_timezone_set($appConfig['timezone'] ?? 'Africa/Accra');

spl_autoload_register(function (string $class): void {
    $paths = [
        ROOT_PATH . '/core/'           . $class . '.php',
        ROOT_PATH . '/app/middleware/' . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) { require_once $file; return; }
    }
});

if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

require_once ROOT_PATH . '/app/helpers/functions.php';

Session::start();

set_exception_handler(function (Throwable $e): void {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><title>Error</title>
    <style>body{font-family:monospace;background:#1e1e1e;color:#d4d4d4;padding:2rem;}
    h2{color:#f48771;}pre{background:#252526;padding:1rem;border-radius:6px;
    overflow:auto;font-size:13px;}.tip{background:#1a2a3a;border:1px solid #2563eb;
    border-radius:8px;padding:1rem;margin-top:1rem;font-family:sans-serif;
    font-size:13px;color:#93c5fd;}</style></head><body>';
    echo '<h2>💥 ' . get_class($e) . '</h2>';
    echo '<p style="color:#fbbf24;margin-bottom:.5rem;">' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p style="color:#9cdcfe;">File: ' . htmlspecialchars($e->getFile()) . '</p>';
    echo '<p style="color:#86efac;">Line: ' . $e->getLine() . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '<div class="tip"><strong>Common fixes:</strong><br>';
    echo '• DB error → MySQL running? Imported schema.sql?<br>';
    echo '• Class not found → File in wrong folder?<br>';
    echo '• View not found → Check view path in controller</div>';
    echo '</body></html>';
    exit;
});

$router = new Router();
require ROOT_PATH . '/routes.php';
$router->dispatch();
