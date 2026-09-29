<?php
require_once __DIR__ . '/inc/helpers.php';
$s = settings();
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Consultar Submissão · <?= e($s['evento_titulo_1'] . ' ' . $s['evento_titulo_2']) ?></title>
<meta name="description" content="Consultar o estado de uma submissão às Jornadas Técnico-Científicas do IPS 2026.">
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/site.css">
<link rel="stylesheet" href="assets/css/submissao.css">
<style>
.consultar-hero {
  background: var(--navy-900);
  color: var(--branco);
  padding: 140px 0 80px;
  position: relative;
  overflow: hidden;
}
.consultar-hero-grade {
  position: absolute; inset: 0;
  background:
    radial-gradient(ellipse 65% 55% at 20% 30%, rgba(30,78,140,0.5), transparent 60%),
    radial-gradient(ellipse 55% 45% at 85% 75%, rgba(0,165,196,0.15), transparent 60%);
  pointer-events: none;
}
.consultar-hero-conteudo { position: relative; z-index: 1; }
.consultar-rotulo {
  font-size: 13px;
  letter-spacing: .22em;
  text-transform: uppercase;
  font-weight: 600;
  color: var(--verde-400);
  margin-bottom: 20px;
}
.consultar-titulo {
  font-family: var(--font-titulo);
  font-weight: 700;
  font-size: clamp(36px, 5vw, 56px);
  line-height: 1.05;
  letter-spacing: -0.02em;
  margin-bottom: 24px;
}
.consultar-sub {
  font-size: 19px;
  color: rgba(255,255,255,0.8);
  line-height: 1.65;
  max-width: 640px;
}
.consultar-secao {
  padding: 80px 0 120px;
  background: var(--branco);
}
.consultar-caixa {
  max-width: 720px;
  margin: 0 auto;
}
.consultar-cartao {
  background: var(--branco);
  border: 1px solid var(--linha);
  padding: 48px 44px;
}
.consultar-cartao-titulo {
  font-family: var(--font-titulo);
  font-weight: 600;
  font-size: 22px;
  color: var(--navy-900);
  letter-spacing: -0.01em;
  margin-bottom: 12px;
}
.consultar-cartao-nota {
  font-size: 15.5px;
  color: var(--tinta-500);
  line-height: 1.6;
  margin-bottom: 32px;
}
.consultar-campos {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 20px;
  margin-bottom: 28px;
}
.consultar-campo label {
  display: block;
  font-size: 12px;
  letter-spacing: .14em;
  text-transform: uppercase;
  font-weight: 600;
  color: var(--tinta-700);
  margin-bottom: 8px;
}
.consultar-campo input {
  width: 100%;
  padding: 14px 16px;
  border: 1px solid var(--linha);
  border-radius: 2px;
  font-family: var(--font-texto);
  font-size: 16px;
  background: var(--branco);
  color: var(--tinta-900);
  transition: border-color .2s var(--curva);
}
.consultar-campo input:focus {
  outline: none;
  border-color: var(--navy-800);
}
.consultar-botao {
  width: 100%;
  padding: 16px;
  background: var(--navy-800);
  color: var(--branco);
  border: 0;
  border-radius: 2px;
  font-family: var(--font-texto);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: .08em;
  text-transform: uppercase;
  cursor: pointer;
  transition: background .2s var(--curva);
}
.consultar-botao:hover { background: var(--navy-900); }
.consultar-botao:disabled { opacity: 0.6; cursor: wait; }
.consultar-mensagem {
  margin-top: 24px;
  padding: 20px 24px;
  font-size: 15px;
  line-height: 1.6;
  border-left: 3px solid var(--cyan-500);
  background: rgba(0,165,196,0.06);
  color: var(--tinta-900);
}
.consultar-mensagem.erro {
  border-color: #E5484D;
  background: rgba(229,72,77,0.08);
  color: #8E2318;
}

