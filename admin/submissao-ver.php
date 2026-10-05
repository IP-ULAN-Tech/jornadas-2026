<?php
$tituloPagina = 'Detalhe da Submissão';
require __DIR__ . '/inc/header.php';

$id = (int) get('id');
$stmt = db()->prepare('SELECT * FROM submissoes WHERE id = ?');
$stmt->execute([$id]);
$s = $stmt->fetch();

if (!$s) {
    echo '<p class="vazio">Submissão não encontrada.</p>';
    require __DIR__ . '/inc/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido($_POST['csrf'] ?? null)) {
    $status = post('status');
    $obs    = post('observacoes');
    if (!in_array($status, ['pendente', 'em_analise', 'aceite', 'rejeitado'], true)) $status = $s['status'];

    db()->prepare('
        UPDATE submissoes
        SET status = ?, observacoes = ?, avaliado_por = ?, avaliado_em = NOW()
        WHERE id = ?
    ')->execute([$status, $obs, (int) $_SESSION['admin_id'], $id]);

    registar_log((int) $_SESSION['admin_id'], 'atualizar_submissao', "id=$id status=$status");
    flash('mensagem', 'Submissão actualizada.');
    redirecionar('submissao-ver.php?id=' . $id);
}

$mensagem = flash('mensagem');
$tipos = ['comunicacao' => 'Comunicação Oral', 'poster' => 'Poster Científico', 'projeto_inova' => 'Projecto INOVA IPS'];
?>

<?php if ($mensagem): ?><div class="aviso aviso-sucesso"><?= e($mensagem) ?></div><?php endif; ?>

<p style="margin-bottom:20px">
  <a href="submissoes.php" class="link-acao">← Voltar às submissões</a>
</p>

<section class="cartao">
  <div class="cartao-topo">
    <div>
      <p style="font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--tinta-500);font-weight:600;margin-bottom:4px">
        Submissão #<?= (int) $s['id'] ?>
      </p>
      <h2 class="cartao-titulo" style="font-size:20px"><?= e($s['titulo']) ?></h2>
    </div>
    <span class="etiqueta etiqueta-<?= e($s['status']) ?>"><?= e($s['status']) ?></span>
  </div>

  <div class="cartao-corpo">
    <dl style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px 24px;padding-bottom:20px;border-bottom:1px solid var(--linha);margin-bottom:24px">
      <div><dt style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--tinta-500);font-weight:600;margin-bottom:4px">Tipo</dt><dd><?= e($tipos[$s['tipo']] ?? $s['tipo']) ?></dd></div>
      <?php if ($s['eixo']): ?>
        <div><dt style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--tinta-500);font-weight:600;margin-bottom:4px">Eixo</dt><dd>Eixo <?= e($s['eixo']) ?></dd></div>
      <?php endif; ?>
      <div><dt style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--tinta-500);font-weight:600;margin-bottom:4px">Autores</dt><dd><?= e($s['autores']) ?></dd></div>
      <?php if ($s['instituicao']): ?>
        <div><dt style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--tinta-500);font-weight:600;margin-bottom:4px">Instituição</dt><dd><?= e($s['instituicao']) ?></dd></div>
      <?php endif; ?>
      <div><dt style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--tinta-500);font-weight:600;margin-bottom:4px">Email</dt><dd><a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a></dd></div>
      <?php if ($s['telefone']): ?>
        <div><dt style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--tinta-500);font-weight:600;margin-bottom:4px">Telefone</dt><dd><?= e($s['telefone']) ?></dd></div>
      <?php endif; ?>
      <div><dt style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--tinta-500);font-weight:600;margin-bottom:4px">Submetido em</dt><dd><?= e(date('d/m/Y H:i', strtotime($s['criado_em']))) ?></dd></div>
      <?php if ($s['area_inova']): ?>
        <div><dt style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--tinta-500);font-weight:600;margin-bottom:4px">Área INOVA</dt><dd><?= e($s['area_inova']) ?></dd></div>
      <?php endif; ?>
    </dl>

    <?php if ($s['elementos_equipa']): ?>
      <h3 style="font-family:var(--font-titulo);font-size:14px;letter-spacing:.04em;text-transform:uppercase;color:var(--tinta-700);margin-bottom:8px">Equipa</h3>
      <p style="white-space:pre-wrap;color:var(--tinta-700);margin-bottom:20px"><?= e($s['elementos_equipa']) ?></p>
    <?php endif; ?>

    <h3 style="font-family:var(--font-titulo);font-size:14px;letter-spacing:.04em;text-transform:uppercase;color:var(--tinta-700);margin-bottom:8px">Resumo</h3>
    <p style="white-space:pre-wrap;color:var(--tinta-700);line-height:1.7;margin-bottom:20px"><?= e($s['resumo']) ?></p>

    <?php if ($s['palavras_chave']): ?>
      <p style="color:var(--tinta-700);margin-bottom:20px"><strong>Palavras-chave:</strong> <?= e($s['palavras_chave']) ?></p>
    <?php endif; ?>

    <?php if ($s['ficheiro_guardado']): ?>
      <a href="../api/ficheiro.php?id=<?= (int) $s['id'] ?>" target="_blank"
         class="botao botao-verde botao-mini">Abrir PDF (<?= e($s['ficheiro_original']) ?>)</a>
    <?php else: ?>
      <p class="aviso aviso-info">Sem ficheiro anexado a esta submissão.</p>
    <?php endif; ?>
  </div>
</section>

<section class="cartao">
  <div class="cartao-topo"><h2 class="cartao-titulo">Avaliação</h2></div>
  <div class="cartao-corpo">
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

      <div class="campo">
        <label>Estado</label>
        <select name="status">
          <option value="pendente"   <?= $s['status'] === 'pendente'   ? 'selected' : '' ?>>Pendente</option>
          <option value="em_analise" <?= $s['status'] === 'em_analise' ? 'selected' : '' ?>>Em análise</option>
          <option value="aceite"     <?= $s['status'] === 'aceite'     ? 'selected' : '' ?>>Aceite</option>
          <option value="rejeitado"  <?= $s['status'] === 'rejeitado'  ? 'selected' : '' ?>>Rejeitado</option>
        </select>
      </div>

      <div class="campo">
        <label>Observações internas</label>
        <textarea name="observacoes" rows="4"><?= e($s['observacoes'] ?? '') ?></textarea>
      </div>

      <div class="form-acoes">
        <button type="submit" class="botao botao-verde" style="width:auto">Guardar avaliação</button>
      </div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
