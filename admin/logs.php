<?php
$tituloPagina = 'Registo de Actividade';
require __DIR__ . '/inc/auth.php';
exigir_super();

require __DIR__ . '/inc/header.php';

$limite = (int) get('limite', '200');
if ($limite < 50) $limite = 50;
if ($limite > 1000) $limite = 1000;

$filtroAdmin = (int) get('admin');

$sql = '
    SELECT l.*, a.nome AS admin_nome, a.email AS admin_email
    FROM logs l
    LEFT JOIN admins a ON a.id = l.admin_id
';
$params = [];

if ($filtroAdmin > 0) {
    $sql .= ' WHERE l.admin_id = ? ';
    $params[] = $filtroAdmin;
}

$sql .= ' ORDER BY l.criado_em DESC LIMIT ' . $limite;

$stmt = db()->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

$admins = db()->query('SELECT id, nome FROM admins ORDER BY nome')->fetchAll();
?>

<div class="form-inline" style="margin-bottom:20px;gap:12px;align-items:flex-end">
  <form method="get" class="form-inline" style="gap:8px;align-items:flex-end">
    <div class="campo" style="margin:0">
      <label for="admin">Filtrar por administrador</label>
      <select id="admin" name="admin" onchange="this.form.submit()">
        <option value="0">Todos</option>
        <?php foreach ($admins as $a): ?>
          <option value="<?= (int) $a['id'] ?>" <?= $filtroAdmin === (int) $a['id'] ? 'selected' : '' ?>>
            <?= e($a['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="campo" style="margin:0">
      <label for="limite">Mostrar</label>
      <select id="limite" name="limite" onchange="this.form.submit()">
        <option value="100" <?= $limite === 100 ? 'selected' : '' ?>>100 registos</option>
        <option value="200" <?= $limite === 200 ? 'selected' : '' ?>>200 registos</option>
        <option value="500" <?= $limite === 500 ? 'selected' : '' ?>>500 registos</option>
        <option value="1000" <?= $limite === 1000 ? 'selected' : '' ?>>1000 registos</option>
      </select>
    </div>
  </form>
</div>

<section class="cartao">
  <div class="cartao-topo">
    <h2 class="cartao-titulo">Actividade registada</h2>
    <span style="font-size:13px;color:var(--tinta-500)"><?= count($logs) ?> registo(s)</span>
  </div>

  <?php if (!$logs): ?>
    <p class="vazio">Sem actividade registada.</p>
  <?php else: ?>
    <table class="tabela">
      <thead>
        <tr>
          <th>Data</th>
          <th>Administrador</th>
          <th>Acção</th>
          <th>Detalhe</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $l): ?>
          <tr>
            <td style="white-space:nowrap"><?= e(date('d/m/Y H:i', strtotime($l['criado_em']))) ?></td>
            <td>
              <?php if ($l['admin_nome']): ?>
                <strong><?= e($l['admin_nome']) ?></strong>
                <div style="font-size:12px;color:var(--tinta-500)"><?= e($l['admin_email'] ?? '') ?></div>
              <?php else: ?>
                <span style="color:var(--tinta-500)">Sistema / público</span>
              <?php endif; ?>
            </td>
            <td><?= e($l['acao']) ?></td>
            <td style="font-size:13px;color:var(--tinta-500)"><?= e($l['detalhe'] ?? '') ?></td>
            <td style="font-size:13px;color:var(--tinta-500)"><?= e($l['ip'] ?? '—') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>