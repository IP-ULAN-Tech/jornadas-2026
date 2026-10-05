<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/db.php';

// ─── Editar aqui antes de correr ───────────────────────────
$email    = 'coordenacao@ips.ao';
$password = 'troca-esta-password-forte-agora';
$nome     = 'Coordenação das Jornadas';
// ────────────────────────────────────────────────────────────

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Email inválido.');
}
if (strlen($password) < 10) {
    exit('Password deve ter pelo menos 10 caracteres.');
}

try {
    $stmt = db()->prepare('SELECT id FROM admins WHERE email = ?');
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        exit('Já existe um admin com este email.');
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    db()->prepare('INSERT INTO admins (email, password_hash, nome) VALUES (?, ?, ?)')
       ->execute([$email, $hash, $nome]);

    echo "<h2>Admin criado com sucesso</h2>";
    echo "<p>Email: <strong>" . htmlspecialchars($email) . "</strong></p>";
    echo "<p>Apaga este ficheiro agora: <code>install/criar-admin.php</code></p>";
    echo '<p><a href="../admin/login.php">Ir para o login</a></p>';

} catch (Throwable $e) {
    echo 'Erro: ' . htmlspecialchars($e->getMessage());
}