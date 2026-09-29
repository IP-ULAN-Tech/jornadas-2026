<?php
$tituloPagina = 'INOVA IPS';
require __DIR__ . '/inc/header.php';
exigir_editor_ou_super();

$aba = get('aba', 'blocos');
$mensagem = flash('mensagem');
$erro = '';

/* 
   Processar formulários
    */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido($_POST['csrf'] ?? null)) {
    $form = $_POST['form'] ?? '';

    try {

        /* ---------- Bloco ---------- */
        if ($form === 'bloco') {
            $id = (int) ($_POST['id'] ?? 0);
            $titulo = post('titulo');
            $descricao = post('descricao');
            $ordem = (int) post('ordem', '0');
            $ativo = isset($_POST['ativo']) ? 1 : 0;

            if ($titulo === '' || $descricao === '') {
                throw new RuntimeException('Título e descrição são obrigatórios.');
            }

            if ($id > 0) {
                db()->prepare('UPDATE inova_blocos SET titulo=?, descricao=?, ordem=?, ativo=? WHERE id=?')
                   ->execute([$titulo, $descricao, $ordem, $ativo, $id]);
                registar_log((int) $_SESSION['admin_id'], 'editar', "inova_blocos #$id");
                flash('mensagem', 'Bloco actualizado.');
            } else {
                db()->prepare('INSERT INTO inova_blocos (titulo, descricao, ordem, ativo) VALUES (?,?,?,?)')
                   ->execute([$titulo, $descricao, $ordem, $ativo]);
                registar_log((int) $_SESSION['admin_id'], 'criar', 'inova_blocos');
                flash('mensagem', 'Bloco criado.');
            }
            redirecionar('inova.php?aba=blocos');
        }

        /* ---------- Critério ---------- */
        if ($form === 'criterio') {
            $id = (int) ($_POST['id'] ?? 0);
            $nome = post('nome');
            $percentagem = (int) post('percentagem', '0');
            $ordem = (int) post('ordem', '0');

            if ($nome === '') throw new RuntimeException('O nome é obrigatório.');
            if ($percentagem < 0 || $percentagem > 100) {
                throw new RuntimeException('A percentagem deve estar entre 0 e 100.');
            }

            if ($id > 0) {
                db()->prepare('UPDATE inova_criterios SET nome=?, percentagem=?, ordem=? WHERE id=?')
                   ->execute([$nome, $percentagem, $ordem, $id]);
                registar_log((int) $_SESSION['admin_id'], 'editar', "inova_criterios #$id");
                flash('mensagem', 'Critério actualizado.');
            } else {
                db()->prepare('INSERT INTO inova_criterios (nome, percentagem, ordem) VALUES (?,?,?)')
                   ->execute([$nome, $percentagem, $ordem]);
                registar_log((int) $_SESSION['admin_id'], 'criar', 'inova_criterios');
                flash('mensagem', 'Critério criado.');
            }
            redirecionar('inova.php?aba=criterios');
        }

        /* ---------- Item de lista ---------- */
        if ($form === 'lista') {
            $id = (int) ($_POST['id'] ?? 0);
            $grupo = post('grupo');
            $texto = post('texto');
            $ordem = (int) post('ordem', '0');

            if (!in_array($grupo, ['participacao', 'aceites'], true)) {
                throw new RuntimeException('Grupo inválido.');
            }
            if ($texto === '') throw new RuntimeException('O texto é obrigatório.');

            if ($id > 0) {
                db()->prepare('UPDATE inova_listas SET grupo=?, texto=?, ordem=? WHERE id=?')
                   ->execute([$grupo, $texto, $ordem, $id]);
                registar_log((int) $_SESSION['admin_id'], 'editar', "inova_listas #$id");
                flash('mensagem', 'Item actualizado.');
            } else {
                db()->prepare('INSERT INTO inova_listas (grupo, texto, ordem) VALUES (?,?,?)')
                   ->execute([$grupo, $texto, $ordem]);
                registar_log((int) $_SESSION['admin_id'], 'criar', 'inova_listas');
                flash('mensagem', 'Item criado.');
            }
            redirecionar('inova.php?aba=listas');
        }

        /* ---------- Projecto ---------- */
        if ($form === 'projeto') {
            $id = (int) ($_POST['id'] ?? 0);
            $titulo = post('titulo');
            $descricao = post('descricao');
            $curso = post('curso');
            $ordem = (int) post('ordem', '0');
            $ativo = isset($_POST['ativo']) ? 1 : 0;

            if ($titulo === '') throw new RuntimeException('O título é obrigatório.');

            // Buscar imagem actual
            $imagemAtual = null;
            if ($id > 0) {
                $stmt = db()->prepare('SELECT imagem FROM inova_projetos WHERE id = ?');
                $stmt->execute([$id]);
                $imagemAtual = $stmt->fetchColumn() ?: null;
            }

            if ($id === 0 && empty($_FILES['imagem']['name'])) {
                throw new RuntimeException('Selecione uma imagem para o projecto.');
            }

            $imagem = guardar_imagem('imagem', 'inova', $imagemAtual);

            if ($id > 0) {
                db()->prepare('UPDATE inova_projetos SET titulo=?, descricao=?, curso=?, imagem=?, ordem=?, ativo=? WHERE id=?')
                   ->execute([$titulo, $descricao ?: null, $curso ?: null, $imagem, $ordem, $ativo, $id]);
                registar_log((int) $_SESSION['admin_id'], 'editar', "inova_projetos #$id");
                flash('mensagem', 'Projecto actualizado.');
            } else {
                db()->prepare('INSERT INTO inova_projetos (titulo, descricao, curso, imagem, ordem, ativo) VALUES (?,?,?,?,?,?)')
                   ->execute([$titulo, $descricao ?: null, $curso ?: null, $imagem, $ordem, $ativo]);
                registar_log((int) $_SESSION['admin_id'], 'criar', 'inova_projetos');
                flash('mensagem', 'Projecto criado.');
            }
            redirecionar('inova.php?aba=projetos');
        }

    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}

/* 
   Eliminações
    */
if (get('acao') === 'eliminar' && csrf_valido(get('csrf'))) {
    $id = (int) get('id');
    $tipo = get('tipo');

    if ($tipo === 'bloco') {
        db()->prepare('DELETE FROM inova_blocos WHERE id = ?')->execute([$id]);
        flash('mensagem', 'Bloco eliminado.');
        redirecionar('inova.php?aba=blocos');

    } elseif ($tipo === 'criterio') {
        db()->prepare('DELETE FROM inova_criterios WHERE id = ?')->execute([$id]);
        flash('mensagem', 'Critério eliminado.');
        redirecionar('inova.php?aba=criterios');

    } elseif ($tipo === 'lista') {
        db()->prepare('DELETE FROM inova_listas WHERE id = ?')->execute([$id]);
        flash('mensagem', 'Item eliminado.');
        redirecionar('inova.php?aba=listas');

    } elseif ($tipo === 'projeto') {
        $stmt = db()->prepare('SELECT imagem FROM inova_projetos WHERE id = ?');
        $stmt->execute([$id]);
        $img = $stmt->fetchColumn();
        if ($img) apagar_imagem($img);

        db()->prepare('DELETE FROM inova_projetos WHERE id = ?')->execute([$id]);
        flash('mensagem', 'Projecto eliminado.');
        redirecionar('inova.php?aba=projetos');
    }
}

/* 
   Carregar registo para edição
    */
$editar = null;
if (get('acao') === 'editar') {
    $id = (int) get('id');
    $tipo = get('tipo');

    $tabelas = [
        'bloco'    => 'inova_blocos',
        'criterio' => 'inova_criterios',
        'lista'    => 'inova_listas',
        'projeto'  => 'inova_projetos',
    ];

    if (isset($tabelas[$tipo])) {
        $stmt = db()->prepare('SELECT * FROM ' . $tabelas[$tipo] . ' WHERE id = ?');
        $stmt->execute([$id]);
        $editar = $stmt->fetch();
    }
}

/* 
   Listar dados
 */
$blocos = db()->query('SELECT * FROM inova_blocos ORDER BY ordem, id')->fetchAll();
$criterios = db()->query('SELECT * FROM inova_criterios ORDER BY ordem, id')->fetchAll();
$listas = db()->query('SELECT * FROM inova_listas ORDER BY grupo, ordem, id')->fetchAll();
$projetos = db()->query('SELECT * FROM inova_projetos ORDER BY ordem, id')->fetchAll();
?>

<?php if ($mensagem): ?>
  <div class="aviso aviso-sucesso"><?= e($mensagem) ?></div>
<?php endif; ?>

<?php if ($erro): ?>
  <div class="aviso aviso-erro"><?= e($erro) ?></div>
<?php endif; ?>

<div class="form-inline" style="margin-bottom:20px;gap:8px">
  <a href="?aba=blocos"    class="botao-ligacao <?= $aba === 'blocos'    ? 'botao-verde' : '' ?>">Blocos Informativos</a>
  <a href="?aba=criterios" class="botao-ligacao <?= $aba === 'criterios' ? 'botao-verde' : '' ?>">Critérios de Avaliação</a>
  <a href="?aba=listas"    class="botao-ligacao <?= $aba === 'listas'    ? 'botao-verde' : '' ?>">Listas</a>
  <a href="?aba=projetos"  class="botao-ligacao <?= $aba === 'projetos'  ? 'botao-verde' : '' ?>">Projectos em Carrossel</a>
</div>

<?php /* 
       ABA: BLOCOS
        */ ?>
<?php if ($aba === 'blocos'): ?>

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo"><?= $editar ? 'Editar Bloco' : 'Novo Bloco' ?></h2>
      <?php if ($editar): ?><a href="?aba=blocos" class="link-acao">Cancelar edição</a><?php endif; ?>
    </div>
    <div class="cartao-corpo">
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="form" value="bloco">
        <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? 0) ?>">

        <div class="campo">
          <label for="titulo">Título</label>
          <input type="text" id="titulo" name="titulo" required maxlength="150" value="<?= e($editar['titulo'] ?? '') ?>">
        </div>
        <div class="campo">
          <label for="descricao">Descrição</label>
          <textarea id="descricao" name="descricao" rows="3" required><?= e($editar['descricao'] ?? '') ?></textarea>
        </div>
        <div class="form-linha">
          <div class="campo">
            <label for="ordem">Ordem</label>
            <input type="number" id="ordem" name="ordem" value="<?= (int) ($editar['ordem'] ?? 0) ?>">
          </div>
          <div class="campo">
            <label style="display:block;margin-top:32px">
              <input type="checkbox" name="ativo" value="1" <?= !isset($editar['ativo']) || $editar['ativo'] ? 'checked' : '' ?>> Activo
            </label>
          </div>
        </div>
        <div class="form-acoes">
          <button type="submit" class="botao botao-verde" style="width:auto"><?= $editar ? 'Guardar' : 'Criar' ?></button>
          <?php if ($editar): ?><a href="?aba=blocos" class="botao botao-sec" style="width:auto">Cancelar</a><?php endif; ?>
        </div>
      </form>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Blocos Existentes</h2></div>
    <?php if (!$blocos): ?>
      <p class="vazio">Ainda não existem blocos.</p>
    <?php else: ?>
      <table class="tabela">
        <thead><tr><th>Título</th><th>Descrição</th><th>Ordem</th><th>Estado</th><th class="col-acao"></th></tr></thead>
        <tbody>
          <?php foreach ($blocos as $l): ?>
            <tr>
              <td><strong><?= e($l['titulo']) ?></strong></td>
              <td><?= e(mb_substr($l['descricao'], 0, 80)) ?>…</td>
              <td><?= (int) $l['ordem'] ?></td>
              <td><span class="etiqueta etiqueta-<?= $l['ativo'] ? 'ativo' : 'inativo' ?>"><?= $l['ativo'] ? 'Activo' : 'Inactivo' ?></span></td>
              <td class="col-acao">
                <a href="?aba=blocos&acao=editar&tipo=bloco&id=<?= (int) $l['id'] ?>" class="link-acao">Editar</a>
                <a href="?aba=blocos&acao=eliminar&tipo=bloco&id=<?= (int) $l['id'] ?>&csrf=<?= e(csrf_token()) ?>"
                   class="link-acao link-acao-perigo" data-confirmar="Eliminar este bloco?">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

