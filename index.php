<?php
require_once __DIR__ . '/inc/helpers.php';

$s = settings();

$documentos = [];
foreach (glob(__DIR__ . '/assets/pdfs/*.pdf') ?: [] as $ficheiroPdf) {
  $nomeFicheiro = basename($ficheiroPdf);
  $nomeBusca = strtolower($nomeFicheiro);
  $tituloDocumento = $nomeFicheiro;
  $descricaoDocumento = 'Documento oficial de apoio à participação nas Jornadas.';

  if (strpos($nomeBusca, 'normas') !== false) {
    $tituloDocumento = 'Normas das Jornadas';
    $descricaoDocumento = 'Consulte as regras, orientações e critérios para participar e submeter trabalhos.';
  } elseif (strpos($nomeBusca, 'resumo') !== false) {
    $tituloDocumento = 'Modelo de Resumo Alargado';
    $descricaoDocumento = 'Use este modelo para estruturar e formatar o resumo do seu trabalho.';
  } elseif (strpos($nomeBusca, 'ficha') !== false) {
    $tituloDocumento = 'Ficha de Inscrição do Projeto';
    $descricaoDocumento = 'Ficha para reunir os dados necessários à inscrição do projeto.';
  }

  $documentos[] = [
    'ficheiro' => $nomeFicheiro,
    'titulo' => $tituloDocumento,
    'descricao' => $descricaoDocumento,
    'tamanho' => filesize($ficheiroPdf),
  ];
}
usort($documentos, static function ($a, $b) {
  return strcmp($a['titulo'], $b['titulo']);
});

$slides = db()->query('SELECT * FROM slides WHERE ativo = 1 ORDER BY ordem, id')->fetchAll();

$eixos = db()->query('SELECT * FROM eixos WHERE ativo = 1 ORDER BY ordem, id')->fetchAll();
$cursosPorEixo = [];
foreach (db()->query('SELECT * FROM eixo_cursos ORDER BY ordem') as $c) {
    $cursosPorEixo[$c['eixo_id']][] = $c['nome'];
}

$dias = db()->query('SELECT * FROM programa_dias ORDER BY ordem')->fetchAll();

$inovaBlocos = db()->query('SELECT * FROM inova_blocos WHERE ativo = 1 ORDER BY ordem')->fetchAll();
$inovaCriterios = db()->query('SELECT * FROM inova_criterios ORDER BY ordem')->fetchAll();
$inovaParticipacao = db()->query("SELECT * FROM inova_listas WHERE grupo='participacao' ORDER BY ordem")->fetchAll();
$inovaAceites = db()->query("SELECT * FROM inova_listas WHERE grupo='aceites' ORDER BY ordem")->fetchAll();
$inovaProjetos = db()->query('SELECT * FROM inova_projetos WHERE ativo = 1 ORDER BY ordem, id')->fetchAll();

$premios = db()->query('SELECT * FROM premios ORDER BY ordem, id')->fetchAll();
$premioItens = [];
foreach (db()->query('SELECT * FROM premio_itens ORDER BY ordem') as $i) {
    $premioItens[$i['premio_id']][] = $i;
}

$modalidades = db()->query('SELECT * FROM modalidades ORDER BY ordem')->fetchAll();
$modalidadeItens = [];
foreach (db()->query('SELECT * FROM modalidade_itens ORDER BY ordem') as $i) {
    $modalidadeItens[$i['modalidade_id']][] = $i['texto'];
}

$prazos = db()->query('SELECT * FROM prazos ORDER BY ordem')->fetchAll();
$prazoItens = [];
foreach (db()->query('SELECT * FROM prazo_itens ORDER BY ordem') as $i) {
    $prazoItens[$i['prazo_id']][] = $i['texto'];
}

$cronograma = db()->query('SELECT * FROM cronograma ORDER BY ordem')->fetchAll();

