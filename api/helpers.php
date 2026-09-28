<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/* 
   SESSÃO
    */

function iniciar_sessao(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;

    $cfg = require __DIR__ . '/config.php';

    session_name($cfg['session_name']);
    session_set_cookie_params([
        'lifetime' => 8 * 60 * 60,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    ]);
    session_start();
}

/* 
   RESPOSTAS JSON
    */

function responder_json($dados, int $codigo = 200): void
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

function responder_erro(string $mensagem, int $codigo = 400): void
{
    responder_json(['erro' => $mensagem], $codigo);
}

/* 
   AUTENTICAÇÃO
    */

function admin_autenticado(): bool
{
    iniciar_sessao();
    return !empty($_SESSION['admin_id']);
}

function exigir_admin(): int
{
    if (!admin_autenticado()) {
        responder_erro('Não autenticado.', 401);
    }
    return (int) $_SESSION['admin_id'];
}

/* 
   CSRF
    */

function csrf_token(): string
{
    iniciar_sessao();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valido(?string $token): bool
{
    iniciar_sessao();
    return !empty($_SESSION['csrf'])
        && !empty($token)
        && hash_equals($_SESSION['csrf'], $token);
}

/* 
   ENTRADA
    */

function corpo_json(): array
{
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $dados = json_decode($raw, true);
    return is_array($dados) ? $dados : [];
}

function campo(string $chave, array $fonte, string $padrao = ''): string
{
    return isset($fonte[$chave]) ? trim((string) $fonte[$chave]) : $padrao;
}

function ip_cliente(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
}

/* 
   LOGS
    */

function registar_log(?int $adminId, string $acao, ?string $detalhe = null): void
{
    try {
        $stmt = db()->prepare(
            'INSERT INTO logs (admin_id, acao, detalhe, ip) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$adminId, $acao, $detalhe, ip_cliente()]);
    } catch (Throwable $e) {
        // falha silenciosa — log não é crítico
    }
}

/* 
   RATE LIMIT SIMPLES (por sessão e por IP)
    */

function limitar(string $chave, int $max, int $janelaSegundos): bool
{
    iniciar_sessao();
    $agora = time();
    $registo = $_SESSION['rate'][$chave] ?? [];

    // descartar entradas antigas
    $registo = array_values(array_filter($registo, fn($t) => $t > $agora - $janelaSegundos));

    if (count($registo) >= $max) {
        $_SESSION['rate'][$chave] = $registo;
        return false;
    }

    $registo[] = $agora;
    $_SESSION['rate'][$chave] = $registo;
    return true;
}