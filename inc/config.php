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
    'app_env'   => env('APP_ENV', 'production'),
    'app_debug' => $debug,

    'db_host'    => env('DB_HOST', '127.0.0.1'),
    'db_port'    => env_int('DB_PORT', 3306),
    'db_name'    => env('DB_NAME', 'jornadas_ips_2026'),
    'db_user'    => env('DB_USER', 'root'),
    'db_pass'    => env('DB_PASS', ''),
    'db_charset' => 'utf8mb4',

    'base_url'    => $baseUrl,
    'uploads_dir' => realpath(__DIR__ . '/../uploads'),
    'uploads_url' => rtrim(env('UPLOADS_URL', $baseUrl . '/uploads'), '/'),
    'uploads_max' => 10 * 1024 * 1024,
    'session_name' => 'jornadas_ips_sid',

    'timezone' => env('APP_TIMEZONE', 'Africa/Luanda'),

    'smtp' => [
        'ativo'      => env_bool('SMTP_ATIVO', false),
        'host'       => env('SMTP_HOST', ''),
        'port'       => env_int('SMTP_PORT', 587),
        'seguranca'  => env('SMTP_SEGURANCA', 'tls'),
        'utilizador' => env('SMTP_USER', ''),
        'password'   => env('SMTP_PASS', ''),
        'remetente'  => env('SMTP_REMETENTE', ''),
        'nome'       => env('SMTP_NOME', 'Jornadas IPS 2026'),
        'reply_to'   => env('SMTP_REPLY_TO', ''),
    ],
];