$galeria = db()->query('SELECT * FROM galeria_itens WHERE ativo = 1 ORDER BY ordem, id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($s['evento_titulo_1'] . ' ' . $s['evento_titulo_2']) ?> · <?= e($s['evento_edicao']) ?></title>
<meta name="description" content="<?= e($s['evento_tema']) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/site.css?v=<?= filemtime(__DIR__ . '/assets/css/site.css') ?>">
<link rel="stylesheet" href="assets/css/utilities.css?v=<?= filemtime(__DIR__ . '/assets/css/utilities.css') ?>">
</head>
<body>

<div class="regua-topo" aria-hidden="true"></div>

<header class="cabecalho" id="cabecalho">
  <div class="limite cabecalho-interno">

    <a href="#inicio" class="marca">
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

    <nav class="menu" id="menuPrincipal" aria-label="Menu principal">
      <a href="#evento">O Evento</a>
      <a href="#eixos">Eixos</a>
      <a href="#programa">Programa</a>
      <a href="#inova">INOVA IPS</a>
      <a href="#galeria">Galeria</a>
      <a href="#documentos">Dúvidas e documentos</a>
      <a href="#submissoes">Submissões</a>
      <a href="consultar.php">Consultar</a>
    </nav>

    <a href="submeter.php" class="bt bt-verde cabecalho-bt hover:-translate-y-0.5 active:translate-y-0">Submeter Trabalho</a>

    <button class="menu-toggle focus-visible:ring-2 focus-visible:ring-cyan-500 focus-visible:ring-offset-2" id="menuToggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="menuPrincipal">
      <span></span><span></span><span></span>
    </button>

  </div>
</header>

<main>

<section class="hero" id="inicio">
  <div class="hero-slides" id="heroGaleria">
    <ul class="galeria-lista" id="galeriaLista">
      <?php foreach ($slides as $i => $slide): ?>
        <li class="galeria-slide <?= $i === 0 ? 'ativo' : '' ?>" data-index="<?= $i ?>">
          <figure>
            <img src="<?= e(imagem_url($slide['imagem'], $slide['titulo'])) ?>"
                 alt="<?= e($slide['titulo']) ?>"
                 <?= $i === 0 ? 'loading="eager"' : 'loading="lazy"' ?>>
            <figcaption>
              <span class="slide-num"><?= e($slide['numero']) ?></span>
              <span class="slide-legenda"><?= e($slide['legenda'] ?: $slide['titulo']) ?></span>
            </figcaption>
          </figure>
        </li>
      <?php endforeach; ?>

      <?php if (!$slides): ?>
        <li class="galeria-slide ativo">
          <figure>
            <img src="<?= e(imagem_url(null, 'Jornadas IPS 2026')) ?>" alt="Jornadas Técnico-Científicas 2026">
            <figcaption>
              <span class="slide-num">01</span>
              <span class="slide-legenda">Instituto Politécnico de Saurimo</span>
            </figcaption>
          </figure>
        </li>
      <?php endif; ?>
    </ul>
  </div>

  <div class="rede-molecular rede-molecular-hero" id="redeHero"></div>
  <div class="hero-overlay" aria-hidden="true"></div>

  <div class="hero-conteudo">
    <div class="limite hero-conteudo-inner">
      <div class="hero-texto">
        <p class="hero-marca">
          <span class="ponto"></span>
          <?= e($s['evento_data']) ?> &nbsp;·&nbsp; <?= e($s['evento_local_curto']) ?>
        </p>

        <h1 class="hero-titulo">
          <?= e($s['evento_titulo_1']) ?><br>
          <?= e($s['evento_titulo_2']) ?>
          <span class="hero-edicao"><?= e($s['evento_edicao']) ?></span>
        </h1>

        <div class="hero-tema">
          <p class="tema-rotulo">Tema</p>
          <p class="tema-texto">"<?= e($s['evento_tema']) ?>"</p>
          <p class="tema-lema"><?= e($s['evento_lema']) ?></p>
        </div>

        <div class="hero-acoes">
          <a href="#programa" class="bt bt-contorno">Consultar Programa</a>
          <a href="submeter.php" class="bt bt-verde">Submeter Trabalho</a>
        </div>
      </div>

      <div class="hero-barra">
        <div class="galeria-numeros" id="galeriaNumeros">
          <?php foreach ($slides as $i => $slide): ?>
            <button type="button" class="numero <?= $i === 0 ? 'ativo' : '' ?>" data-index="<?= $i ?>"><?= e($slide['numero']) ?></button>
          <?php endforeach; ?>
        </div>

        <div class="galeria-setas">
          <button type="button" class="seta" id="galeriaAnterior" aria-label="Imagem anterior">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M15 6l-6 6 6 6"/></svg>
          </button>
          <button type="button" class="seta" id="galeriaSeguinte" aria-label="Imagem seguinte">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 6l6 6-6 6"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="hero-rodape">
    <div class="limite">
      <dl class="hero-dados">
        <div><dt>Organização</dt><dd><?= e($s['evento_organizacao']) ?></dd></div>
        <div><dt>Instituição</dt><dd><?= e($s['evento_instituicao']) ?></dd></div>
        <div><dt>Contexto</dt><dd><?= e($s['evento_contexto']) ?></dd></div>
      </dl>
    </div>
  </div>
</section>

<section class="secao secao-clara" id="evento">
  <div class="limite">
    <div class="secao-topo">
      <p class="rotulo">01 · O Evento</p>
      <h2 class="secao-titulo">Sobre as Jornadas</h2>
    </div>

    <div class="evento-grelha">
      <div class="evento-texto">
        <p class="evento-abertura"><?= e($s['sobre_abertura']) ?></p>
        <p><?= e($s['sobre_paragrafo_1']) ?></p>
        <p><?= e($s['sobre_paragrafo_2']) ?></p>

        <dl class="evento-numeros-inline">
          <div><dt>Cursos</dt><dd><?= e($s['numeros_cursos']) ?></dd></div>
          <div><dt>Eixos</dt><dd><?= e($s['numeros_eixos']) ?></dd></div>
          <div><dt>Dias</dt><dd><?= e($s['numeros_dias']) ?></dd></div>
          <div class="numero-destaque"><dt>Feira Paralela</dt><dd><?= e($s['numeros_feira']) ?></dd></div>
        </dl>
      </div>

      <figure class="evento-imagem">
        <img src="<?= e(imagem_url($s['sobre_imagem'] ?? null, 'Jornadas IPS')) ?>"
             alt="<?= e($s['sobre_legenda'] ?? '') ?>" loading="lazy">
        <figcaption>
          <span class="slide-num">IPS</span>
          <span class="slide-legenda"><?= e($s['sobre_legenda'] ?: 'Edição anterior') ?></span>
        </figcaption>
      </figure>
    </div>
  </div>
</section>

<section class="secao secao-escura secao-rede" id="eixos">
  <div class="rede-molecular" id="redeEixos"></div>
  <div class="limite">
    <div class="secao-topo secao-topo-escuro">
      <p class="rotulo">02 · Estrutura Científica</p>
      <h2 class="secao-titulo">Eixos Temáticos</h2>
      <p class="secao-sub">Quatro eixos organizam a produção científica e a discussão das Jornadas.</p>
    </div>

    <div class="eixos">
      <?php foreach ($eixos as $eixo): ?>
        <article class="eixo">
          <span class="eixo-num"><?= e($eixo['numero']) ?></span>
          <div class="eixo-corpo">
            <h3><?= e($eixo['titulo']) ?></h3>
            <ul>
              <?php foreach ($cursosPorEixo[$eixo['id']] ?? [] as $curso): ?>
                <li><?= e($curso) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="secao secao-clara" id="programa">
  <div class="limite">
    <div class="secao-topo">
      <p class="rotulo">03 · Programa</p>
      <h2 class="secao-titulo">Dois dias de trabalho</h2>
      <p class="secao-sub">Conferências, mesas redondas, apresentações científicas e exposição da INOVA IPS.</p>
    </div>

    <div class="programa">
      <div class="programa-topo">
        <?php foreach ($dias as $dia): ?>
          <div class="programa-dia">
            <p class="dia-rotulo"><?= e($dia['numero']) ?></p>
            <h3 class="dia-data"><?= e($dia['data']) ?></h3>
            <p class="dia-semana"><?= e($dia['semana']) ?></p>
            <p class="dia-intro"><?= e($dia['intro']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="programa-base">
        <?php foreach ($dias as $dia): ?>
          <figure class="programa-flayer">
            <img src="<?= e(imagem_url($dia['flyer'], $dia['numero'])) ?>"
                 alt="Cartaz oficial — <?= e($dia['numero']) ?>" loading="lazy">
            <figcaption>
              <span class="slide-num"><?= e($dia['numero']) ?></span>
              <span class="slide-legenda">Cartaz oficial — <?= e($dia['data']) ?></span>
            </figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="secao secao-inova secao-rede" id="inova">
  <div class="rede-molecular" id="redeInova"></div>
  <div class="limite">
    <div class="inova-topo">
      <div>
        <p class="rotulo rotulo-inova">04 · Feira de Inovação Tecnológica</p>
        <h2 class="secao-titulo secao-titulo-inova">INOVA IPS 2026</h2>
      </div>
      <p class="inova-intro"><?= e($s['inova_intro']) ?></p>
    </div>

    <?php if ($inovaProjetos): ?>
      <div class="projetos-carrossel" id="projetosCarrossel">
        <div class="projetos-topo">
          <div>
            <p class="rotulo rotulo-inova">Projectos em exposição</p>
            <p class="projetos-sub">Trabalhos de inovação desenvolvidos pelos estudantes do Instituto Politécnico de Saurimo.</p>
          </div>
          <div class="galeria-setas">
            <button type="button" class="seta" id="projetosAnterior" aria-label="Projecto anterior">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M15 6l-6 6 6 6"/></svg>
            </button>
            <button type="button" class="seta" id="projetosSeguinte" aria-label="Projecto seguinte">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 6l6 6-6 6"/></svg>
            </button>
          </div>
        </div>

        <div class="projetos-janela">
          <ul class="projetos-lista" id="projetosLista">
            <?php foreach ($inovaProjetos as $i => $proj): ?>
              <li class="projetos-slide <?= $i === 0 ? 'ativo' : '' ?>" data-index="<?= $i ?>">
                <figure>
                  <img src="<?= e(imagem_url($proj['imagem'], $proj['titulo'])) ?>"
                       alt="<?= e($proj['titulo']) ?>" loading="lazy">
                  <figcaption>
                    <span class="slide-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="slide-legenda"><?= e($proj['titulo']) ?></span>
                  </figcaption>
                </figure>
                <div class="projetos-info">
                  <h3><?= e($proj['titulo']) ?></h3>
                  <?php if ($proj['descricao']): ?>
                    <p><?= e($proj['descricao']) ?></p>
                  <?php endif; ?>
                  <?php if ($proj['curso']): ?>
                    <p class="projetos-curso"><?= e($proj['curso']) ?></p>
                  <?php endif; ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="projetos-numeros" id="projetosNumeros">
          <?php foreach ($inovaProjetos as $i => $proj): ?>
            <button type="button" class="numero <?= $i === 0 ? 'ativo' : '' ?>" data-index="<?= $i ?>">
              <?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="brevemente brevemente-escuro">
        <p class="brevemente-texto">Brevemente</p>
        <p class="brevemente-nota">Os projectos em exposição serão apresentados aqui assim que forem registados pela comissão organizadora.</p>
      </div>
    <?php endif; ?>

    <div class="inova-blocos">
      <?php foreach ($inovaBlocos as $b): ?>
        <div class="inova-bloco">
          <h3><?= e($b['titulo']) ?></h3>
          <p><?= e($b['descricao']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="inova-detalhe">
      <div class="inova-participacao">
        <h3>Quem pode participar</h3>
        <ul>
          <?php foreach ($inovaParticipacao as $i): ?><li><?= e($i['texto']) ?></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="inova-aceites">
        <h3>Projectos aceites</h3>
        <ul>
          <?php foreach ($inovaAceites as $i): ?><li><?= e($i['texto']) ?></li><?php endforeach; ?>
        </ul>
      </div>
    </div>

    <?php if ($inovaCriterios): ?>
      <div class="criterios">
        <h3 class="criterios-titulo">Critérios de avaliação</h3>
        <ul class="criterios-lista">
          <?php foreach ($inovaCriterios as $c): ?>
            <li>
              <span class="criterio-nome"><?= e($c['nome']) ?></span>
              <span class="criterio-barra"><i style="--v:<?= (int) $c['percentagem'] ?>%"></i></span>
              <span class="criterio-valor"><?= (int) $c['percentagem'] ?>%</span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="secao secao-escura" id="premiacao">
  <div class="limite">
    <div class="secao-topo secao-topo-escuro">
      <p class="rotulo">Premiação</p>
      <h2 class="secao-titulo">Premiação INOVA IPS 2026</h2>
      <p class="secao-sub">Reconhecimento dos melhores projectos apresentados na Feira de Inovação Tecnológica.</p>
    </div>

    <?php
      $temConteudo = false;
      foreach ($premios as $p) {
          if (!empty($p['foto']) || !empty($p['nome'])) {
              $temConteudo = true;
              break;
          }
      }
    ?>

    <?php if (!$temConteudo): ?>
      <div class="brevemente brevemente-escuro">
        <p class="brevemente-texto">Brevemente</p>
        <p class="brevemente-nota">Os premiados da INOVA IPS 2026 serão anunciados durante a cerimónia de encerramento, no dia 13 de Novembro de 2026.</p>
      </div>
    <?php else: ?>
      <div class="premios">
        <?php foreach ($premios as $p): ?>
          <article class="premio <?= $p['destaque'] ? 'premio-primeiro' : '' ?>">

            <?php if (!empty($p['foto'])): ?>
              <figure class="premio-foto">
                <img src="<?= e(imagem_url($p['foto'], $p['nome'] ?: $p['lugar'])) ?>"
                     alt="<?= e($p['nome'] ?: $p['lugar']) ?>"
                     loading="lazy">
              </figure>
            <?php endif; ?>

            <p class="premio-posicao"><?= e($p['lugar']) ?></p>

            <?php if (!empty($p['nome'])): ?>
              <h3 class="premio-nome"><?= e($p['nome']) ?></h3>
            <?php endif; ?>

            <?php if (!empty($p['descricao'])): ?>
              <p class="premio-descricao"><?= e($p['descricao']) ?></p>
            <?php endif; ?>

            <?php if (!empty($premioItens[$p['id']])): ?>
              <ul class="premio-itens">
                <?php foreach ($premioItens[$p['id']] as $item): ?>
                  <li>
                    <?= e($item['texto']) ?>
                    <?php if ($item['nota']): ?><small><?= e($item['nota']) ?></small><?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="secao secao-clara" id="galeria">
  <div class="limite">
    <div class="secao-topo">
      <p class="rotulo">05 · Galeria</p>
      <h2 class="secao-titulo">Registos das Jornadas</h2>
      <p class="secao-sub">Momentos da edição anterior, dos laboratórios e dos projectos apresentados pelos estudantes do Instituto Politécnico de Saurimo.</p>
    </div>

    <?php if ($galeria): ?>
      <div class="galeria-carrossel" id="galeriaEventos">
        <div class="galeria-carrossel-janela">
          <ul class="galeria-carrossel-lista" id="galeriaEventosLista">
            <?php foreach ($galeria as $i => $item): ?>
              <li class="galeria-carrossel-slide <?= $i === 0 ? 'ativo' : '' ?>" data-index="<?= $i ?>">
                <figure>
                  <img src="<?= e(imagem_url($item['imagem'], $item['legenda'] ?? 'Galeria')) ?>"
                       alt="<?= e($item['legenda'] ?? '') ?>" loading="lazy">
                  <?php if ($item['legenda']): ?>
                    <figcaption>
                      <span class="slide-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                      <span class="slide-legenda"><?= e($item['legenda']) ?></span>
                    </figcaption>
                  <?php endif; ?>
                </figure>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="galeria-carrossel-controlos">
          <div class="galeria-carrossel-numeros" id="galeriaEventosNumeros">
            <?php foreach ($galeria as $i => $item): ?>
              <button type="button" class="numero-evento <?= $i === 0 ? 'ativo' : '' ?>" data-index="<?= $i ?>">
                <?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?>
              </button>
            <?php endforeach; ?>
          </div>

          <div class="galeria-carrossel-setas">
            <button type="button" class="seta-evento" id="galeriaEventosAnterior" aria-label="Anterior">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M15 6l-6 6 6 6"/></svg>
            </button>
            <button type="button" class="seta-evento" id="galeriaEventosSeguinte" aria-label="Seguinte">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 6l6 6-6 6"/></svg>
            </button>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="brevemente">
        <p class="brevemente-texto"><?= e($s['galeria_brevemente_texto']) ?></p>
        <p class="brevemente-nota"><?= e($s['galeria_brevemente_nota']) ?></p>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="secao secao-escura secao-rede" id="participacao">
  <div class="rede-molecular" id="redeParticipacao"></div>
  <div class="limite">
    <div class="secao-topo secao-topo-escuro">
      <p class="rotulo">06 · Modalidades</p>
      <h2 class="secao-titulo">Participação nas Jornadas</h2>
    </div>

    <div class="participacao">
      <?php foreach ($modalidades as $m): ?>
        <article class="modalidade">
          <p class="modalidade-letra"><?= e($m['letra']) ?></p>
          <h3><?= e($m['titulo']) ?></h3>
          <p><?= e($m['descricao']) ?></p>
          <?php if (!empty($modalidadeItens[$m['id']])): ?>
            <ul>
              <?php foreach ($modalidadeItens[$m['id']] as $it): ?><li><?= e($it) ?></li><?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="secao secao-escura" id="submissoes" style="background:#061530">
  <div class="limite">
    <div class="secao-topo secao-topo-escuro">
      <p class="rotulo">07 · Submissões</p>
      <h2 class="secao-titulo">Submeta o seu trabalho</h2>
      <p class="secao-sub">As submissões decorrem até 15 de Outubro de 2026.</p>
    </div>

    <div class="prazos">
      <?php foreach ($prazos as $pr): ?>
        <article class="prazo <?= $pr['destaque'] ? 'prazo-principal' : '' ?>">
          <p class="prazo-data"><?= e($pr['data']) ?></p>
          <h3><?= e($pr['titulo']) ?></h3>
          <?php if (!empty($prazoItens[$pr['id']])): ?>
            <ul>
              <?php foreach ($prazoItens[$pr['id']] as $it): ?><li><?= e($it) ?></li><?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="submissoes-atalho">
      <div class="submissoes-atalho-texto">
        <p class="submissoes-atalho-titulo">Pronto para submeter?</p>
        <p class="submissoes-atalho-sub">Aceda ao formulário completo com upload de PDF, escolha do eixo temático e confirmação imediata.</p>
      </div>
      <div class="submissoes-atalho-acoes">
        <a href="submeter.php" class="bt bt-verde bt-grande">Submeter Trabalho</a>
        <a href="consultar.php" class="bt bt-contorno" style="border-color:rgba(255,255,255,0.4)">Consultar Submissão</a>
      </div>
    </div>
  </div>
</section>

<section class="secao secao-local documentos" id="documentos">
  <div class="limite">
    <div class="secao-topo">
      <p class="rotulo">08 · Apoio ao concorrente</p>
      <h2 class="secao-titulo">Dúvidas e documentos</h2>
      <p class="secao-sub">Consulte as normas e descarregue os modelos oficiais antes de preparar a sua submissão.</p>
    </div>

    <p class="documentos-meta"><strong><?= count($documentos) ?></strong> <?= count($documentos) === 1 ? 'documento oficial disponível' : 'documentos oficiais disponíveis' ?></p>

    <?php if ($documentos): ?>
      <div class="documentos-lista">
        <?php foreach ($documentos as $documento): ?>
          <?php $urlDocumento = 'assets/pdfs/' . rawurlencode($documento['ficheiro']); ?>
          <article class="documento-item">
            <div class="documento-identidade">
              <span class="documento-selo" aria-hidden="true">PDF</span>
              <div>
                <h3><?= e($documento['titulo']) ?></h3>
                <p><?= e($documento['descricao']) ?></p>
                <span class="documento-tamanho"><?= number_format($documento['tamanho'] / 1048576, 1, ',', '.') ?> MB · PDF</span>
              </div>
            </div>
            <div class="documento-acoes">
              <a class="documento-ver" href="<?= e($urlDocumento) ?>" target="_blank" rel="noopener">Ver documento</a>
              <a class="documento-baixar" href="<?= e($urlDocumento) ?>" download>Descarregar</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="documentos-vazio">Os documentos de apoio serão disponibilizados em breve.</p>
    <?php endif; ?>
  </div>
</section>

<section class="secao secao-clara" id="cronograma">
  <div class="limite">
    <div class="secao-topo">
      <p class="rotulo">09 · Calendário</p>
      <h2 class="secao-titulo">Cronograma</h2>
    </div>

    <ol class="cronograma">
      <?php foreach ($cronograma as $item): ?>
        <li class="<?= $item['tipo'] === 'evento' ? 'cronograma-evento' : '' ?>">
          <span class="cronograma-data"><?= e($item['data']) ?></span>
          <span class="cronograma-marca"></span>
          <span class="cronograma-texto"><?= e($item['texto']) ?></span>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="secao secao-local" id="contactos">
  <div class="limite local-grelha">
    <div class="local-info">
      <p class="rotulo">10 · Local</p>
      <h2 class="secao-titulo">Local do Evento</h2>
      <address>
        <strong><?= e($s['local_nome']) ?></strong>
        <?php if (!empty($s['local_endereco'])): ?>
          <?= e($s['local_endereco']) ?><br>
        <?php endif; ?>
        <?= e($s['local_cidade']) ?><br>
        <?= e($s['local_pais']) ?>
      </address>

      <?php if (!empty($s['local_indicacoes'])): ?>
        <p class="local-indicacoes"><?= e($s['local_indicacoes']) ?></p>
      <?php endif; ?>

      <?php if (!empty($s['local_contacto'])): ?>
        <p class="local-contacto"><strong>Contacto:</strong> <?= e($s['local_contacto']) ?></p>
      <?php endif; ?>
    </div>

    <div class="local-mapa">
      <?php if (!empty($s['local_mapa_url'])): ?>
        <iframe src="<?= e($s['local_mapa_url']) ?>"
                width="100%" height="100%"
                style="border:0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="<?= e($s['local_nome']) ?>"></iframe>
      <?php else: ?>
        <div class="mapa-placeholder">
          <span class="mapa-ponto"></span>
          <p><?= e($s['local_nome']) ?></p>
          <small>A integração do mapa será disponibilizada após confirmação das coordenadas oficiais.</small>
        </div>
      <?php endif; ?>
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
        <li><a href="#inicio">Início</a></li>
        <li><a href="#evento">O Evento</a></li>
        <li><a href="#eixos">Eixos</a></li>
        <li><a href="#programa">Programa</a></li>
        <li><a href="#inova">INOVA IPS</a></li>
        <li><a href="#documentos">Dúvidas e documentos</a></li>
        <li><a href="submeter.php">Submeter Trabalho</a></li>
        <li><a href="consultar.php">Consultar Submissão</a></li>
      </ul>
    </div>
  </div>

  <div class="limite rodape-base">
    <p class="rodape-lema"><?= e($s['rodape_lema']) ?></p>
    <p class="rodape-copy">© 2026 <?= e($s['evento_organizacao']) ?> · <?= e($s['evento_instituicao']) ?></p>
  </div>
</footer>

<script src="assets/js/redes.js"></script>
<script src="assets/js/menu.js"></script>
<script src="assets/js/site.js"></script>
</body>
</html>