<?php
$tituloPagina = 'Painel';
require __DIR__ . '/inc/header.php';

$total     = (int) db()->query('SELECT COUNT(*) FROM submissoes')->fetchColumn();
$pendentes = (int) db()->query("SELECT COUNT(*) FROM submissoes WHERE status='pendente'")->fetchColumn();
$analise   = (int) db()->query("SELECT COUNT(*) FROM submissoes WHERE status='em_analise'")->fetchColumn();
$aceites   = (int) db()->query("SELECT COUNT(*) FROM submissoes WHERE status='aceite'")->fetchColumn();
$hoje      = (int) db()->query("SELECT COUNT(*) FROM submissoes WHERE DATE(criado_em)=CURDATE()")->fetchColumn();

$ultimas = db()->query('
    SELECT id, tipo, titulo, autores, status, criado_em
    FROM submissoes ORDER BY criado_em DESC LIMIT 8
')->fetchAll();

$logs = db()->query('
    SELECT l.acao, l.detalhe, l.criado_em, a.nome
    FROM logs l
    LEFT JOIN admins a ON a.id = l.admin_id
    ORDER BY l.criado_em DESC LIMIT 6
')->fetchAll();
?>

<section class="estatisticas">
  <div class="estat"><p class="estat-rotulo">Total</p><p class="estat-num"><?= $total ?></p></div>
  <div class="estat"><p class="estat-rotulo">Pendentes</p><p class="estat-num"><?= $pendentes ?></p></div>
  <div class="estat"><p class="estat-rotulo">Em análise</p><p class="estat-num"><?= $analise ?></p></div>
  <div class="estat"><p class="estat-rotulo">Aceites</p><p class="estat-num"><?= $aceites ?></p></div>
  <div class="estat"><p class="estat-rotulo">Submetidas hoje</p><p class="estat-num"><?= $hoje ?></p></div>
</section>

<section class="cartao">
  <div class="cartao-topo">
    <h2 class="cartao-titulo">Últimas submissões</h2>
    <a href="submissoes.php" class="link-acao">Ver todas</a>
  </div>
  <?php if (!$ultimas): ?>
    <p class="vazio">Ainda não existem submissões registadas.</p>
  <?php else: ?>
    <table class="tabela">
      <thead>
        <tr>
          <th>#</th>
          <th>Data</th>
          <th>Título</th>
          <th>Autor</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ultimas as $s): ?>
          <tr>
            <td><?= (int) $s['id'] ?></td>
            <td><?= e(date('d/m/Y H:i', strtotime($s['criado_em']))) ?></td>
            <td><?= e($s['titulo']) ?></td>
            <td><?= e(explode(';', $s['autores'])[0]) ?></td>
            <td><span class="etiqueta etiqueta-<?= e($s['status']) ?>"><?= e($s['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<section class="cartao">
  <div class="cartao-topo">
    <h2 class="cartao-titulo">Actividade recente</h2>
  </div>
  <?php if (!$logs): ?>
    <p class="vazio">Sem actividade registada.</p>
  <?php else: ?>
    <table class="tabela">
      <thead>
        <tr><th>Data</th><th>Administrador</th><th>Acção</th><th>Detalhe</th></tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $l): ?>
          <tr>
            <td><?= e(date('d/m/Y H:i', strtotime($l['criado_em']))) ?></td>
            <td><?= e($l['nome'] ?? '—') ?></td>
            <td><?= e($l['acao']) ?></td>
            <td><?= e($l['detalhe'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
