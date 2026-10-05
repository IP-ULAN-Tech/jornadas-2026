<?php
require_once dirname(__DIR__) . '/inc/env.php';

// Sem segredos neste ficheiro: as credenciais vêm de variáveis de ambiente / .env
return [
    'db_host'    => env('DB_HOST', 'localhost'),
    'db_name'    => env_required('DB_NAME'),
    'db_user'    => env_required('DB_USER'),
    'db_pass'    => env_required('DB_PASS'),
    'db_charset' => env('DB_CHARSET', 'utf8mb4'),

    'session_name' => env('SESSION_NAME', 'jornadas_ips_sid'),

    // Caminho absoluto para a pasta de uploads
    'upload_dir' => env('UPLOADS_DIR') ?: (realpath(__DIR__ . '/../uploads') ?: dirname(__DIR__) . '/uploads'),

    // Limite de tamanho do ficheiro em bytes (10 MB)
    'upload_max' => (int)env('UPLOADS_MAX', (string)(10 * 1024 * 1024)),

    'base_url' => env('API_BASE_URL', '/jornadas-ips-2026'),
];
