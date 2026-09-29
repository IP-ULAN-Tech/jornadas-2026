<?php
return [
    'db_host'      => '127.0.0.1',
    'db_name'      => 'jornadas_ips_2026',
    'db_user'      => 'root',
    'db_pass'      => '',
    'db_charset'   => 'utf8mb4',

    'session_name' => 'jornadas_ips_sid',

    // Caminho absoluto para a pasta de uploads
    'upload_dir'   => realpath(__DIR__ . '/../uploads'),

    // Limite de tamanho do ficheiro em bytes (10 MB)
    'upload_max'   => 10 * 1024 * 1024,

    // Nome do cookie de sessão
    'base_url'     => '/jornadas-ips-2026',
];