<?php /* 
       ABA: CRITÉRIOS
        */ ?>
<?php elseif ($aba === 'criterios'): ?>

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo"><?= $editar ? 'Editar Critério' : 'Novo Critério' ?></h2>
      <?php if ($editar): ?><a href="?aba=criterios" class="link-acao">Cancelar edição</a><?php endif; ?>
    </div>
    <div class="cartao-corpo">
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="form" value="criterio">
        <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? 0) ?>">

        <div class="campo">
          <label for="nome">Nome do critério</label>
          <input type="text" id="nome" name="nome" required maxlength="180" value="<?= e($editar['nome'] ?? '') ?>">
        </div>
        <div class="form-linha">
          <div class="campo">
            <label for="percentagem">Percentagem (0 a 100)</label>
            <input type="number" id="percentagem" name="percentagem" min="0" max="100" required value="<?= (int) ($editar['percentagem'] ?? 0) ?>">
          </div>
          <div class="campo">
            <label for="ordem">Ordem</label>
            <input type="number" id="ordem" name="ordem" value="<?= (int) ($editar['ordem'] ?? 0) ?>">
          </div>
        </div>
        <div class="form-acoes">
          <button type="submit" class="botao botao-verde" style="width:auto"><?= $editar ? 'Guardar' : 'Criar' ?></button>
          <?php if ($editar): ?><a href="?aba=criterios" class="botao botao-sec" style="width:auto">Cancelar</a><?php endif; ?>
        </div>
      </form>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Critérios Existentes</h2></div>
    <?php if (!$criterios): ?>
      <p class="vazio">Ainda não existem critérios.</p>
    <?php else: ?>
      <table class="tabela">
        <thead><tr><th>Nome</th><th>Percentagem</th><th>Ordem</th><th class="col-acao"></th></tr></thead>
        <tbody>
          <?php foreach ($criterios as $c): ?>
            <tr>
              <td><?= e($c['nome']) ?></td>
              <td><strong><?= (int) $c['percentagem'] ?>%</strong></td>
              <td><?= (int) $c['ordem'] ?></td>
              <td class="col-acao">
                <a href="?aba=criterios&acao=editar&tipo=criterio&id=<?= (int) $c['id'] ?>" class="link-acao">Editar</a>
                <a href="?aba=criterios&acao=eliminar&tipo=criterio&id=<?= (int) $c['id'] ?>&csrf=<?= e(csrf_token()) ?>"
                   class="link-acao link-acao-perigo" data-confirmar="Eliminar este critério?">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

