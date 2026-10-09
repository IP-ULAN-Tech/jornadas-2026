(function () {
  'use strict';

  const reduzido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function redeCerebro(el, opcoes) {
    if (!el) return;

    const cfg = Object.assign({
      colunas: 18,
      linhas: 12,
      espaco: 88,
      raioLigacao: 125,
      corPonto: 'rgba(255,255,255,0.6)',
      corPontoDestaque: 'rgba(184,230,0,0.85)',
      corLinha: 'rgba(0,165,196,0.4)',
      raioPonto: [2.2, 3.2],
      raioDestaque: [4, 5.5],
      opacidadeLinhaMax: 0.4,
      espessuraLinha: 1.1,
      amplitudeOnda: 15,
      velocidadeOnda: 0.001,
      densidadeDestaque: 0.05
    }, opcoes || {});

    const L = (cfg.colunas - 1) * cfg.espaco;
    const A = (cfg.linhas - 1) * cfg.espaco;

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', '0 0 ' + L + ' ' + A);
    svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');

    const gLinhas = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    const gPontos = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    svg.appendChild(gLinhas);
    svg.appendChild(gPontos);

    const pontos = [];
    const circulos = [];
    const linhas = [];

    const cx = L / 2;
    const cy = A / 2;

    function calcularOpacidade(x, y) {
      const dx = (x - cx) / cx;
      const dy = (y - cy) / cy;
      const dist = Math.sqrt(dx * dx + dy * dy);
      return Math.max(0.2, 1 - dist * 0.8);
    }

    for (let r = 0; r < cfg.linhas; r++) {
      for (let c = 0; c < cfg.colunas; c++) {
        const xBase = c * cfg.espaco;
        const yBase = r * cfg.espaco;

        const opacBase = calcularOpacidade(xBase, yBase);
        const destaque = Math.random() < cfg.densidadeDestaque;

        const p = {
          xBase: xBase,
          yBase: yBase,
          x: xBase,
          y: yBase,
          r: destaque
            ? cfg.raioDestaque[0] + Math.random() * (cfg.raioDestaque[1] - cfg.raioDestaque[0])
            : cfg.raioPonto[0] + Math.random() * (cfg.raioPonto[1] - cfg.raioPonto[0]),
          destaque: destaque,
          opacBase: opacBase,
          fase: Math.random() * Math.PI * 2,
          fase2: Math.random() * Math.PI * 2,
          col: c,
          lin: r
        };

        const circulo = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circulo.setAttribute('r', p.r);
        circulo.setAttribute('fill', destaque ? cfg.corPontoDestaque : cfg.corPonto);
        circulo.setAttribute('fill-opacity', (destaque ? 0.85 : 0.6) * opacBase);
        gPontos.appendChild(circulo);

        pontos.push(p);
        circulos.push(circulo);
      }
    }

    const mapa = {};
    pontos.forEach((p) => {
      mapa[p.lin + ',' + p.col] = p;
    });

    pontos.forEach((p) => {
      const dir = mapa[p.lin + ',' + (p.col + 1)];
      if (dir) {
        const l = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        l.setAttribute('stroke', cfg.corLinha);
        l.setAttribute('stroke-width', cfg.espessuraLinha);
        l.setAttribute('stroke-linecap', 'round');
        gLinhas.appendChild(l);
        linhas.push({ a: p, b: dir, el: l });
      }
      const baixo = mapa[(p.lin + 1) + ',' + p.col];
      if (baixo) {
        const l = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        l.setAttribute('stroke', cfg.corLinha);
        l.setAttribute('stroke-width', cfg.espessuraLinha);
        l.setAttribute('stroke-linecap', 'round');
        gLinhas.appendChild(l);
        linhas.push({ a: p, b: baixo, el: l });
      }
      if (Math.random() < 0.25) {
        const diag = mapa[(p.lin + 1) + ',' + (p.col + 1)];
        if (diag) {
          const l = document.createElementNS('http://www.w3.org/2000/svg', 'line');
          l.setAttribute('stroke', cfg.corLinha);
          l.setAttribute('stroke-width', cfg.espessuraLinha * 0.7);
          l.setAttribute('stroke-opacity', '0.5');
          l.setAttribute('stroke-linecap', 'round');
          gLinhas.appendChild(l);
          linhas.push({ a: p, b: diag, el: l, maisFraca: true });
        }
      }
    });

    el.innerHTML = '';
    el.appendChild(svg);

    const inicio = performance.now();

    function desenhar(agora) {
      const t = (agora - inicio) * cfg.velocidadeOnda;

      for (let k = 0; k < pontos.length; k++) {
        const p = pontos[k];

        if (!reduzido) {
          const ondaX = Math.sin(t + p.fase + p.xBase * 0.008) * cfg.amplitudeOnda;
          const ondaY = Math.cos(t + p.fase2 + p.yBase * 0.008) * cfg.amplitudeOnda;

          p.x = p.xBase + ondaX;
          p.y = p.yBase + ondaY;

          circulos[k].setAttribute('cx', p.x.toFixed(2));
          circulos[k].setAttribute('cy', p.y.toFixed(2));

          if (p.destaque) {
            const pulso = 0.75 + Math.sin(t * 2.5 + p.fase) * 0.15;
            circulos[k].setAttribute('fill-opacity', (pulso * p.opacBase).toFixed(3));
          }
        }
      }

      for (let m = 0; m < linhas.length; m++) {
        const l = linhas[m];
        const a = l.a;
        const b = l.b;
        const dx = a.x - b.x;
        const dy = a.y - b.y;
        const dist = Math.sqrt(dx * dx + dy * dy);

        const opacBase = Math.min(a.opacBase, b.opacBase);
        let opac = (1 - dist / cfg.raioLigacao) * cfg.opacidadeLinhaMax * opacBase;
        if (opac < 0) opac = 0;
        if (l.maisFraca) opac *= 0.5;

        l.el.setAttribute('x1', a.x.toFixed(2));
        l.el.setAttribute('y1', a.y.toFixed(2));
        l.el.setAttribute('x2', b.x.toFixed(2));
        l.el.setAttribute('y2', b.y.toFixed(2));
        l.el.setAttribute('stroke-opacity', opac.toFixed(3));
      }

      if (!reduzido) requestAnimationFrame(desenhar);
    }

    requestAnimationFrame(desenhar);
  }

  document.addEventListener('DOMContentLoaded', function () {

    /* 
       HERO — sobre imagem fotográfica, ligeiramente mais brilhante
        */
    const hero = document.getElementById('redeHero');
    if (hero) {
      redeCerebro(hero, {
        colunas: 20,
        linhas: 13,
        espaco: 78,
        raioLigacao: 115,
        corPonto: 'rgba(255,255,255,0.75)',
        corPontoDestaque: 'rgba(184,230,0,0.9)',
        corLinha: 'rgba(184,230,0,0.6)',
        raioPonto: [2.5, 3.5],
        raioDestaque: [4.5, 6],
        opacidadeLinhaMax: 0.5,
        espessuraLinha: 1.3,
        amplitudeOnda: 16,
        velocidadeOnda: 0.0011,
        densidadeDestaque: 0.05
      });
    }

    /* ------------------------------------------------------------
       EIXOS, INOVA, PARTICIPAÇÃO — exactamente iguais
       Fundo discreto, presente mas nunca dominante
       ------------------------------------------------------------ */
    const configuracaoPadrao = {
      colunas: 18,
      linhas: 12,
      espaco: 88,
      raioLigacao: 125,
      corPonto: 'rgba(255,255,255,0.6)',
      corPontoDestaque: 'rgba(184,230,0,0.85)',
      corLinha: 'rgba(0,165,196,0.4)',
      raioPonto: [2.2, 3.2],
      raioDestaque: [4, 5.5],
      opacidadeLinhaMax: 0.4,
      espessuraLinha: 1.1,
      amplitudeOnda: 15,
      velocidadeOnda: 0.001,
      densidadeDestaque: 0.05
    };

    const eixos = document.getElementById('redeEixos');
    if (eixos) {
      redeCerebro(eixos, Object.assign({}, configuracaoPadrao, {
        corPonto: 'rgba(255,255,255,0.9)',
        corPontoDestaque: 'rgba(232,196,119,1)',
        corLinha: 'rgba(139,183,214,0.58)',
        raioPonto: [2.8, 3.8],
        raioDestaque: [5, 7],
        opacidadeLinhaMax: 0.55,
        espessuraLinha: 1.35,
        densidadeDestaque: 0.09
      }));
    }

    const inova = document.getElementById('redeInova');
    if (inova) redeCerebro(inova, configuracaoPadrao);

    const participacao = document.getElementById('redeParticipacao');
    if (participacao) redeCerebro(participacao, configuracaoPadrao);
  });

})();