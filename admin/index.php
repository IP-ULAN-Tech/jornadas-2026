<?php
require_once __DIR__ . '/../inc/auth.php';
if (admin_autenticado()) redirecionar('dashboard.php');
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jornadas Técnico-Científicas · IPS 2026</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="pagina-splash">

<div class="splash-marca">
  <p class="splash-inst">Instituto Politécnico de Saurimo</p>
  <h1 class="splash-titulo">Jornadas<br>Técnico-Científicas</h1>
  <p class="splash-sub">Edição 2026 · Painel de Gestão</p>
</div>

<a href="login.php" class="splash-acao">Entrar no painel</a>

<p class="splash-rodape">Universidade Lueji A'Nkonde · 2026</p>

</body>
</html>
