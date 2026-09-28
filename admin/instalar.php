<?php
require_once __DIR__ . '/../inc/helpers.php';

$existe = (int) db()->query('SELECT COUNT(*) FROM admins')->fetchColumn();
if ($existe > 0) {
    exit('Já existe pelo menos um administrador. Apague este ficheiro.');
}

$erro = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($nome === '' || $email === '' || $pass === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif (strlen($pass) < 10) {
        $erro = 'A password deve ter pelo menos 10 caracteres.';
    } elseif ($pass !== $pass2) {
        $erro = 'As passwords não coincidem.';
    } else {
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
        db()->prepare('INSERT INTO admins (nome, email, password_hash) VALUES (?, ?, ?)')
           ->execute([$nome, $email, $hash]);
        $sucesso = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<title>Instalação · Jornadas IPS 2026</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="pagina-login">
<div class="login-caixa">
  <div class="login-marca">
    <p class="login-inst">Instituto Politécnico de Saurimo</p>
    <h1 class="login-titulo">Instalação</h1>
    <p class="login-sub">Criar o primeiro administrador</p>
  </div>

  <?php if ($sucesso): ?>
    <div class="aviso aviso-sucesso">
      Administrador criado com sucesso.<br>
      <strong>Apague agora o ficheiro <code>admin/instalar.php</code>.</strong>
    </div>
    <p><a href="login.php" class="botao">Ir para o login</a></p>

  <?php else: ?>
    <?php if ($erro): ?><div class="aviso aviso-erro"><?= e($erro) ?></div><?php endif; ?>

    <form method="post" class="login-form">
      <div class="campo">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required value="<?= e($_POST['nome'] ?? '') ?>">
      </div>
      <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
      </div>
      <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="10">
      </div>
      <div class="campo">
        <label for="password2">Repetir password</label>
        <input type="password" id="password2" name="password2" required minlength="10">
      </div>
      <button type="submit" class="botao">Criar administrador</button>
    </form>
  <?php endif; ?>
</div>
</body>
</html>