<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function cfg(): array { static $c = null; return $c ??= require __DIR__ . '/config.php'; }

function iniciar_sessao(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $c = cfg();
    session_name($c['session_name']);
    session_set_cookie_params([
        'lifetime' => 8 * 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    ]);
    session_start();
}

function e(?string $txt): string
{
    return htmlspecialchars((string) $txt, ENT_QUOTES, 'UTF-8');
}

function url(string $caminho = ''): string
{
    return rtrim(cfg()['base_url'], '/') . '/' . ltrim($caminho, '/');
}

function upload_url(?string $subpasta): string
{
    return rtrim(cfg()['uploads_url'], '/') . '/' . ltrim((string) $subpasta, '/');
}

function redirecionar(string $url): void
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }

    echo '<!DOCTYPE html><html lang="pt"><head><meta charset="UTF-8">';
    echo '<title>A redirecionar</title>';
    echo '<meta http-equiv="refresh" content="0;url=' . e($url) . '">';
    echo '<script>window.location.replace(' . json_encode($url) . ');</script>';
    echo '<style>body{font-family:system-ui,sans-serif;background:#061530;color:#fff;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;text-align:center}a{color:#B8E600}</style>';
    echo '</head><body><div><p>A redirecionar</p><p><a href="' . e($url) . '">Continuar</a></p></div></body></html>';
    exit;
}

function flash(string $chave, ?string $valor = null): ?string
{
    iniciar_sessao();
    if ($valor !== null) { $_SESSION['flash'][$chave] = $valor; return null; }
    $v = $_SESSION['flash'][$chave] ?? null;
    unset($_SESSION['flash'][$chave]);
    return $v;
}

function settings(): array
{
    static $cache = null;
    if ($cache !== null) return $cache;

    $cache = [];
    foreach (db()->query('SELECT chave, valor FROM settings') as $linha) {
        $cache[$linha['chave']] = $linha['valor'];
    }
    return $cache;
}

function setting(string $chave, string $padrao = ''): string
{
    return settings()[$chave] ?? $padrao;
}

function guardar_setting(string $chave, ?string $valor): void
{
    $stmt = db()->prepare('
        INSERT INTO settings (chave, valor) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE valor = VALUES(valor)
    ');
    $stmt->execute([$chave, $valor]);
}

function csrf_token(): string
{
    iniciar_sessao();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_valido(?string $t): bool
{
    iniciar_sessao();
    return !empty($_SESSION['csrf']) && !empty($t) && hash_equals($_SESSION['csrf'], $t);
}

function ip_cliente(): string { return $_SERVER['REMOTE_ADDR'] ?? '-'; }

function registar_log(?int $adminId, string $acao, ?string $detalhe = null): void
{
    try {
        db()->prepare('INSERT INTO logs (admin_id, acao, detalhe, ip) VALUES (?, ?, ?, ?)')
            ->execute([$adminId, $acao, $detalhe, ip_cliente()]);
    } catch (Throwable $e) { }
}

function post(string $chave, string $padrao = ''): string
{
    return isset($_POST[$chave]) ? trim((string) $_POST[$chave]) : $padrao;
}

function get(string $chave, string $padrao = ''): string
{
    return isset($_GET[$chave]) ? trim((string) $_GET[$chave]) : $padrao;
}

function guardar_imagem(string $campo, string $subpasta, ?string $actual = null): ?string
{
    if (empty($_FILES[$campo]['name'])) return $actual;

    $f = $_FILES[$campo];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Falha no upload.');
    if ($f['size'] > 5 * 1024 * 1024) throw new RuntimeException('Imagem maior que 5 MB.');

    $mime = mime_content_type($f['tmp_name']) ?: '';
    $extensoes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($extensoes[$mime])) throw new RuntimeException('Formato inválido. Use JPG, PNG ou WebP.');

    $info = @getimagesize($f['tmp_name']);
    if ($info === false) throw new RuntimeException('O ficheiro não é uma imagem válida.');

    [$largura, $altura] = $info;
    if ($largura < 100 || $altura < 100) throw new RuntimeException('Imagem demasiado pequena.');
    if ($largura > 6000 || $altura > 6000) throw new RuntimeException('Imagem demasiado grande.');

    $destinoDir = rtrim(cfg()['uploads_dir'], '/') . '/' . $subpasta;
    if (!is_dir($destinoDir)) {
        if (!mkdir($destinoDir, 0755, true)) throw new RuntimeException('Não foi possível criar a pasta.');
    }
    if (!is_writable($destinoDir)) throw new RuntimeException('Pasta sem permissão de escrita.');

    $nome = date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $extensoes[$mime];
    $destino = $destinoDir . '/' . $nome;

    if (!move_uploaded_file($f['tmp_name'], $destino)) {
        throw new RuntimeException('Não foi possível guardar a imagem.');
    }

    @chmod($destino, 0644);

    if ($actual) {
        $antigo = rtrim(cfg()['uploads_dir'], '/') . '/' . ltrim($actual, '/');
        if (is_file($antigo)) @unlink($antigo);
    }

    return $subpasta . '/' . $nome;
}

function apagar_imagem(?string $caminho): void
{
    if (!$caminho) return;
    $completo = rtrim(cfg()['uploads_dir'], '/') . '/' . ltrim($caminho, '/');
    if (is_file($completo)) @unlink($completo);
}

function imagem_url(?string $caminho, string $texto = ''): string
{
    $cfg = cfg();
    if ($caminho) {
        $completo = rtrim($cfg['uploads_dir'], '/') . '/' . ltrim($caminho, '/');
        if (is_file($completo)) {
            return rtrim($cfg['uploads_url'], '/') . '/' . ltrim($caminho, '/');
        }
    }

    $texto = $texto !== '' ? htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') : 'Imagem';
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600">'
         . '<rect width="800" height="600" fill="#0A1F44"/>'
         . '<line x1="0" y1="0" x2="800" y2="600" stroke="rgba(184,230,0,0.15)" stroke-width="1"/>'
         . '<line x1="800" y1="0" x2="0" y2="600" stroke="rgba(184,230,0,0.15)" stroke-width="1"/>'
         . '<circle cx="400" cy="300" r="60" fill="none" stroke="rgba(184,230,0,0.4)" stroke-width="1"/>'
         . '<text x="400" y="308" text-anchor="middle" fill="#B8E600" font-family="system-ui" font-size="14" letter-spacing="2">' . mb_strtoupper($texto) . '</text>'
         . '</svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function imagem_existe(?string $caminho): bool
{
    if (!$caminho) return false;
    $cfg = cfg();
    $completo = rtrim($cfg['uploads_dir'], '/') . '/' . ltrim($caminho, '/');
    return is_file($completo);
}