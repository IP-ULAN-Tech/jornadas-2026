<?php
declare(strict_types=1);
require_once __DIR__ . '/env.php';
env_carregar(__DIR__ . '/../.env');
date_default_timezone_set(env('APP_TIMEZONE', 'Africa/Luanda'));

$debug = env_bool('APP_DEBUG', false);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
error_reporting($debug ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$baseUrl = rtrim(env('BASE_URL', ''), '/');

return [
    'app_env' => env('APP_ENV', 'production'),
    'app_debug' => $debug,

    'db_host' => env('DB_HOST', '127.0.0.1'),
    'db_name' => env_required('DB_NAME'),
    'db_user' => env_required('DB_USER'),
    'db_pass' => env_required('DB_PASS'),
    'db_charset' => env('DB_CHARSET', 'utf8mb4'),
    'db_port' => env_int('DB_PORT', 3306),


    'base_url' => $baseUrl,
    'uploads_dir' => env('UPLOADS_DIR') ?: (realpath(__DIR__ . '/../uploads') ?: dirname(__DIR__) . '/uploads'),
    'uploads_url' => env('UPLOADS_URL', '/uploads'),
    'uploads_max' => (int) env('UPLOADS_MAX', (string) (10 * 1024 * 1024)),
    'session_name' => env('SESSION_NAME', 'jornadas_ips_sid'),

    'timezone' => env('APP_TIMEZONE', 'Africa/Luanda'),

    'smtp' => [
        'ativo' => env_bool('SMTP_ATIVO', false),
        'host' => env('SMTP_HOST', ''),
        'port' => env_int('SMTP_PORT', 587),
        'seguranca' => env('SMTP_SEGURANCA', 'tls'),
        'utilizador' => env('SMTP_USER', ''),
        'password' => env('SMTP_PASS', ''),
        'remetente' => env('SMTP_REMETENTE', ''),
        'nome' => env('SMTP_NOME', 'Jornadas IPS 2026'),
        'reply_to' => env('SMTP_REPLY_TO', ''),
    ],
];
