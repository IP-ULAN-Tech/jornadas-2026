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
<meta name="description" content="Submeta o seu trabalho científico para as Jornadas Técnico-Científicas do IPS 2026.">
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
        <img class="marca-logo"
             src="<?= e(upload_url($s['site_logo'])) ?>"
             alt="<?= e($s['evento_organizacao']) ?>">
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
      Preencha o formulário abaixo. As submissões são registadas pela comissão
      organizadora das Jornadas Técnico-Científicas do IPS — Edição 2026.
    </p>

    <div class="submeter-prazos">
      <div class="submeter-prazo">
        <span class="submeter-prazo-data">15 de Outubro</span>
        <span class="submeter-prazo-texto">Prazo de submissão</span>
      </div>
      <div class="submeter-prazo">
        <span class="submeter-prazo-data">3 de Novembro</span>
        <span class="submeter-prazo-texto">Notificação de aceitação</span>
      </div>
      <div class="submeter-prazo">
        <span class="submeter-prazo-data">8 de Novembro</span>
        <span class="submeter-prazo-texto">Entrega das versões finais</span>
      </div>
    </div>
  </div>
</section>

<section class="submeter-secao">
  <div class="limite">
    <div class="submeter-caixa">

      <div class="submeter-cartao">

        <div class="submeter-cartao-topo">
          <p class="submeter-cartao-rotulo">Formulário de submissão</p>
          <h2 class="submeter-cartao-titulo">Dados do trabalho</h2>
        </div>

        <form id="formSubmissao" novalidate>

          <div class="form-linha">
            <div class="form-campo">
              <label for="tipo">Tipo de submissão <span class="obrigatorio">*</span></label>
              <select id="tipo" name="tipo" required>
                <option value="">Selecione...</option>
                <option value="comunicacao">Comunicação Oral</option>
                <option value="poster">Poster Científico</option>
                <option value="projeto_inova">Projecto INOVA IPS 2026</option>
              </select>
            </div>

            <div class="form-campo" id="campoEixo">
              <label for="eixo">Eixo temático</label>
              <select id="eixo" name="eixo">
                <option value="">Selecione...</option>
                <?php foreach ($eixos as $e): ?>
                  <option value="<?= e($e['numero']) ?>"><?= e($e['numero']) ?> · <?= e($e['titulo']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-campo">
            <label for="titulo">Título do trabalho <span class="obrigatorio">*</span></label>
            <input type="text" id="titulo" name="titulo" required maxlength="255"
                   placeholder="Ex.: Sistema de irrigação automática para pequenas hortas">
          </div>

          <div class="form-campo">
            <label for="autores">Autores <span class="obrigatorio">*</span></label>
            <input type="text" id="autores" name="autores" required
                   placeholder="Nome do autor principal; Nome do coautor 1; Nome do coautor 2">
            <small>Separe os nomes por ponto e vírgula (;)</small>
          </div>

          <div class="form-linha">
            <div class="form-campo">
              <label for="instituicao">Instituição</label>
              <input type="text" id="instituicao" name="instituicao"
                     placeholder="Instituto Politécnico de Saurimo">
            </div>

            <div class="form-campo">
              <label for="email">Email de contacto <span class="obrigatorio">*</span></label>
              <input type="email" id="email" name="email" required
                     placeholder="email@exemplo.com">
            </div>
          </div>

          <div class="form-campo">
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" placeholder="+244 9XX XXX XXX">
          </div>

          <div class="form-campo">
            <label for="resumo">Resumo <span class="obrigatorio">*</span></label>
            <textarea id="resumo" name="resumo" rows="8" required maxlength="2000"
                      placeholder="Descreva o objectivo, metodologia, resultados e conclusões do trabalho."></textarea>
            <small><span id="contadorResumo">0</span> / 2000 caracteres · Mínimo 50</small>
          </div>

          <div class="form-campo">
            <label for="palavras_chave">Palavras-chave</label>
            <input type="text" id="palavras_chave" name="palavras_chave"
                   placeholder="Separadas por vírgula">
          </div>

          <div id="camposInova" hidden>
            <div class="submeter-cartao-interno">
              <p class="submeter-interno-titulo">Informação adicional — Projecto INOVA IPS</p>

              <div class="form-campo">
                <label for="area_inova">Área de inovação <span class="obrigatorio">*</span></label>
                <select id="area_inova" name="area_inova">
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
                <label for="elementos_equipa">Elementos da equipa <span class="obrigatorio">*</span></label>
                <textarea id="elementos_equipa" name="elementos_equipa" rows="3"
                          placeholder="Um nome por linha. Máximo 4 elementos."></textarea>
              </div>
            </div>
          </div>

          <div class="form-campo">
            <label for="ficheiro">Ficheiro PDF</label>
            <div class="ficheiro-caixa">
              <input type="file" id="ficheiro" name="ficheiro" accept=".pdf,application/pdf">
              <p class="ficheiro-info">Máximo 10 MB · Apenas ficheiros PDF</p>
            </div>
            <small>Opcional para comunicação e poster. Recomendado para projectos INOVA.</small>
          </div>

          <div class="submeter-acoes">
            <button type="submit" class="submeter-botao" id="botaoSubmeter">
              <span>Submeter Trabalho</span>
            </button>
            <p class="submeter-nota">
              Ao submeter, receberá um número de registo por email.
              Guarde-o para consultar o estado da submissão mais tarde.
            </p>
          </div>

        </form>
      </div>

      <div class="submeter-resultado" id="resultadoSubmissao" hidden></div>

      <div class="submeter-ajuda">
        <p><strong>Antes de submeter, confirme:</strong></p>
        <ul>
          <li>O resumo tem entre 50 e 2000 caracteres.</li>
          <li>O email indicado é o que vai usar para consultar o estado.</li>
          <li>O ficheiro PDF está bem formatado (se aplicável).</li>
        </ul>
        <p style="margin-top:16px">
          Problemas técnicos? Contacte a comissão organizadora das Jornadas.
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
      <p class="rodape-rotulo">Tema</p>
      <p class="rodape-texto">"<?= e($s['evento_tema']) ?>"</p>
      <p class="rodape-rotulo">Lema</p>
      <p class="rodape-texto">"<?= e($s['evento_lema']) ?>"</p>
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

<script>
(function () {
  'use strict';

  var form = document.getElementById('formSubmissao');
  if (form) {
    var tipo = document.getElementById('tipo');
    var campoEixo = document.getElementById('campoEixo');
    var camposInova = document.getElementById('camposInova');
    var areaInova = document.getElementById('area_inova');
    var equipa = document.getElementById('elementos_equipa');
    var resumo = document.getElementById('resumo');
    var contador = document.getElementById('contadorResumo');
    var botao = document.getElementById('botaoSubmeter');
    var resultado = document.getElementById('resultadoSubmissao');

    tipo.addEventListener('change', function () {
      var eInova = tipo.value === 'projeto_inova';
      camposInova.hidden = !eInova;
      if (campoEixo) campoEixo.style.opacity = eInova ? 0.5 : 1;
      if (areaInova) areaInova.required = eInova;
      if (equipa) equipa.required = eInova;
    });

    resumo.addEventListener('input', function () {
      var n = resumo.value.length;
      contador.textContent = n;
      contador.style.color = n > 2000 ? '#E5484D' : '';
    });

    form.addEventListener('submit', async function (evento) {
      evento.preventDefault();

      resultado.hidden = true;
      resultado.classList.remove('erro');
      resultado.innerHTML = '';

      botao.disabled = true;
      botao.querySelector('span').textContent = 'A enviar...';

      try {
        var resposta = await fetch('submeter-api.php', {
          method: 'POST',
          body: new FormData(form)
        });

        var dados = await resposta.json();

        if (!resposta.ok) {
          throw new Error(dados.erro || 'Não foi possível submeter.');
        }

        resultado.innerHTML =
          '<h2>Submissão recebida com sucesso</h2>' +
          '<p class="resultado-numero">#' + dados.id + '</p>' +
          '<p>O seu trabalho foi registado nas Jornadas Técnico-Científicas do IPS — Edição 2026. ' +
          '<strong>Guarde o número de registo</strong> — vai precisar dele para consultar o estado da submissão.</p>' +
          '<p>Foi enviada uma confirmação para <strong>' + escapar(form.email.value) + '</strong>. ' +
          'Se não receber em alguns minutos, verifique a pasta de spam.</p>' +
          '<p class="resultado-rodape">A comissão organizadora irá analisar o trabalho e comunicará a decisão por email, ' +
          'no prazo indicado (até 3 de Novembro de 2026).</p>' +
          '<div class="resultado-acoes">' +
            '<a href="consultar.php" class="resultado-botao">Consultar estado da submissão</a>' +
            '<a href="submeter.php" class="resultado-botao secundario">Submeter outro trabalho</a>' +
          '</div>';

        resultado.hidden = false;
        resultado.scrollIntoView({ behavior: 'smooth', block: 'center' });

        form.reset();
        contador.textContent = '0';
        camposInova.hidden = true;

      } catch (erro) {
        resultado.innerHTML =
          '<h2>Não foi possível concluir a submissão</h2>' +
          '<p>' + escapar(erro.message || 'Erro desconhecido.') + '</p>' +
          '<p class="resultado-rodape">Verifique os dados introduzidos e tente novamente. ' +
          'Se o problema persistir, contacte a comissão organizadora.</p>';

        resultado.classList.add('erro');
        resultado.hidden = false;
        resultado.scrollIntoView({ behavior: 'smooth', block: 'center' });

      } finally {
        botao.disabled = false;
        botao.querySelector('span').textContent = 'Submeter Trabalho';
      }
    });

    function escapar(txt) {
      var d = document.createElement('div');
      d.textContent = txt == null ? '' : String(txt);
      return d.innerHTML;
    }
  }

  // Rede molecular de fundo — cobre toda a página
  var rede = document.getElementById('redeFundo');
  if (rede) {
    var L = 1600;
    var A = 1000;
    var total = 32;
    var pontos = [];
    var i;

    for (i = 0; i < total; i++) {
      pontos.push({
        x: Math.random() * L,
        y: Math.random() * A,
        vx: (Math.random() - 0.5) * 0.4,
        vy: (Math.random() - 0.5) * 0.4,
        r: 1.5 + Math.random() * 1.8
      });
    }

    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', '0 0 ' + L + ' ' + A);
    svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');

    var gLinhas = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    var gPontos = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    svg.appendChild(gLinhas);
    svg.appendChild(gPontos);

    var linhas = [];
    var circulos = [];

    for (i = 0; i < total; i++) {
      var c = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
      c.setAttribute('r', pontos[i].r);
      c.setAttribute('fill', '#0A1F44');
      c.setAttribute('fill-opacity', '0.22');
      gPontos.appendChild(c);
      circulos.push(c);
    }

    for (i = 0; i < total; i++) {
      for (var j = i + 1; j < total; j++) {
        var l = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        l.setAttribute('stroke', '#00A5C4');
        l.setAttribute('stroke-opacity', '0.0');
        l.setAttribute('stroke-width', '1');
        gLinhas.appendChild(l);
        linhas.push({ i: i, j: j, el: l });
      }
    }

    rede.appendChild(svg);

    var reduzido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function desenhar() {
      for (var k = 0; k < total; k++) {
        var p = pontos[k];
        if (!reduzido) {
          p.x += p.vx;
          p.y += p.vy;
          if (p.x < 0 || p.x > L) p.vx *= -1;
          if (p.y < 0 || p.y > A) p.vy *= -1;
        }
        circulos[k].setAttribute('cx', p.x);
        circulos[k].setAttribute('cy', p.y);
      }

      for (var m = 0; m < linhas.length; m++) {
        var a = pontos[linhas[m].i];
        var b = pontos[linhas[m].j];
        var dx = a.x - b.x;
        var dy = a.y - b.y;
        var dist = Math.sqrt(dx * dx + dy * dy);
        var opacidade = dist < 220 ? (1 - dist / 220) * 0.22 : 0;

        linhas[m].el.setAttribute('x1', a.x);
        linhas[m].el.setAttribute('y1', a.y);
        linhas[m].el.setAttribute('x2', b.x);
        linhas[m].el.setAttribute('y2', b.y);
        linhas[m].el.setAttribute('stroke-opacity', opacidade.toFixed(3));
      }

      if (!reduzido) requestAnimationFrame(desenhar);
    }

    desenhar();
  }

  var cabecalho = document.getElementById('cabecalho');
  if (cabecalho) {
    window.addEventListener('scroll', function () {
      cabecalho.classList.toggle('rolado', window.scrollY > 12);
    }, { passive: true });
  }

  var botaoMenu = document.getElementById('menuToggle');
  var menu = document.querySelector('.menu');
  if (botaoMenu && menu) {
    botaoMenu.addEventListener('click', function () {
      var aberto = menu.classList.toggle('aberto');
      botaoMenu.classList.toggle('aberto', aberto);
    });
  }

})();
</script>

</body>
</html>