<?php /* 
       ABA: LISTAS
        */ ?>
<?php elseif ($aba === 'listas'): ?>

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo"><?= $editar ? 'Editar Item' : 'Novo Item' ?></h2>
      <?php if ($editar): ?><a href="?aba=listas" class="link-acao">Cancelar edição</a><?php endif; ?>
    </div>
    <div class="cartao-corpo">
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="form" value="lista">
        <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? 0) ?>">

        <div class="campo">
          <label for="grupo">Grupo</label>
          <select id="grupo" name="grupo">
            <option value="participacao" <?= ($editar['grupo'] ?? '') === 'participacao' ? 'selected' : '' ?>>Quem pode participar</option>
            <option value="aceites" <?= ($editar['grupo'] ?? '') === 'aceites' ? 'selected' : '' ?>>Projectos aceites</option>
          </select>
        </div>
        <div class="campo">
          <label for="texto">Texto</label>
          <input type="text" id="texto" name="texto" required maxlength="255" value="<?= e($editar['texto'] ?? '') ?>">
        </div>
        <div class="campo">
          <label for="ordem">Ordem</label>
          <input type="number" id="ordem" name="ordem" value="<?= (int) ($editar['ordem'] ?? 0) ?>">
        </div>
        <div class="form-acoes">
          <button type="submit" class="botao botao-verde" style="width:auto"><?= $editar ? 'Guardar' : 'Criar' ?></button>
          <?php if ($editar): ?><a href="?aba=listas" class="botao botao-sec" style="width:auto">Cancelar</a><?php endif; ?>
        </div>
      </form>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Itens Existentes</h2></div>
    <?php if (!$listas): ?>
      <p class="vazio">Ainda não existem itens.</p>
    <?php else: ?>
      <table class="tabela">
        <thead><tr><th>Grupo</th><th>Texto</th><th>Ordem</th><th class="col-acao"></th></tr></thead>
        <tbody>
          <?php foreach ($listas as $l): ?>
            <tr>
              <td><?= $l['grupo'] === 'participacao' ? 'Participação' : 'Aceites' ?></td>
              <td><?= e($l['texto']) ?></td>
              <td><?= (int) $l['ordem'] ?></td>
              <td class="col-acao">
                <a href="?aba=listas&acao=editar&tipo=lista&id=<?= (int) $l['id'] ?>" class="link-acao">Editar</a>
                <a href="?aba=listas&acao=eliminar&tipo=lista&id=<?= (int) $l['id'] ?>&csrf=<?= e(csrf_token()) ?>"
                   class="link-acao link-acao-perigo" data-confirmar="Eliminar este item?">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

