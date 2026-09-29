<?php
$tituloPagina = 'Sobre as Jornadas';
require __DIR__ . '/../inc/auth.php';
exigir_editor_ou_super();

require __DIR__ . '/inc/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido($_POST['csrf'] ?? null)) {
    try {
        foreach ($_POST['settings'] ?? [] as $chave => $valor) {
            guardar_setting($chave, (string) $valor);
        }

        if (!empty($_FILES['imagem']['name'])) {
            $actual = setting('sobre_imagem');
            $novo = guardar_imagem('imagem', 'sobre', $actual ?: null);
            guardar_setting('sobre_imagem', $novo);
        }

        if (!empty($_POST['remover_imagem'])) {
            apagar_imagem(setting('sobre_imagem'));
            guardar_setting('sobre_imagem', null);
        }

        flash('mensagem', 'Guardado com sucesso.');
        redirecionar('sobre.php');
    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}

$mensagem = flash('mensagem');
$s = settings();
$imagem = setting('sobre_imagem');
?>

<?php if ($mensagem): ?><div class="aviso aviso-sucesso"><?= e($mensagem) ?></div><?php endif; ?>
<?php if (!empty($erro)): ?><div class="aviso aviso-erro"><?= e($erro) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Texto da Secção</h2></div>
    <div class="cartao-corpo">
      <div class="campo">
        <label>Abertura</label>
        <textarea name="settings[sobre_abertura]" rows="3"><?= e($s['sobre_abertura'] ?? '') ?></textarea>
      </div>
      <div class="campo">
        <label>Parágrafo 1</label>
        <textarea name="settings[sobre_paragrafo_1]" rows="3"><?= e($s['sobre_paragrafo_1'] ?? '') ?></textarea>
      </div>
      <div class="campo">
        <label>Parágrafo 2</label>
        <textarea name="settings[sobre_paragrafo_2]" rows="3"><?= e($s['sobre_paragrafo_2'] ?? '') ?></textarea>
      </div>
    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo"><h2 class="cartao-titulo">Imagem</h2></div>
    <div class="cartao-corpo">
      <?php if ($imagem): ?>
        <div style="margin-bottom:16px">
          <img src="<?= e(upload_url($imagem)) ?>" alt="" style="max-width:280px;border:1px solid var(--linha)">
        </div>
        <label style="margin-bottom:16px;display:block">
          <input type="checkbox" name="remover_imagem" value="1"> Remover imagem actual
        </label>
      <?php endif; ?>
      <div class="campo">
        <label for="imagem">Substituir imagem</label>
        <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp" data-preview="#preview">
        <div id="preview" style="margin-top:12px"></div>
        <small>JPG, PNG ou WebP · Máx. 5 MB</small>
      </div>
      <div class="campo">
        <label>Legenda</label>
        <input type="text" name="settings[sobre_legenda]" value="<?= e($s['sobre_legenda'] ?? '') ?>">
      </div>
    </div>
  </section>

  <div class="form-acoes">
    <button type="submit" class="botao botao-verde" style="width:auto">Guardar</button>
  </div>
</form>

<?php require __DIR__ . '/inc/footer.php'; ?>
