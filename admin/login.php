<?php
require_once __DIR__ . '/../inc/auth.php';

if (admin_autenticado()) redirecionar('dashboard.php');

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    iniciar_sessao();
    $tentativas = $_SESSION['tentativas_login'] ?? ['qtd' => 0, 'ate' => 0];

    if ($tentativas['qtd'] >= 8 && time() < $tentativas['ate']) {
        $erro = 'Demasiadas tentativas. Aguarde 15 minutos.';
    } else {
        $stmt = db()->prepare('SELECT * FROM admins WHERE email = ?');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($pass, $admin['password_hash'])) {
            $tentativas['qtd'] = ($tentativas['qtd'] ?? 0) + 1;
            $tentativas['ate'] = time() + 900;
            $_SESSION['tentativas_login'] = $tentativas;

            registar_log(null, 'login_falhado', 'email=' . $email);
            $erro = 'Credenciais inválidas.';

        } elseif (!$admin['ativo']) {
            $erro = 'A sua conta está desactivada. Contacte o super administrador.';

        } else {
            session_regenerate_id(true);
            $_SESSION['admin_id']    = (int) $admin['id'];
            $_SESSION['admin_nome']  = $admin['nome'];
            $_SESSION['admin_nivel'] = (int) $admin['nivel'];
            $_SESSION['csrf']        = bin2hex(random_bytes(32));
            unset($_SESSION['tentativas_login']);

            db()->prepare('UPDATE admins SET ultimo_acesso = NOW() WHERE id = ?')
               ->execute([$admin['id']]);

            registar_log((int) $admin['id'], 'login', 'nivel=' . $admin['nivel']);

            redirecionar('dashboard.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Entrar · Painel Jornadas IPS 2026</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="pagina-login">

<div class="login-caixa">
  <div class="login-marca">
    <p class="login-inst">Instituto Politécnico de Saurimo</p>
    <h1 class="login-titulo">Entrar no painel</h1>
    <p class="login-sub">Jornadas Técnico-Científicas · Edição 2026</p>
  </div>

  <?php if ($erro): ?>
    <div class="aviso aviso-erro"><?= e($erro) ?></div>
  <?php endif; ?>

  <form method="post" class="login-form">
    <div class="campo">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required autocomplete="username"
             value="<?= e($_POST['email'] ?? '') ?>" autofocus>
    </div>
    <div class="campo">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required autocomplete="current-password">
    </div>
    <button type="submit" class="botao">Entrar</button>
  </form>
</div>

</body>
</html>