/* Resultado */
.consultar-resultado {
  margin-top: 40px;
  background: var(--branco);
  border: 1px solid var(--linha);
  padding: 44px 40px;
  animation: aparecer .5s var(--curva);
}
@keyframes aparecer {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}
.consultar-estado {
  display: inline-block;
  padding: 6px 16px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: .14em;
  text-transform: uppercase;
  border-radius: 2px;
  margin-bottom: 20px;
}
.consultar-estado.pendente    { background: #F1F3F5; color: #4A5568; }
.consultar-estado.em_analise  { background: #E6F0FA; color: #1E4E8C; }
.consultar-estado.aceite      { background: #E5F5E0; color: #2D6A2F; }
.consultar-estado.rejeitado   { background: #FBE7E8; color: #9F2A2D; }

.consultar-titulo-sub {
  font-family: var(--font-titulo);
  font-weight: 600;
  font-size: 24px;
  letter-spacing: -0.015em;
  line-height: 1.25;
  color: var(--navy-900);
  margin-bottom: 24px;
}
.consultar-dados {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px 32px;
  padding: 24px 0;
  border-top: 1px solid var(--linha);
  border-bottom: 1px solid var(--linha);
  margin-bottom: 24px;
}
.consultar-dados dt {
  font-size: 11px;
  letter-spacing: .14em;
  text-transform: uppercase;
  color: var(--tinta-500);
  font-weight: 600;
  margin-bottom: 6px;
}
.consultar-dados dd {
  font-size: 15.5px;
  color: var(--tinta-900);
  line-height: 1.5;
}
.consultar-texto {
  font-size: 15.5px;
  line-height: 1.7;
  color: var(--tinta-700);
  margin-bottom: 20px;
}
.consultar-texto strong {
  display: block;
  font-size: 13px;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--tinta-500);
  font-weight: 600;
  margin-bottom: 8px;
}
.consultar-resumo {
  padding: 20px 24px;
  background: var(--papel);
  border-left: 3px solid var(--navy-800);
  font-size: 15.5px;
  line-height: 1.7;
  color: var(--tinta-700);
  white-space: pre-wrap;
  margin-bottom: 24px;
}
.consultar-rodape-nota {
  font-size: 14.5px;
  line-height: 1.65;
  color: var(--tinta-500);
  padding-top: 20px;
  border-top: 1px solid var(--linha);
}
.consultar-ajuda {
  margin-top: 32px;
  padding: 24px;
  background: var(--papel);
  border-left: 3px solid var(--verde-400);
  font-size: 15px;
  line-height: 1.7;
  color: var(--tinta-700);
}
.consultar-ajuda strong { color: var(--navy-900); }

@media (max-width: 700px) {
  .consultar-hero { padding: 100px 0 60px; }
  .consultar-cartao,
  .consultar-resultado { padding: 32px 24px; }
  .consultar-campos { grid-template-columns: 1fr; }
  .consultar-dados { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

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
      <a href="index.php#submissoes">Submissões</a>
      <a href="consultar.php" style="color:var(--navy-800);font-weight:600">Consultar</a>
    </nav>

    <a href="index.php#submissoes" class="bt bt-verde cabecalho-bt">Submeter Trabalho</a>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<main>

<section class="consultar-hero">
  <div class="consultar-hero-grade" aria-hidden="true"></div>
  <div class="limite consultar-hero-conteudo">
    <p class="consultar-rotulo">Consulta pública</p>
    <h1 class="consultar-titulo">Consultar Submissão</h1>
    <p class="consultar-sub">
      Introduza o número de registo que recebeu ao submeter o trabalho e o email
      indicado nessa altura. Verá o estado actual da sua submissão.
    </p>
  </div>
</section>

<section class="consultar-secao">
  <div class="limite">
    <div class="consultar-caixa">

      <div class="consultar-cartao">
        <h2 class="consultar-cartao-titulo">Dados da submissão</h2>
        <p class="consultar-cartao-nota">
          Ambos os campos são obrigatórios. O email tem de ser exactamente o mesmo que
          usou quando submeteu o trabalho.
        </p>

        <form id="formConsulta" novalidate>
          <div class="consultar-campos">
            <div class="consultar-campo">
              <label for="numero">Número de registo</label>
              <input type="text" id="numero" name="numero" inputmode="numeric" pattern="[0-9]*"
                     placeholder="Ex.: 42" required maxlength="10">
            </div>
            <div class="consultar-campo">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" required
                     placeholder="email@exemplo.com" maxlength="190">
            </div>
          </div>

          <button type="submit" class="consultar-botao" id="botaoConsultar">
            Consultar estado
          </button>

          <div class="consultar-mensagem" id="mensagemConsulta" hidden></div>
        </form>
      </div>

      <div class="consultar-resultado" id="resultadoConsulta" hidden></div>

      <div class="consultar-ajuda">
        <strong>Não sabe o número de registo?</strong>
        Verifique o email de confirmação que recebeu ao submeter o trabalho.
        Se não encontrar, contacte a comissão organizadora das Jornadas
        indicando o título do trabalho.
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
        <li><a href="index.php#submissoes">Submissões</a></li>
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

  const form = document.getElementById('formConsulta');
  const botao = document.getElementById('botaoConsultar');
  const mensagem = document.getElementById('mensagemConsulta');
  const resultado = document.getElementById('resultadoConsulta');

  const rotulos = {
    pendente:   'Pendente',
    em_analise: 'Em análise',
    aceite:     'Aceite',
    rejeitado:  'Rejeitado'
  };

  const descricoes = {
    pendente:   'A submissão foi recebida e aguarda análise pela comissão científica.',
    em_analise: 'A submissão está a ser avaliada pela comissão científica.',
    aceite:     'A submissão foi aceite. Receberá instruções sobre a apresentação por email.',
    rejeitado:  'A submissão não foi aceite. Consulte o email de notificação para mais detalhes.'
  };

  function limpar() {
    mensagem.hidden = true;
    mensagem.textContent = '';
    mensagem.classList.remove('erro');
    resultado.hidden = true;
    resultado.innerHTML = '';
  }

  function escapar(txt) {
    const d = document.createElement('div');
    d.textContent = txt == null ? '' : String(txt);
    return d.innerHTML;
  }

  form.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    limpar();

    const numero = document.getElementById('numero').value.trim();
    const email = document.getElementById('email').value.trim();

    if (!numero || !email) {
      mensagem.textContent = 'Preencha o número de registo e o email.';
      mensagem.classList.add('erro');
      mensagem.hidden = false;
      return;
    }

    botao.disabled = true;
    botao.textContent = 'A consultar...';

    try {
      const resposta = await fetch('consultar-api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ numero: numero, email: email })
      });

      const dados = await resposta.json();

      if (!resposta.ok) {
        throw new Error(dados.erro || 'Não foi possível consultar.');
      }

      const s = dados.submissao;
      const estado = rotulos[s.status] || s.status;
      const classeEstado = s.status.replace(/[^a-z_]/g, '');

      resultado.innerHTML = `
        <span class="consultar-estado ${classeEstado}">${escapar(estado)}</span>
        <h2 class="consultar-titulo-sub">${escapar(s.titulo)}</h2>

        <dl class="consultar-dados">
          <div><dt>Número de registo</dt><dd>#${escapar(s.id)}</dd></div>
          <div><dt>Tipo</dt><dd>${escapar(s.tipo_nome)}</dd></div>
          <div><dt>Autor principal</dt><dd>${escapar(s.autor_principal)}</dd></div>
          <div><dt>Submetido em</dt><dd>${escapar(s.criado_em)}</dd></div>
        </dl>

        <div class="consultar-texto">
          <strong>Estado actual</strong>
          ${escapar(descricoes[s.status] || '')}
        </div>

        ${s.tem_ficheiro ? `
          <p class="consultar-texto" style="margin-bottom:0">
            <strong>Ficheiro anexado</strong>
            ${escapar(s.ficheiro_nome || 'PDF')}
          </p>
        ` : ''}

        <p class="consultar-rodape-nota">
          Esta consulta é informativa. Qualquer decisão oficial é comunicada por email
          através do endereço indicado na submissão.
        </p>
      `;
      resultado.hidden = false;
      resultado.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } catch (erro) {
      mensagem.textContent = erro.message;
      mensagem.classList.add('erro');
      mensagem.hidden = false;

    } finally {
      botao.disabled = false;
      botao.textContent = 'Consultar estado';
    }
  });

})();
</script>

<script src="assets/js/site.js"></script>
</body>
</html>