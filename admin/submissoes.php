<?php
$tituloPagina = 'Submissões Recebidas';
require __DIR__ . '/inc/header.php';

$status = get('status');
$tipo   = get('tipo');
$q      = get('q');

$sql = 'SELECT * FROM submissoes WHERE 1=1';
$p = [];
if ($status) { $sql .= ' AND status = ?'; $p[] = $status; }
if ($tipo)   { $sql .= ' AND tipo = ?';   $p[] = $tipo; }
if ($q)      { $sql .= ' AND (titulo LIKE ? OR autores LIKE ? OR email LIKE ?)'; $l = "%$q%"; array_push($p, $l, $l, $l); }
$sql .= ' ORDER BY criado_em DESC LIMIT 500';

$stmt = db()->prepare($sql);
$stmt->execute($p);
$linhas = $stmt->fetchAll();

$total = (int) db()->query('SELECT COUNT(*) FROM submissoes')->fetchColumn();
$pendentes = (int) db()->query("SELECT COUNT(*) FROM submissoes WHERE status='pendente'")->fetchColumn();
$analise = (int) db()->query("SELECT COUNT(*) FROM submissoes WHERE status='em_analise'")->fetchColumn();
$aceites = (int) db()->query("SELECT COUNT(*) FROM submissoes WHERE status='aceite'")->fetchColumn();
$rejeitados = (int) db()->query("SELECT COUNT(*) FROM submissoes WHERE status='rejeitado'")->fetchColumn();
?>

<section class="estatisticas">
  <div class="estat"><p class="estat-rotulo">Total</p><p class="estat-num"><?= $total ?></p></div>
  <div class="estat"><p class="estat-rotulo">Pendentes</p><p class="estat-num"><?= $pendentes ?></p></div>
  <div class="estat"><p class="estat-rotulo">Em análise</p><p class="estat-num"><?= $analise ?></p></div>
  <div class="estat"><p class="estat-rotulo">Aceites</p><p class="estat-num"><?= $aceites ?></p></div>
  <div class="estat"><p class="estat-rotulo">Rejeitadas</p><p class="estat-num"><?= $rejeitados ?></p></div>
</section>

<section class="cartao">
  <div class="cartao-topo">
    <h2 class="cartao-titulo"><?= count($linhas) ?> submissões</h2>
    <a href="exportar-csv.php?<?= http_build_query(['status' => $status, 'tipo' => $tipo, 'q' => $q]) ?>"
       class="botao botao-sec botao-mini">Exportar CSV</a>
  </div>

  <div class="cartao-corpo" style="padding:16px 24px">
    <form method="get" class="form-inline">
      <div class="campo" style="flex:1;min-width:220px;margin:0">
        <input type="search" name="q" placeholder="Pesquisar por título, autor ou email" value="<?= e($q) ?>">
      </div>
      <div class="campo" style="margin:0">
        <select name="status">
          <option value="">Todos os estados</option>
          <option value="pendente" <?= $status === 'pendente' ? 'selected' : '' ?>>Pendente</option>
          <option value="em_analise" <?= $status === 'em_analise' ? 'selected' : '' ?>>Em análise</option>
          <option value="aceite" <?= $status === 'aceite' ? 'selected' : '' ?>>Aceite</option>
          <option value="rejeitado" <?= $status === 'rejeitado' ? 'selected' : '' ?>>Rejeitado</option>
        </select>
      </div>
      <div class="campo" style="margin:0">
        <select name="tipo">
          <option value="">Todos os tipos</option>
          <option value="comunicacao" <?= $tipo === 'comunicacao' ? 'selected' : '' ?>>Comunicação</option>
          <option value="poster" <?= $tipo === 'poster' ? 'selected' : '' ?>>Poster</option>
          <option value="projeto_inova" <?= $tipo === 'projeto_inova' ? 'selected' : '' ?>>Projecto INOVA</option>
        </select>
      </div>
      <button type="submit" class="botao botao-sec botao-mini">Filtrar</button>
    </form>
  </div>

  <?php if (!$linhas): ?>
    <p class="vazio">Nenhuma submissão encontrada.</p>
  <?php else: ?>
    <table class="tabela">
      <thead>
        <tr>
          <th>#</th>
          <th>Data</th>
          <th>Tipo</th>
          <th>Título</th>
          <th>Autor principal</th>
          <th>Estado</th>
          <th class="col-acao"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($linhas as $s): ?>
          <?php $tipos = ['comunicacao' => 'Comunicação', 'poster' => 'Poster', 'projeto_inova' => 'INOVA IPS']; ?>
          <tr>
            <td><?= (int) $s['id'] ?></td>
            <td><?= e(date('d/m/Y H:i', strtotime($s['criado_em']))) ?></td>
            <td><?= e($tipos[$s['tipo']] ?? $s['tipo']) ?></td>
            <td><strong><?= e($s['titulo']) ?></strong></td>
            <td><?= e(explode(';', $s['autores'])[0]) ?></td>
            <td><span class="etiqueta etiqueta-<?= e($s['status']) ?>"><?= e($s['status']) ?></span></td>
            <td class="col-acao">
              <a href="submissao-ver.php?id=<?= (int) $s['id'] ?>" class="link-acao">Ver</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
