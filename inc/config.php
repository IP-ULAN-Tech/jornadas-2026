<?php
$documentRoot = isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== ''
    ? realpath($_SERVER['DOCUMENT_ROOT'])
    : false;
$applicationRoot = realpath(__DIR__ . '/../') ?: dirname(__DIR__);
$applicationUrl = '/';

if ($documentRoot !== false) {
    $documentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');
    $applicationRoot = rtrim(str_replace('\\', '/', $applicationRoot), '/');

    if (strcasecmp($applicationRoot, $documentRoot) === 0) {
        $applicationUrl = '/';
    } elseif (stripos($applicationRoot, $documentRoot . '/') === 0) {
        $applicationUrl = '/' . trim(substr($applicationRoot, strlen($documentRoot)), '/') . '/';
    }
}

return [
    'db_host'    => '127.0.0.1',
    'db_name'    => 'jornadas',
    'db_user'    => 'jornadas',
    'db_pass'    => '12345678',
    'db_charset' => 'utf8mb4',

    'base_url'    => $applicationUrl,
    'uploads_dir' => realpath(__DIR__ . '/../uploads'),
    'uploads_url' => rtrim($applicationUrl, '/') . '/uploads',
    'uploads_max' => 10 * 1024 * 1024,
    'session_name' => 'jornadas_ips_sid',

    'timezone' => 'Africa/Luanda',
];