<?php /* 
       ABA: PROJECTOS
        */ ?>
<?php else: ?>

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo"><?= $editar ? 'Editar Projecto' : 'Novo Projecto' ?></h2>
      <?php if ($editar): ?><a href="?aba=projetos" class="link-acao">Cancelar edição</a><?php endif; ?>
    </div>
    <div class="cartao-corpo">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="form" value="projeto">
        <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? 0) ?>">

        <div class="campo">
          <label for="titulo">Título do projecto</label>
          <input type="text" id="titulo" name="titulo" required maxlength="180"
                 placeholder="Ex.: Sistema de irrigação automática"
                 value="<?= e($editar['titulo'] ?? '') ?>">
        </div>

        <div class="campo">
          <label for="descricao">Descrição curta</label>
          <textarea id="descricao" name="descricao" rows="2" maxlength="255"
                    placeholder="Ex.: Protótipo de automação para pequenas hortas familiares"><?= e($editar['descricao'] ?? '') ?></textarea>
        </div>

        <div class="campo">
          <label for="curso">Curso / Autores (opcional)</label>
          <input type="text" id="curso" name="curso" maxlength="150"
                 placeholder="Ex.: Engenharia Informática · Grupo 3"
                 value="<?= e($editar['curso'] ?? '') ?>">
        </div>

        <?php if ($editar && !empty($editar['imagem'])): ?>
          <div class="campo">
            <label>Imagem actual</label>
            <div style="margin-bottom:12px">
              <img src="<?= e(imagem_url($editar['imagem'], 'Projecto')) ?>" alt=""
                   style="max-width:280px;border:1px solid var(--linha)">
            </div>
          </div>
        <?php endif; ?>

        <div class="campo">
          <label for="imagem"><?= $editar ? 'Substituir imagem' : 'Imagem do projecto' ?></label>
          <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp"
                 <?= $editar ? '' : 'required' ?>>
          <small>JPG, PNG ou WebP · Máx. 5 MB · Formato recomendado 4:3 horizontal</small>
        </div>

        <div class="form-linha">
          <div class="campo">
            <label for="ordem">Ordem</label>
            <input type="number" id="ordem" name="ordem" value="<?= (int) ($editar['ordem'] ?? 0) ?>">
          </div>
          <div class="campo">
            <label style="display:block;margin-top:32px">
              <input type="checkbox" name="ativo" value="1" <?= !isset($editar['ativo']) || $editar['ativo'] ? 'checked' : '' ?>> Activo
            </label>
          </div>
        </div>

        <div class="form-acoes">
          <button type="submit" class="botao botao-verde" style="width:auto"><?= $editar ? 'Guardar' : 'Criar' ?></button>
          <?php if ($editar): ?><a href="?aba=projetos" class="botao botao-sec" style="width:auto">Cancelar</a><?php endif; ?>
        </div>
      </form>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo">Projectos Registados</h2>
      <span style="font-size:13px;color:var(--tinta-500)"><?= count($projetos) ?> projecto(s)</span>
    </div>

    <?php if (!$projetos): ?>
      <p class="vazio">Ainda não existem projectos. Adiciona o primeiro acima.</p>
    <?php else: ?>
      <table class="tabela">
        <thead>
          <tr>
            <th>Imagem</th>
            <th>Título</th>
            <th>Curso / Autores</th>
            <th>Ordem</th>
            <th>Estado</th>
            <th class="col-acao"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($projetos as $p): ?>
            <tr>
              <td class="col-img">
                <img src="<?= e(imagem_url($p['imagem'], 'Projecto')) ?>" alt="">
              </td>
              <td>
                <strong><?= e($p['titulo']) ?></strong>
                <?php if ($p['descricao']): ?>
                  <div style="font-size:13px;color:var(--tinta-500);margin-top:4px">
                    <?= e(mb_substr($p['descricao'], 0, 70)) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td><?= $p['curso'] ? e($p['curso']) : '<span style="color:#8A99B3">—</span>' ?></td>
              <td><?= (int) $p['ordem'] ?></td>
              <td><span class="etiqueta etiqueta-<?= $p['ativo'] ? 'ativo' : 'inativo' ?>"><?= $p['ativo'] ? 'Activo' : 'Inactivo' ?></span></td>
              <td class="col-acao">
                <a href="?aba=projetos&acao=editar&tipo=projeto&id=<?= (int) $p['id'] ?>" class="link-acao">Editar</a>
                <a href="?aba=projetos&acao=eliminar&tipo=projeto&id=<?= (int) $p['id'] ?>&csrf=<?= e(csrf_token()) ?>"
                   class="link-acao link-acao-perigo" data-confirmar="Eliminar este projecto?">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

<?php endif; ?>

<?php require __DIR__ . '/inc/footer.php'; ?>