<?php
$tituloPagina = 'Premiação';
require __DIR__ . '/inc/header.php';

$mensagem = flash('mensagem');
$erro = '';

/* 
   Processar criação / edição
    */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido($_POST['csrf'] ?? null)) {

    try {
        $id         = (int) ($_POST['id'] ?? 0);
        $lugar      = post('lugar');
        $nome       = post('nome');
        $descricao  = post('descricao');
        $destaque   = isset($_POST['destaque']) ? 1 : 0;
        $ordem      = (int) post('ordem', '0');

        if ($lugar === '') {
            throw new RuntimeException('A posição é obrigatória.');
        }

        // Buscar foto actual (se edição)
        $fotoAtual = null;
        if ($id > 0) {
            $stmt = db()->prepare('SELECT foto FROM premios WHERE id = ?');
            $stmt->execute([$id]);
            $fotoAtual = $stmt->fetchColumn() ?: null;
        }

        // Guardar foto se foi carregada
        $foto = $fotoAtual;
        if (!empty($_FILES['foto']['name'])) {
            $foto = guardar_imagem('foto', 'premios', $fotoAtual);
        }

        if ($id > 0) {
            db()->prepare('
                UPDATE premios
                SET lugar=?, nome=?, descricao=?, foto=?, destaque=?, ordem=?
                WHERE id=?
            ')->execute([$lugar, $nome ?: null, $descricao ?: null, $foto, $destaque, $ordem, $id]);
            registar_log((int) $_SESSION['admin_id'], 'editar', "premios #$id");
            flash('mensagem', 'Prémio actualizado.');
        } else {
            db()->prepare('
                INSERT INTO premios (lugar, nome, descricao, foto, destaque, ordem)
                VALUES (?, ?, ?, ?, ?, ?)
            ')->execute([$lugar, $nome ?: null, $descricao ?: null, $foto, $destaque, $ordem]);
            registar_log((int) $_SESSION['admin_id'], 'criar', 'premios');
            flash('mensagem', 'Prémio criado.');
        }

        redirecionar('premiacao.php');

    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}

/* ============================================================
   Eliminar
   ============================================================ */
if (get('acao') === 'eliminar' && csrf_valido(get('csrf'))) {
    $id = (int) get('id');

    // Apagar foto do disco
    $stmt = db()->prepare('SELECT foto FROM premios WHERE id = ?');
    $stmt->execute([$id]);
    $foto = $stmt->fetchColumn();
    if ($foto) apagar_imagem($foto);

    db()->prepare('DELETE FROM premios WHERE id = ?')->execute([$id]);
    registar_log((int) $_SESSION['admin_id'], 'eliminar', "premios #$id");
    flash('mensagem', 'Prémio eliminado.');
    redirecionar('premiacao.php');
}

/* ============================================================
   Registos para edição
   ============================================================ */
$editar = null;
if (get('acao') === 'editar') {
    $stmt = db()->prepare('SELECT * FROM premios WHERE id = ?');
    $stmt->execute([(int) get('id')]);
    $editar = $stmt->fetch() ?: null;
}

/* ============================================================
   Listar
   ============================================================ */
$premios = db()->query('SELECT * FROM premios ORDER BY ordem, id')->fetchAll();
?>

<?php if ($mensagem): ?><div class="aviso aviso-sucesso"><?= e($mensagem) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="aviso aviso-erro"><?= e($erro) ?></div><?php endif; ?>

<!-- ============ FORMULÁRIO ============ -->
<section class="cartao">
  <div class="cartao-topo">
    <h2 class="cartao-titulo"><?= $editar ? 'Editar Prémio' : 'Novo Prémio' ?></h2>
    <?php if ($editar): ?>
      <a href="premiacao.php" class="link-acao">Cancelar edição</a>
    <?php endif; ?>
  </div>

  <div class="cartao-corpo">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? 0) ?>">

      <div class="form-linha">
        <div class="campo">
          <label for="lugar">Posição</label>
          <input type="text" id="lugar" name="lugar" required maxlength="60"
                 placeholder="Ex.: 1.º Lugar, 2.º Lugar, 3.º Lugar, Menção Honrosa"
                 value="<?= e($editar['lugar'] ?? '') ?>">
        </div>

        <div class="campo">
          <label for="nome">Nome do grupo / integrantes</label>
          <input type="text" id="nome" name="nome" maxlength="180"
                 placeholder="Ex.: Grupo de Engenharia Informática"
                 value="<?= e($editar['nome'] ?? '') ?>">
          <small>Pode conter vários nomes separados por vírgula.</small>
        </div>
      </div>

      <div class="campo">
        <label for="descricao">Descrição curta (opcional)</label>
        <input type="text" id="descricao" name="descricao" maxlength="255"
               placeholder="Ex.: Sistema de irrigação automática para pequenas hortas"
               value="<?= e($editar['descricao'] ?? '') ?>">
      </div>

      <?php if ($editar && !empty($editar['foto'])): ?>
        <div class="campo">
          <label>Foto actual</label>
          <div style="margin-bottom:12px">
            <img src="<?= e(upload_url($editar['foto'])) ?>" alt=""
                 style="max-width:260px;border:1px solid var(--linha)">
          </div>
        </div>
      <?php endif; ?>

      <div class="campo">
        <label for="foto"><?= $editar ? 'Substituir foto' : 'Foto do grupo' ?></label>
        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
        <small>JPG, PNG ou WebP · Máx. 5 MB · Recomendado 800×600 ou superior</small>
      </div>

      <div class="form-linha">
        <div class="campo">
          <label for="ordem">Ordem</label>
          <input type="number" id="ordem" name="ordem" value="<?= (int) ($editar['ordem'] ?? 0) ?>">
        </div>
        <div class="campo">
          <label style="display:block;margin-top:32px">
            <input type="checkbox" name="destaque" value="1"
                   <?= !empty($editar['destaque']) ? 'checked' : '' ?>>
            Destacar visualmente (1.º lugar)
          </label>
        </div>
      </div>

      <div class="form-acoes">
        <button type="submit" class="botao botao-verde" style="width:auto">
          <?= $editar ? 'Guardar alterações' : 'Criar' ?>
        </button>
        <?php if ($editar): ?>
          <a href="premiacao.php" class="botao botao-sec" style="width:auto">Cancelar</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</section>

<!-- ============ LISTA ============ -->
<section class="cartao">
  <div class="cartao-topo">
    <h2 class="cartao-titulo">Prémios Registados</h2>
    <span style="font-size:13px;color:var(--tinta-500)"><?= count($premios) ?> registo(s)</span>
  </div>

  <?php if (!$premios): ?>
    <p class="vazio">Ainda não existem prémios. Cria o primeiro com o formulário acima.</p>
  <?php else: ?>
    <table class="tabela">
      <thead>
        <tr>
          <th>Foto</th>
          <th>Posição</th>
          <th>Nome</th>
          <th>Descrição</th>
          <th>Ordem</th>
          <th class="col-acao"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($premios as $p): ?>
          <tr>
            <td class="col-img">
              <?php if ($p['foto']): ?>
                <img src="<?= e(imagem_url($p['foto'], 'Foto')) ?>" alt="">
              <?php else: ?>
                <div style="width:90px;height:60px;background:var(--papel);border:1px solid var(--linha);display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--tinta-500)">
                  Sem foto
                </div>
              <?php endif; ?>
            </td>
            <td>
              <strong><?= e($p['lugar']) ?></strong>
              <?php if ($p['destaque']): ?>
                <span class="etiqueta etiqueta-ativo" style="margin-left:6px;font-size:10px">Destaque</span>
              <?php endif; ?>
            </td>
            <td><?= $p['nome'] ? e($p['nome']) : '<span style="color:#8A99B3">—</span>' ?></td>
            <td><?= $p['descricao'] ? e(mb_substr($p['descricao'], 0, 60)) . (mb_strlen($p['descricao']) > 60 ? '…' : '') : '<span style="color:#8A99B3">—</span>' ?></td>
            <td><?= (int) $p['ordem'] ?></td>
            <td class="col-acao">
              <a href="?acao=editar&id=<?= (int) $p['id'] ?>" class="link-acao">Editar</a>
              <a href="?acao=eliminar&id=<?= (int) $p['id'] ?>&csrf=<?= e(csrf_token()) ?>"
                 class="link-acao link-acao-perigo"
                 data-confirmar="Eliminar este prémio?">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>