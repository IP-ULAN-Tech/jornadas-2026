<?php
require_once __DIR__ . '/../../inc/auth.php';
exigir_login();

$admin = admin_actual();
$tituloPagina = $tituloPagina ?? 'Painel';

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($tituloPagina) ?> · Painel Jornadas IPS 2026</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
<meta name="csrf" content="<?= e(csrf_token()) ?>">
</head>
<body>
<div class="app">
  <?php require __DIR__ . '/sidebar.php'; ?>
  <div class="conteudo">
    <header class="barra-topo">
      <h1 class="barra-topo-titulo"><?= e($tituloPagina) ?></h1>
      <div class="barra-topo-dir">
        <span class="barra-topo-admin"><?= e($admin['nome']) ?></span>
        <a href="<?= e(url('index.php')) ?>" target="_blank" class="botao-ligacao botao-mini">Ver site</a>
        <a href="logout.php" class="barra-topo-sair">Sair</a>
      </div>
    </header>
    <div class="conteudo-corpo">