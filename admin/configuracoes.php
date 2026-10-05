<?php
$tituloPagina = 'Configurações';
require __DIR__ . '/../inc/auth.php';
exigir_editor_ou_super();

require __DIR__ . '/inc/header.php';

$mensagem = flash('mensagem');
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido($_POST['csrf'] ?? null)) {
    try {
        foreach ($_POST['settings'] ?? [] as $chave => $valor) {
            guardar_setting($chave, (string) $valor);
        }

        if (!empty($_FILES['logo']['name'])) {
            $actual = setting('site_logo');
            $novo = guardar_imagem('logo', 'geral', $actual ?: null);
            guardar_setting('site_logo', $novo);
        }

        if (!empty($_POST['remover_logo'])) {
            apagar_imagem(setting('site_logo'));
            guardar_setting('site_logo', null);
        }

        flash('mensagem', 'Configurações guardadas.');
        redirecionar('configuracoes.php');

    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}

$s = settings();
$logo = setting('site_logo');

function campo_setting(string $chave, string $label, string $tipo = 'texto', array $s = []): void
{
    $valor = $s[$chave] ?? '';
    echo '<div class="campo">';
    echo '<label for="s_' . e($chave) . '">' . e($label) . '</label>';
    if ($tipo === 'textarea') {
        echo '<textarea id="s_' . e($chave) . '" name="settings[' . e($chave) . ']" rows="3">' . e($valor) . '</textarea>';
    } else {
        echo '<input type="text" id="s_' . e($chave) . '" name="settings[' . e($chave) . ']" value="' . e($valor) . '">';
    }
    echo '</div>';
}
?>

<?php if ($mensagem): ?><div class="aviso aviso-sucesso"><?= e($mensagem) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="aviso aviso-erro"><?= e($erro) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Logotipo Institucional</h2></div>
    <div class="cartao-corpo">
      <p style="font-size:14px;color:var(--tinta-500);margin-bottom:20px;line-height:1.6">
        Logotipo que aparece no canto superior esquerdo do site, ao lado do nome do Instituto.
        Recomendado: PNG com fundo transparente, altura entre 100 e 200 px.
      </p>

      <?php if ($logo): ?>
        <div style="margin-bottom:20px;padding:20px;background:var(--papel);border:1px solid var(--linha);display:inline-block">
          <img src="<?= e(upload_url($logo)) ?>" alt="Logotipo actual"
               style="height:80px;width:auto;display:block">
        </div>
        <label style="margin-bottom:20px;display:block">
          <input type="checkbox" name="remover_logo" value="1"> Remover logotipo actual (volta ao texto IPS)
        </label>
      <?php else: ?>
        <div class="aviso aviso-info" style="margin-bottom:20px">
          Nenhum logotipo carregado. O site mostra o marcador "IPS" no círculo.
        </div>
      <?php endif; ?>

      <div class="campo">
        <label for="logo"><?= $logo ? 'Substituir logotipo' : 'Carregar logotipo' ?></label>
        <input type="file" id="logo" name="logo"
               accept="image/jpeg,image/png,image/webp"
               data-preview="#preview-logo">
        <div id="preview-logo" style="margin-top:12px"></div>
        <small>JPG, PNG ou WebP · Máx. 5 MB · PNG com fundo transparente é o ideal</small>
      </div>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Identidade do Evento</h2></div>
    <div class="cartao-corpo">
      <div class="form-linha">
        <?php campo_setting('evento_titulo_1', 'Título — linha 1', 'texto', $s); ?>
        <?php campo_setting('evento_titulo_2', 'Título — linha 2', 'texto', $s); ?>
      </div>
      <div class="form-linha">
        <?php campo_setting('evento_edicao', 'Edição', 'texto', $s); ?>
        <?php campo_setting('evento_data', 'Datas (texto)', 'texto', $s); ?>
      </div>
      <div class="form-linha">
        <?php campo_setting('evento_local_curto', 'Local curto', 'texto', $s); ?>
        <?php campo_setting('evento_organizacao', 'Organização', 'texto', $s); ?>
      </div>
      <?php campo_setting('evento_tema', 'Tema', 'textarea', $s); ?>
      <?php campo_setting('evento_lema', 'Lema', 'textarea', $s); ?>
      <div class="form-linha">
        <?php campo_setting('evento_instituicao', 'Instituição', 'texto', $s); ?>
        <?php campo_setting('evento_contexto', 'Contexto', 'texto', $s); ?>
      </div>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Sobre as Jornadas</h2></div>
    <div class="cartao-corpo">
      <?php campo_setting('sobre_abertura', 'Abertura (frase de destaque)', 'textarea', $s); ?>
      <?php campo_setting('sobre_paragrafo_1', 'Parágrafo 1', 'textarea', $s); ?>
      <?php campo_setting('sobre_paragrafo_2', 'Parágrafo 2', 'textarea', $s); ?>
      <?php campo_setting('sobre_legenda', 'Legenda da imagem', 'texto', $s); ?>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Números do Evento</h2></div>
    <div class="cartao-corpo">
      <div class="form-linha-3">
        <?php campo_setting('numeros_cursos', 'Cursos', 'texto', $s); ?>
        <?php campo_setting('numeros_eixos', 'Eixos', 'texto', $s); ?>
        <?php campo_setting('numeros_dias', 'Dias', 'texto', $s); ?>
      </div>
      <?php campo_setting('numeros_feira', 'Designação da feira paralela', 'texto', $s); ?>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">INOVA IPS</h2></div>
    <div class="cartao-corpo">
      <?php campo_setting('inova_intro', 'Introdução', 'textarea', $s); ?>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Galeria</h2></div>
    <div class="cartao-corpo">
      <div class="campo">
        <label for="galeria_estado">Estado da secção</label>
        <select id="galeria_estado" name="settings[galeria_estado]">
          <option value="brevemente" <?= ($s['galeria_estado'] ?? '') === 'brevemente' ? 'selected' : '' ?>>Brevemente</option>
          <option value="ativo" <?= ($s['galeria_estado'] ?? '') === 'ativo' ? 'selected' : '' ?>>Activo</option>
        </select>
      </div>
      <?php campo_setting('galeria_brevemente_texto', 'Texto do aviso', 'texto', $s); ?>
      <?php campo_setting('galeria_brevemente_nota', 'Nota do aviso', 'textarea', $s); ?>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Premiação</h2></div>
    <div class="cartao-corpo">
      <div class="campo">
        <label for="premiacao_estado">Estado da secção</label>
        <select id="premiacao_estado" name="settings[premiacao_estado]">
          <option value="brevemente" <?= ($s['premiacao_estado'] ?? '') === 'brevemente' ? 'selected' : '' ?>>Brevemente</option>
          <option value="ativo" <?= ($s['premiacao_estado'] ?? '') === 'ativo' ? 'selected' : '' ?>>Activo</option>
        </select>
      </div>
      <?php campo_setting('premiacao_brevemente_texto', 'Texto do aviso', 'texto', $s); ?>
      <?php campo_setting('premiacao_brevemente_nota', 'Nota do aviso', 'textarea', $s); ?>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Local do Evento</h2></div>
    <div class="cartao-corpo">
      <?php campo_setting('local_nome', 'Nome do local', 'texto', $s); ?>
      <div class="form-linha">
        <?php campo_setting('local_cidade', 'Cidade / Província', 'texto', $s); ?>
        <?php campo_setting('local_pais', 'País', 'texto', $s); ?>
      </div>
      <?php campo_setting('local_mapa_url', 'URL do mapa (Google Maps embed)', 'textarea', $s); ?>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Rodapé</h2></div>
    <div class="cartao-corpo">
      <?php campo_setting('rodape_lema', 'Lema do rodapé', 'texto', $s); ?>
    </div>
  </section>

  <div class="form-acoes">
    <button type="submit" class="botao botao-verde" style="width:auto">Guardar todas as alterações</button>
  </div>
</form>

<?php require __DIR__ . '/inc/footer.php'; ?>