<?php
$tituloPagina = 'Administradores';
require __DIR__ . '/../inc/auth.php';
exigir_super();      // ← antes de qualquer HTML

require __DIR__ . '/inc/header.php';

$mensagem = flash('mensagem');
$erro = flash('erro');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido($_POST['csrf'] ?? null)) {
    try {
        $id       = (int) ($_POST['id'] ?? 0);
        $nome     = post('nome');
        $email    = strtolower(post('email'));
        $password = $_POST['password'] ?? '';
        $nivel    = (int) post('nivel', '2');
        $ativo    = isset($_POST['ativo']) ? 1 : 0;
        $notas    = post('notas');

        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Nome e email válidos são obrigatórios.');
        }

        if (!in_array($nivel, [NIVEL_SUPER, NIVEL_EDITOR, NIVEL_AVALIADOR], true)) {
            throw new RuntimeException('Nível inválido.');
        }

        if ($id > 0 && $id === (int) $_SESSION['admin_id']) {
            if ($nivel !== NIVEL_SUPER) {
                throw new RuntimeException('Não pode retirar o seu próprio acesso de super administrador.');
            }
            if (!$ativo) {
                throw new RuntimeException('Não pode desactivar a sua própria conta.');
            }
        }

        if ($id === 0 && strlen($password) < 10) {
            throw new RuntimeException('A password deve ter pelo menos 10 caracteres.');
        }

        if ($id > 0) {
            if ($password !== '') {
                if (strlen($password) < 10) {
                    throw new RuntimeException('A nova password deve ter pelo menos 10 caracteres.');
                }
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                db()->prepare('
                    UPDATE admins
                    SET nome = ?, email = ?, ativo = ?, nivel = ?, notas = ?, password_hash = ?
                    WHERE id = ?
                ')->execute([$nome, $email, $ativo, $nivel, $notas ?: null, $hash, $id]);
            } else {
                db()->prepare('
                    UPDATE admins
                    SET nome = ?, email = ?, ativo = ?, nivel = ?, notas = ?
                    WHERE id = ?
                ')->execute([$nome, $email, $ativo, $nivel, $notas ?: null, $id]);
            }
            registar_log((int) $_SESSION['admin_id'], 'editar_admin', "id=$id");
            flash('mensagem', 'Administrador actualizado.');

        } else {
            $stmt = db()->prepare('SELECT id FROM admins WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new RuntimeException('Já existe um administrador com este email.');
            }

            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            db()->prepare('
                INSERT INTO admins (nome, email, ativo, nivel, notas, password_hash)
                VALUES (?, ?, ?, ?, ?, ?)
            ')->execute([$nome, $email, $ativo, $nivel, $notas ?: null, $hash]);

            registar_log((int) $_SESSION['admin_id'], 'criar_admin', "email=$email nivel=$nivel");
            flash('mensagem', 'Administrador criado.');
        }

        redirecionar('administradores.php');

    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}

if (get('acao') === 'eliminar' && csrf_valido(get('csrf'))) {
    $id = (int) get('id');

    if ($id === (int) $_SESSION['admin_id']) {
        flash('erro', 'Não pode eliminar a sua própria conta.');
        redirecionar('administradores.php');
    }

    $stmt = db()->prepare('SELECT nome, email FROM admins WHERE id = ?');
    $stmt->execute([$id]);
    $alvo = $stmt->fetch();

    if ($alvo) {
        db()->prepare('DELETE FROM admins WHERE id = ?')->execute([$id]);
        registar_log((int) $_SESSION['admin_id'], 'eliminar_admin', 'id=' . $id . ' email=' . $alvo['email']);
        flash('mensagem', 'Administrador eliminado.');
    }
    redirecionar('administradores.php');
}

if (get('acao') === 'alternar' && csrf_valido(get('csrf'))) {
    $id = (int) get('id');

    if ($id === (int) $_SESSION['admin_id']) {
        flash('erro', 'Não pode desactivar a sua própria conta.');
        redirecionar('administradores.php');
    }

    db()->prepare('UPDATE admins SET ativo = 1 - ativo WHERE id = ?')->execute([$id]);
    registar_log((int) $_SESSION['admin_id'], 'alternar_admin', "id=$id");
    flash('mensagem', 'Estado alterado.');
    redirecionar('administradores.php');
}

$editar = null;
if (get('acao') === 'editar') {
    $stmt = db()->prepare('SELECT * FROM admins WHERE id = ?');
    $stmt->execute([(int) get('id')]);
    $editar = $stmt->fetch() ?: null;
}

$admins = db()->query('
    SELECT id, nome, email, ativo, nivel, notas, ultimo_acesso, criado_em
    FROM admins
    ORDER BY nivel, nome
')->fetchAll();
?>

<?php if ($mensagem): ?><div class="aviso aviso-sucesso"><?= e($mensagem) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="aviso aviso-erro"><?= e($erro) ?></div><?php endif; ?>

<section class="cartao">
  <div class="cartao-topo">
    <h2 class="cartao-titulo"><?= $editar ? 'Editar Administrador' : 'Novo Administrador' ?></h2>
    <?php if ($editar): ?><a href="administradores.php" class="link-acao">Cancelar edição</a><?php endif; ?>
  </div>

  <div class="cartao-corpo">
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? 0) ?>">

      <div class="form-linha">
        <div class="campo">
          <label for="nome">Nome completo</label>
          <input type="text" id="nome" name="nome" required maxlength="120"
                 value="<?= e($editar['nome'] ?? '') ?>">
        </div>
        <div class="campo">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required maxlength="190"
                 value="<?= e($editar['email'] ?? '') ?>">
        </div>
      </div>

      <div class="form-linha">
        <div class="campo">
          <label for="password">Password <?= $editar ? '(deixar vazio para manter)' : '' ?></label>
          <input type="password" id="password" name="password"
                 <?= $editar ? '' : 'required' ?> minlength="10">
          <small>Mínimo 10 caracteres.</small>
        </div>
        <div class="campo">
          <label for="nivel">Nível de acesso</label>
          <select id="nivel" name="nivel" required>
            <option value="<?= NIVEL_SUPER ?>"     <?= (int) ($editar['nivel'] ?? 2) === NIVEL_SUPER     ? 'selected' : '' ?>>Super Administrador</option>
            <option value="<?= NIVEL_EDITOR ?>"    <?= (int) ($editar['nivel'] ?? 2) === NIVEL_EDITOR    ? 'selected' : '' ?>>Editor</option>
            <option value="<?= NIVEL_AVALIADOR ?>" <?= (int) ($editar['nivel'] ?? 2) === NIVEL_AVALIADOR ? 'selected' : '' ?>>Avaliador</option>
          </select>
        </div>
      </div>

      <div class="campo">
        <label for="notas">Notas internas (opcional)</label>
        <input type="text" id="notas" name="notas" maxlength="255"
               placeholder="Ex.: Responsável pela comissão científica"
               value="<?= e($editar['notas'] ?? '') ?>">
      </div>

      <div class="campo">
        <label>
          <input type="checkbox" name="ativo" value="1"
                 <?= !isset($editar['ativo']) || $editar['ativo'] ? 'checked' : '' ?>>
          Conta activa
        </label>
      </div>

      <div class="form-acoes">
        <button type="submit" class="botao botao-verde" style="width:auto">
          <?= $editar ? 'Guardar alterações' : 'Criar administrador' ?>
        </button>
        <?php if ($editar): ?>
          <a href="administradores.php" class="botao botao-sec" style="width:auto">Cancelar</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</section>

<section class="cartao">
  <div class="cartao-topo">
    <h2 class="cartao-titulo">Níveis de acesso</h2>
  </div>
  <div class="cartao-corpo">
    <table class="tabela">
      <thead>
        <tr>
          <th>Nível</th>
          <th>Conteúdo do site</th>
          <th>Apagar</th>
          <th>Submissões</th>
          <th>Utilizadores</th>
          <th>Configurações</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Super Administrador</strong></td>
          <td>Sim</td>
          <td>Sim</td>
          <td>Sim</td>
          <td>Sim</td>
          <td>Sim</td>
        </tr>
        <tr>
          <td><strong>Editor</strong></td>
          <td>Sim</td>
          <td>Não</td>
          <td>Sim</td>
          <td>Não</td>
          <td>Não</td>
        </tr>
        <tr>
          <td><strong>Avaliador</strong></td>
          <td>Não</td>
          <td>Não</td>
          <td>Sim</td>
          <td>Não</td>
          <td>Não</td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section class="cartao">
  <div class="cartao-topo">
    <h2 class="cartao-titulo">Utilizadores registados</h2>
    <span style="font-size:13px;color:var(--tinta-500)"><?= count($admins) ?> conta(s)</span>
  </div>

  <?php if (!$admins): ?>
    <p class="vazio">Sem administradores.</p>
  <?php else: ?>
    <table class="tabela">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Email</th>
          <th>Nível</th>
          <th>Estado</th>
          <th>Último acesso</th>
          <th class="col-acao"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($admins as $a): ?>
          <?php $ehProprio = (int) $a['id'] === (int) $_SESSION['admin_id']; ?>
          <tr>
            <td>
              <strong><?= e($a['nome']) ?></strong>
              <?php if ($ehProprio): ?>
                <span class="etiqueta etiqueta-ativo" style="margin-left:6px;font-size:10px">Você</span>
              <?php endif; ?>
              <?php if ($a['notas']): ?>
                <div style="font-size:12px;color:var(--tinta-500);margin-top:4px"><?= e($a['notas']) ?></div>
              <?php endif; ?>
            </td>
            <td><?= e($a['email']) ?></td>
            <td>
              <span class="etiqueta <?= (int) $a['nivel'] === NIVEL_SUPER ? 'etiqueta-aceite' : ((int) $a['nivel'] === NIVEL_EDITOR ? 'etiqueta-em_analise' : 'etiqueta-pendente') ?>">
                <?= e(admin_abrev_nivel((int) $a['nivel'])) ?>
              </span>
            </td>
            <td>
              <span class="etiqueta etiqueta-<?= $a['ativo'] ? 'ativo' : 'inativo' ?>">
                <?= $a['ativo'] ? 'Activo' : 'Inactivo' ?>
              </span>
            </td>
            <td><?= $a['ultimo_acesso'] ? e(date('d/m/Y H:i', strtotime($a['ultimo_acesso']))) : '—' ?></td>
            <td class="col-acao">
              <a href="?acao=editar&id=<?= (int) $a['id'] ?>" class="link-acao">Editar</a>

              <?php if (!$ehProprio): ?>
                <a href="?acao=alternar&id=<?= (int) $a['id'] ?>&csrf=<?= e(csrf_token()) ?>"
                   class="link-acao"
                   data-confirmar="<?= $a['ativo'] ? 'Desactivar' : 'Activar' ?> este utilizador?">
                  <?= $a['ativo'] ? 'Desactivar' : 'Activar' ?>
                </a>
                <a href="?acao=eliminar&id=<?= (int) $a['id'] ?>&csrf=<?= e(csrf_token()) ?>"
                   class="link-acao link-acao-perigo"
                   data-confirmar="Eliminar este administrador?">Eliminar</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>