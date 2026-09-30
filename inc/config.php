<?php
require_once __DIR__ . '/env.php';

// Sem segredos neste ficheiro: as credenciais vêm de variáveis de ambiente / .env
return [
    'db_host'    => env('DB_HOST', 'localhost'),
    'db_name'    => env_required('DB_NAME'),
    'db_user'    => env_required('DB_USER'),
    'db_pass'    => env_required('DB_PASS'),
    'db_charset' => env('DB_CHARSET', 'utf8mb4'),

    'base_url'    => env('BASE_URL', '/'),
    'uploads_dir' => env('UPLOADS_DIR') ?: (realpath(__DIR__ . '/../uploads') ?: dirname(__DIR__) . '/uploads'),
    'uploads_url' => env('UPLOADS_URL', '/uploads'),
    'uploads_max' => (int)env('UPLOADS_MAX', (string)(10 * 1024 * 1024)),
    'session_name' => env('SESSION_NAME', 'jornadas_ips_sid'),

    'timezone' => env('TIMEZONE', 'Africa/Luanda'),
];
