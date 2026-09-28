<?php
require_once __DIR__ . '/../inc/auth.php';

iniciar_sessao();

if (!empty($_SESSION['admin_id'])) {
    registar_log((int) $_SESSION['admin_id'], 'logout');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

redirecionar('login.php');