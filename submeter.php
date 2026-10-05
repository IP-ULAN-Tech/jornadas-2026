<?php
require_once __DIR__ . '/inc/helpers.php';
$s = settings();
$eixos = db()->query('SELECT * FROM eixos WHERE ativo = 1 ORDER BY ordem, id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submeter Trabalho · <?= e($s['evento_titulo_1'] . ' ' . $s['evento_titulo_2']) ?></title>
<meta name="description" content="Submissão de trabalhos científicos e projectos de inovação para as Jornadas Técnico-Científicas do IPS 2026.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/site.css">
<link rel="stylesheet" href="assets/css/submissao.css">
</head>
<body class="pagina-submeter">

<div class="regua-topo" aria-hidden="true"></div>

<header class="cabecalho" id="cabecalho">
  <div class="limite cabecalho-interno">
    <a href="index.php" class="marca">
      <?php if (!empty($s['site_logo']) && imagem_existe($s['site_logo'])): ?>
        <img class="marca-logo" src="<?= e(upload_url($s['site_logo'])) ?>" alt="<?= e($s['evento_organizacao']) ?>">
      <?php else: ?>
        <div class="marca-fallback" aria-hidden="true"><span>IPS</span></div>
      <?php endif; ?>
      <div class="marca-texto">
        <span class="marca-inst"><?= e($s['evento_organizacao']) ?></span>
        <span class="marca-evento">Jornadas Técnico-Científicas · 2026</span>
      </div>
    </a>

    <nav class="menu" aria-label="Menu principal">
      <a href="index.php#evento">O Evento</a>
      <a href="index.php#eixos">Eixos</a>
      <a href="index.php#programa">Programa</a>
      <a href="index.php#inova">INOVA IPS</a>
      <a href="consultar.php">Consultar</a>
    </nav>

    <a href="submeter.php" class="bt bt-verde cabecalho-bt">Submeter Trabalho</a>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<div class="submeter-fundo-rede" id="redeFundo" aria-hidden="true"></div>

<main class="submeter-conteudo">

<section class="submeter-hero">
  <div class="limite">
    <p class="submeter-rotulo">Submissão de Trabalhos</p>
    <h1 class="submeter-titulo">Submeta o seu trabalho</h1>
    <p class="submeter-sub">
      Escolha a modalidade adequada ao seu trabalho. As Jornadas Científicas
      e a Feira de Inovação Tecnológica INOVA IPS 2026 têm formulários distintos.
    </p>

    <div class="submeter-prazos">
      <div class="submeter-prazo">
        <span class="submeter-prazo-data">23 de Outubro</span>
        <span class="submeter-prazo-texto">Prazo de submissão</span>
      </div>
      <div class="submeter-prazo">
        <span class="submeter-prazo-data">3 de Novembro</span>
        <span class="submeter-prazo-texto">Notificação de aceitação</span>
      </div>
      <div class="submeter-prazo">
        <span class="submeter-prazo-data">9 de Novembro</span>
        <span class="submeter-prazo-texto">Entrega das versões finais</span>
      </div>
    </div>
  </div>
</section>

<section class="submeter-secao">
  <div class="limite">
    <div class="submeter-caixa">

      <div class="submeter-separadores" role="tablist">
        <button type="button" class="submeter-separador ativo" data-form="jornadas" role="tab" aria-selected="true">
          <span class="separador-rotulo">Formulário</span>
          <span class="separador-titulo">Jornadas Científicas</span>
          <span class="separador-desc">Comunicação oral ou póster científico</span>
        </button>

        <button type="button" class="submeter-separador" data-form="inova" role="tab" aria-selected="false">
          <span class="separador-rotulo">Formulário</span>
          <span class="separador-titulo">INOVA IPS 2026</span>
          <span class="separador-desc">Projecto de inovação tecnológica</span>
        </button>
      </div>

      <div class="submeter-cartao" id="cartaoJornadas">
        <div class="submeter-cartao-topo">
          <p class="submeter-cartao-rotulo">Jornadas Científicas</p>
          <h2 class="submeter-cartao-titulo">Submissão de trabalho científico</h2>
        </div>

        <form id="formJornadas" novalidate>
          <input type="hidden" name="formulario" value="jornadas">

          <div class="form-linha">
            <div class="form-campo">
              <label for="tipo">Modalidade <span class="obrigatorio">*</span></label>
              <select id="tipo" name="tipo" required>
                <option value="">Selecione...</option>
                <option value="comunicacao">Comunicação Oral</option>
                <option value="poster">Póster Científico</option>
              </select>
            </div>

            <div class="form-campo">
              <label for="eixo">Eixo temático <span class="obrigatorio">*</span></label>
              <select id="eixo" name="eixo" required>
                <option value="">Selecione...</option>
                <?php foreach ($eixos as $e): ?>
                  <option value="<?= e($e['numero']) ?>"><?= e($e['numero']) ?> · <?= e($e['titulo']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-campo">
            <label for="titulo">Título do trabalho <span class="obrigatorio">*</span></label>
            <input type="text" id="titulo" name="titulo" required maxlength="255">
          </div>

          <div class="form-campo">
            <label for="autores">Autores <span class="obrigatorio">*</span></label>
            <input type="text" id="autores" name="autores" required placeholder="Nome do autor principal; Nome do coautor 1">
            <small>Separe os nomes por ponto e vírgula (;)</small>
          </div>

          <div class="form-linha">
            <div class="form-campo">
              <label for="instituicao">Instituição</label>
              <input type="text" id="instituicao" name="instituicao" placeholder="Instituto Politécnico de Saurimo">
            </div>
            <div class="form-campo">
              <label for="email">Email de contacto <span class="obrigatorio">*</span></label>
              <input type="email" id="email" name="email" required>
            </div>
          </div>

          <div class="form-campo">
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" placeholder="+244 9XX XXX XXX">
          </div>

          <div class="form-campo">
            <label for="resumo">Resumo <span class="obrigatorio">*</span></label>
            <textarea id="resumo" name="resumo" rows="8" required maxlength="2000" placeholder="Descreva o objectivo, metodologia, resultados e conclusões."></textarea>
            <small><span id="contadorResumo">0</span> / 2000 caracteres · Mínimo 50</small>
          </div>

          <div class="form-campo">
            <label for="palavras_chave">Palavras-chave</label>
            <input type="text" id="palavras_chave" name="palavras_chave" placeholder="Separadas por vírgula">
          </div>

          <div class="form-campo">
            <label for="ficheiro">Ficheiro PDF (máx. 10 MB)</label>
            <div class="ficheiro-caixa">
              <input type="file" id="ficheiro" name="ficheiro" accept=".pdf,application/pdf">
              <p class="ficheiro-info">Recomendado para submissão final. Opcional nesta fase.</p>
            </div>
          </div>

          <div class="submeter-acoes">
            <button type="submit" class="submeter-botao" id="botaoJornadas">
              <span>Submeter trabalho às Jornadas</span>
            </button>
            <p class="submeter-nota">Receberá um número de registo por email. Guarde-o para consultar o estado da submissão.</p>
          </div>

          <div class="form-mensagem" id="mensagemJornadas" hidden></div>
        </form>
      </div>

      <div class="submeter-cartao" id="cartaoInova" hidden>
        <div class="submeter-cartao-topo">
          <p class="submeter-cartao-rotulo">INOVA IPS 2026</p>
          <h2 class="submeter-cartao-titulo">Inscrição de projecto de inovação</h2>
        </div>

        <form id="formInova" novalidate>
          <input type="hidden" name="formulario" value="inova">

          <div class="form-campo">
            <label for="titulo_proj">Título do projecto <span class="obrigatorio">*</span></label>
            <input type="text" id="titulo_proj" name="titulo" required maxlength="255">
          </div>

          <div class="form-campo">
            <label for="cursos">Curso(s) envolvido(s) <span class="obrigatorio">*</span></label>
            <input type="text" id="cursos" name="cursos" required maxlength="255" placeholder="Ex.: Engenharia Informática; Engenharia Electromecânica">
          </div>

          <div class="form-campo">
            <label for="membros">Membros do grupo <span class="obrigatorio">*</span></label>
            <textarea id="membros" name="membros" rows="4" required placeholder="Um nome por linha. Máximo 4 elementos."></textarea>
            <small>Individualmente ou em grupos de até 4 elementos.</small>
          </div>

          <div class="form-campo">
            <label for="supervisor">Docente supervisor <span class="obrigatorio">*</span></label>
            <input type="text" id="supervisor" name="supervisor" required maxlength="150">
          </div>

          <div class="form-linha">
            <div class="form-campo">
              <label for="email_inova">Email de contacto <span class="obrigatorio">*</span></label>
              <input type="email" id="email_inova" name="email" required>
            </div>
            <div class="form-campo">
              <label for="telefone_inova">Telefone</label>
              <input type="tel" id="telefone_inova" name="telefone" placeholder="+244 9XX XXX XXX">
            </div>
          </div>

          <div class="form-campo">
            <label for="area_inova">Área de inovação <span class="obrigatorio">*</span></label>
            <select id="area_inova" name="area_inova" required>
              <option value="">Selecione...</option>
              <option value="saude">Saúde</option>
              <option value="educacao">Educação</option>
              <option value="mineracao">Mineração</option>
              <option value="construcao">Construção</option>
              <option value="tecnologia">Tecnologia</option>
              <option value="outra">Outra</option>
            </select>
          </div>

          <div class="form-campo">
            <label for="resumo_inova">Resumo do projecto <span class="obrigatorio">*</span></label>
            <textarea id="resumo_inova" name="resumo" rows="6" required maxlength="2000" placeholder="Descreva o projecto em até 300 palavras."></textarea>
            <small><span id="contadorResumoInova">0</span> / 2000 caracteres</small>
          </div>

          <div class="form-campo">
            <label for="ficheiro_inova">Documento PDF do projecto (opcional)</label>
            <div class="ficheiro-caixa">
              <input type="file" id="ficheiro_inova" name="ficheiro" accept=".pdf,application/pdf">
              <p class="ficheiro-info">Máx. 10 MB · Apenas PDF</p>
            </div>
          </div>

          <div class="submeter-acoes">
            <button type="submit" class="submeter-botao" id="botaoInova">
              <span>Inscrever projecto na INOVA IPS</span>
            </button>
            <p class="submeter-nota">Receberá confirmação por email. A comissão entrará em contacto com o docente supervisor.</p>
          </div>

          <div class="form-mensagem" id="mensagemInova" hidden></div>
        </form>
      </div>

      <div class="submeter-ajuda">
        <p><strong>Antes de submeter, confirme:</strong></p>
        <ul>
          <li>O resumo tem entre 50 e 2000 caracteres.</li>
          <li>O email indicado é o que vai usar para consultar o estado.</li>
          <li>Para projectos INOVA: grupos até 4 elementos e docente supervisor identificado.</li>
        </ul>
        <p style="margin-top:16px">
          Dúvidas? Contacte <a href="mailto:<?= e($s['email_institucional'] ?? 'jornadascientificas@ip-ulan.ao') ?>"><?= e($s['email_institucional'] ?? 'jornadascientificas@ip-ulan.ao') ?></a>
          ou aceda ao webmail em
          <a href="<?= e($s['webmail_url'] ?? 'https://webmail.ip-ulan.ao') ?>" target="_blank" rel="noopener"><?= e($s['webmail_url'] ?? 'https://webmail.ip-ulan.ao') ?></a>.
        </p>
      </div>

    </div>
  </div>
</section>

</main>

<footer class="rodape">
  <div class="rodape-regua" aria-hidden="true"></div>
  <div class="limite rodape-interno">
    <div class="rodape-coluna">
      <p class="rodape-inst"><?= e($s['evento_instituicao']) ?></p>
      <p class="rodape-unidade"><?= e($s['evento_organizacao']) ?></p>
      <p class="rodape-evento">Jornadas Técnico-Científicas — <?= e($s['evento_edicao']) ?></p>
      <p class="rodape-evento">INOVA IPS 2026</p>
    </div>
    <div class="rodape-coluna">
      <p class="rodape-rotulo">Contactos</p>
      <p class="rodape-texto">
        <a href="mailto:<?= e($s['email_institucional'] ?? 'jornadascientificas@ip-ulan.ao') ?>">
          <?= e($s['email_institucional'] ?? 'jornadascientificas@ip-ulan.ao') ?>
        </a><br>
        <a href="<?= e($s['webmail_url'] ?? 'https://webmail.ip-ulan.ao') ?>" target="_blank" rel="noopener">Webmail institucional</a>
      </p>
    </div>
    <div class="rodape-coluna">
      <p class="rodape-rotulo">Navegação</p>
      <ul>
        <li><a href="index.php#inicio">Início</a></li>
        <li><a href="index.php#evento">O Evento</a></li>
        <li><a href="index.php#programa">Programa</a></li>
        <li><a href="consultar.php">Consultar submissão</a></li>
      </ul>
    </div>
  </div>
  <div class="limite rodape-base">
    <p class="rodape-lema"><?= e($s['rodape_lema']) ?></p>
    <p class="rodape-copy">© 2026 <?= e($s['evento_organizacao']) ?> · <?= e($s['evento_instituicao']) ?></p>
  </div>
</footer>

<script src="assets/js/redes.js"></script>
<script>
(function () {
  'use strict';

  var separadores = document.querySelectorAll('.submeter-separador');
  var cartaoJornadas = document.getElementById('cartaoJornadas');
  var cartaoInova = document.getElementById('cartaoInova');

  separadores.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var alvo = btn.dataset.form;
      separadores.forEach(function (b) {
        b.classList.toggle('ativo', b === btn);
        b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
      });
      cartaoJornadas.hidden = alvo !== 'jornadas';
      cartaoInova.hidden = alvo !== 'inova';
    });
  });

  function ligarFormulario(idForm, idBotao, idMensagem, tipo) {
    var form = document.getElementById(idForm);
    if (!form) return;

    var botao = document.getElementById(idBotao);
    var mensagem = document.getElementById(idMensagem);

    form.addEventListener('submit', async function (ev) {
      ev.preventDefault();
      mensagem.hidden = true;
      mensagem.classList.remove('erro');
      mensagem.innerHTML = '';
      botao.disabled = true;
      var span = botao.querySelector('span');
      var textoOriginal = span.textContent;
      span.textContent = 'A enviar...';

      try {
        var resposta = await fetch('submeter-api.php', {
          method: 'POST',
          body: new FormData(form)
        });

        var dados = await resposta.json();
        if (!resposta.ok) throw new Error(dados.erro || 'Não foi possível submeter.');

        mensagem.innerHTML =
          '<h3>Submissão recebida</h3>' +
          '<p class="resultado-numero">#' + dados.id + '</p>' +
          '<p>O seu ' + (tipo === 'inova' ? 'projecto' : 'trabalho') + ' foi registado com sucesso.</p>' +
          '<p>Foi enviada uma confirmação para <strong>' + escapar(form.email.value) + '</strong>. Verifique também a pasta de spam.</p>' +
          '<p class="resultado-rodape">Guarde o número de registo. Pode consultar o estado em <a href="consultar.php">consultar.php</a>.</p>';
        mensagem.hidden = false;
        mensagem.scrollIntoView({ behavior: 'smooth', block: 'center' });

        form.reset();

      } catch (erro) {
        mensagem.textContent = erro.message;
        mensagem.classList.add('erro');
        mensagem.hidden = false;

      } finally {
        botao.disabled = false;
        span.textContent = textoOriginal;
      }
    });
  }

  function escapar(txt) {
    var d = document.createElement('div');
    d.textContent = txt == null ? '' : String(txt);
    return d.innerHTML;
  }

  // Contadores de resumo
  var resumoJ = document.getElementById('resumo');
  var contJ = document.getElementById('contadorResumo');
  if (resumoJ && contJ) {
    resumoJ.addEventListener('input', function () {
      contJ.textContent = resumoJ.value.length;
    });
  }

  var resumoI = document.getElementById('resumo_inova');
  var contI = document.getElementById('contadorResumoInova');
  if (resumoI && contI) {
    resumoI.addEventListener('input', function () {
      contI.textContent = resumoI.value.length;
    });
  }

  ligarFormulario('formJornadas', 'botaoJornadas', 'mensagemJornadas', 'jornadas');
  ligarFormulario('formInova', 'botaoInova', 'mensagemInova', 'inova');

})();
</script>
</body>
</html>