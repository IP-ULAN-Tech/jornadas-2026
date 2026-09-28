<?php
$tituloPagina = 'Local do Evento';
require __DIR__ . '/inc/header.php';

$mensagem = flash('mensagem');
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido($_POST['csrf'] ?? null)) {
    try {
        foreach ($_POST['settings'] ?? [] as $chave => $valor) {
            guardar_setting($chave, (string) $valor);
        }

        registar_log((int) $_SESSION['admin_id'], 'editar', 'local');
        flash('mensagem', 'Dados do local guardados com sucesso.');
        redirecionar('local.php');

    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}

$s = settings();
?>

<?php if ($mensagem): ?><div class="aviso aviso-sucesso"><?= e($mensagem) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="aviso aviso-erro"><?= e($erro) ?></div><?php endif; ?>

<form method="post">

  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo">Informação do Local</h2>
    </div>
    <div class="cartao-corpo">

      <div class="campo">
        <label for="local_nome">Nome do local</label>
        <input type="text" id="local_nome" name="settings[local_nome]"
               value="<?= e($s['local_nome'] ?? '') ?>"
               placeholder="Ex.: Instituto Politécnico de Saurimo"
               required>
      </div>

      <div class="form-linha">
        <div class="campo">
          <label for="local_cidade">Cidade / Província</label>
          <input type="text" id="local_cidade" name="settings[local_cidade]"
                 value="<?= e($s['local_cidade'] ?? '') ?>"
                 placeholder="Ex.: Saurimo — Lunda Sul">
        </div>
        <div class="campo">
          <label for="local_pais">País</label>
          <input type="text" id="local_pais" name="settings[local_pais]"
                 value="<?= e($s['local_pais'] ?? '') ?>"
                 placeholder="Ex.: Angola">
        </div>
      </div>

      <div class="campo">
        <label for="local_endereco">Endereço completo (opcional)</label>
        <input type="text" id="local_endereco" name="settings[local_endereco]"
               value="<?= e($s['local_endereco'] ?? '') ?>"
               placeholder="Ex.: Bairro XYZ, Rua Principal, Saurimo">
        <small>Se preenchido, aparece por baixo do nome do local.</small>
      </div>

    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo">Mapa</h2>
    </div>
    <div class="cartao-corpo">

      <div class="campo">
        <label for="local_mapa_url">URL do mapa (Google Maps embed)</label>
        <textarea id="local_mapa_url" name="settings[local_mapa_url]" rows="3"
                  placeholder="https://www.google.com/maps/embed?pb=..."><?= e($s['local_mapa_url'] ?? '') ?></textarea>
        <small>
          Vai ao <strong>Google Maps</strong> → pesquisa o local → clica em <strong>Partilhar</strong> → <strong>Incorporar um mapa</strong> → copia <strong>apenas o URL</strong> que aparece dentro de <code>src="..."</code> e cola aqui.
        </small>
      </div>

      <div class="campo">
        <label>Pré-visualização</label>
        <div class="local-mapa-preview">
          <?php if (!empty($s['local_mapa_url'])): ?>
            <iframe src="<?= e($s['local_mapa_url']) ?>"
                    width="100%" height="100%"
                    style="border:0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
          <?php else: ?>
            <div class="local-mapa-vazio">
              <span class="local-mapa-ponto"></span>
              <p><?= e($s['local_nome'] ?? 'Instituto Politécnico de Saurimo') ?></p>
              <small>Sem mapa configurado. O site mostra o bloco placeholder.</small>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </section>

  <section class="cartao">
    <div class="cartao-topo">
      <h2 class="cartao-titulo">Informação Adicional (opcional)</h2>
    </div>
    <div class="cartao-corpo">

      <div class="campo">
        <label for="local_indicacoes">Indicações de acesso</label>
        <textarea id="local_indicacoes" name="settings[local_indicacoes]" rows="3"
                  placeholder="Ex.: Entrada pelo portão principal. Estacionamento disponível no pátio interno."><?= e($s['local_indicacoes'] ?? '') ?></textarea>
        <small>Se preenchido, aparece como texto auxiliar na secção do site.</small>
      </div>

      <div class="campo">
        <label for="local_contacto">Contacto no local</label>
        <input type="text" id="local_contacto" name="settings[local_contacto]"
               value="<?= e($s['local_contacto'] ?? '') ?>"
               placeholder="Ex.: +244 923 000 000">
      </div>

    </div>
  </section>

  <div class="form-acoes" style="padding:0 0 40px">
    <button type="submit" class="botao botao-verde" style="width:auto">Guardar alterações</button>
    <a href="configuracoes.php" class="botao botao-sec" style="width:auto">Outras configurações</a>
  </div>

</form>

<?php require __DIR__ . '/inc/footer.php'; ?>