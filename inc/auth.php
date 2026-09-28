<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

define('NIVEL_SUPER', 1);
define('NIVEL_EDITOR', 2);
define('NIVEL_AVALIADOR', 3);

function admin_autenticado(): bool
{
    iniciar_sessao();
    return !empty($_SESSION['admin_id']);
}

function admin_actual(): ?array
{
    if (!admin_autenticado()) return null;

    static $cache = null;
    if ($cache !== null) return $cache;

    $stmt = db()->prepare('
        SELECT id, nome, email, nivel, ativo
        FROM admins
        WHERE id = ? AND ativo = 1
    ');
    $stmt->execute([(int) $_SESSION['admin_id']]);
    $admin = $stmt->fetch() ?: null;

    return $cache = $admin;
}

function exigir_login(): void
{
    if (!admin_autenticado()) {
        redirecionar(url('admin/login.php'));
    }

    if (!admin_actual()) {
        $_SESSION = [];
        session_destroy();
        redirecionar(url('admin/login.php'));
    }
}

function admin_nivel(): int
{
    $a = admin_actual();
    return $a ? (int) $a['nivel'] : 99;
}

function admin_eh_super(): bool
{
    return admin_nivel() === NIVEL_SUPER;
}

function admin_eh_editor(): bool
{
    return admin_nivel() === NIVEL_EDITOR;
}

function admin_eh_avaliador(): bool
{
    return admin_nivel() === NIVEL_AVALIADOR;
}

function admin_pode_editar_conteudo(): bool
{
    return in_array(admin_nivel(), [NIVEL_SUPER, NIVEL_EDITOR], true);
}

function admin_pode_apagar(): bool
{
    return admin_nivel() === NIVEL_SUPER;
}

function admin_pode_gerir_utilizadores(): bool
{
    return admin_nivel() === NIVEL_SUPER;
}

function admin_pode_ver_logs(): bool
{
    return admin_nivel() === NIVEL_SUPER;
}

function admin_pode_gerir_submissoes(): bool
{
    return true;
}

function admin_nome_nivel(int $nivel): string
{
    return [
        NIVEL_SUPER     => 'Super Administrador',
        NIVEL_EDITOR    => 'Editor',
        NIVEL_AVALIADOR => 'Avaliador',
    ][$nivel] ?? 'Desconhecido';
}

function admin_abrev_nivel(int $nivel): string
{
    return [
        NIVEL_SUPER     => 'Super Admin',
        NIVEL_EDITOR    => 'Editor',
        NIVEL_AVALIADOR => 'Avaliador',
    ][$nivel] ?? '—';
}

function exigir_nivel(array $niveisPermitidos): void
{
    exigir_login();
    if (!in_array(admin_nivel(), $niveisPermitidos, true)) {
        flash('erro', 'Não tem permissão para aceder a esta área.');
        redirecionar(url('admin/dashboard.php'));
    }
}

function exigir_super(): void
{
    exigir_nivel([NIVEL_SUPER]);
}

function exigir_editor_ou_super(): void
{
    exigir_nivel([NIVEL_SUPER, NIVEL_EDITOR]);
}