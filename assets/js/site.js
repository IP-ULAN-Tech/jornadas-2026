(function () {
  'use strict';

  const cabecalho = document.getElementById('cabecalho');
  if (cabecalho) {
    const aoRolar = () => cabecalho.classList.toggle('rolado', window.scrollY > 12);
    window.addEventListener('scroll', aoRolar, { passive: true });
    aoRolar();
  }

  const botaoMenu = document.getElementById('menuToggle');
  const menu = document.querySelector('.menu');
  if (botaoMenu && menu) {
    botaoMenu.addEventListener('click', () => {
      const aberto = menu.classList.toggle('aberto');
      botaoMenu.classList.toggle('aberto', aberto);
    });

    menu.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        menu.classList.remove('aberto');
        botaoMenu.classList.remove('aberto');
      });
    });
  }

  /* 
     REDE MOLECULAR VIVA
      */
  const reduzido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function criarRede(el, opcoes) {
    if (!el) return;

    const cfg = Object.assign({
      total: 30,
      raioLigacao: 220,
      velocidade: 0.4,
      corPonto: 'rgba(184,230,0,0.85)',
      corLinha: 'rgba(0,165,196,0.7)',
      opacidadePonto: 0.55,
      opacidadeLinhaMax: 0.42,
      tamanhoPonto: [1.6, 3.2]
    }, opcoes || {});

    const L = 1600;
    const A = 1000;

    const pontos = [];
    for (let i = 0; i < cfg.total; i++) {
      pontos.push({
        x: Math.random() * L,
        y: Math.random() * A,
        vx: (Math.random() - 0.5) * cfg.velocidade,
        vy: (Math.random() - 0.5) * cfg.velocidade,
        r: cfg.tamanhoPonto[0] + Math.random() * (cfg.tamanhoPonto[1] - cfg.tamanhoPonto[0])
      });
    }

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', '0 0 ' + L + ' ' + A);
    svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');

    const gLinhas = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    const gPontos = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    svg.appendChild(gLinhas);
    svg.appendChild(gPontos);

    const linhas = [];
    const circulos = [];

    for (let i = 0; i < cfg.total; i++) {
      const c = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
      c.setAttribute('r', pontos[i].r);
      c.setAttribute('fill', cfg.corPonto);
      c.setAttribute('fill-opacity', cfg.opacidadePonto);
      gPontos.appendChild(c);
      circulos.push(c);
    }

    for (let i = 0; i < cfg.total; i++) {
      for (let j = i + 1; j < cfg.total; j++) {
        const l = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        l.setAttribute('stroke', cfg.corLinha);
        l.setAttribute('stroke-opacity', '0');
        l.setAttribute('stroke-width', '1');
        gLinhas.appendChild(l);
        linhas.push({ i: i, j: j, el: l });
      }
    }

    el.innerHTML = '';
    el.appendChild(svg);

    function desenhar() {
      for (let k = 0; k < cfg.total; k++) {
        const p = pontos[k];
        if (!reduzido) {
          p.x += p.vx;
          p.y += p.vy;
          if (p.x < 0 || p.x > L) p.vx *= -1;
          if (p.y < 0 || p.y > A) p.vy *= -1;
        }
        circulos[k].setAttribute('cx', p.x);
        circulos[k].setAttribute('cy', p.y);
      }

      for (let m = 0; m < linhas.length; m++) {
        const a = pontos[linhas[m].i];
        const b = pontos[linhas[m].j];
        const dx = a.x - b.x;
        const dy = a.y - b.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        const opacidade = dist < cfg.raioLigacao
          ? (1 - dist / cfg.raioLigacao) * cfg.opacidadeLinhaMax
          : 0;

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

  // Hero — mais denso e vivo
  const redeHero = document.getElementById('redeHero');
  if (redeHero) {
    criarRede(redeHero, {
      total: 40,
      raioLigacao: 240,
      velocidade: 0.45,
      corPonto: 'rgba(184,230,0,0.95)',
      corLinha: 'rgba(184,230,0,0.75)',
      opacidadePonto: 0.75,
      opacidadeLinhaMax: 0.55,
      tamanhoPonto: [1.8, 3.5]
    });
  }

  // Secções escuras — rede visível em navy
  document.querySelectorAll('.rede-molecular[data-rede]').forEach((el) => {
    criarRede(el, {
      total: 28,
      raioLigacao: 210,
      velocidade: 0.35,
      corPonto: 'rgba(255,255,255,0.9)',
      corLinha: 'rgba(0,165,196,0.85)',
      opacidadePonto: 0.5,
      opacidadeLinhaMax: 0.4,
      tamanhoPonto: [1.5, 3]
    });
  });

  /* ============================================================
     REVEAL
     ============================================================ */
  const alvos = document.querySelectorAll(
    '.secao-topo, .evento-texto, .evento-imagem, .eixo, .programa, ' +
    '.inova-topo, .projetos-carrossel, .inova-bloco, .inova-detalhe, .criterios, ' +
    '.brevemente, .galeria-carrossel, .modalidade, .prazo, .cronograma, ' +
    '.local-info, .local-mapa'
  );
  alvos.forEach((el) => el.classList.add('reveal'));

  if ('IntersectionObserver' in window) {
    const observador = new IntersectionObserver((entradas) => {
      entradas.forEach((entrada) => {
        if (entrada.isIntersecting) {
          entrada.target.classList.add('visivel');
          observador.unobserve(entrada.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
    alvos.forEach((el) => observador.observe(el));
  } else {
    alvos.forEach((el) => el.classList.add('visivel'));
  }

  /* ============================================================
     SCROLL SUAVE
     ============================================================ */
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener('click', (evento) => {
      const id = link.getAttribute('href');
      if (id.length < 2) return;
      const destino = document.querySelector(id);
      if (!destino) return;
      evento.preventDefault();
      const topo = destino.getBoundingClientRect().top + window.scrollY - 88;
      window.scrollTo({ top: topo, behavior: 'smooth' });
    });
  });

  /* 
     CARROSSEL GENÉRICO
      */
  function iniciarCarrossel(config) {
    const raiz = document.getElementById(config.raiz);
    const lista = document.getElementById(config.lista);
    const numeros = document.querySelectorAll(config.numeros);
    const setaAnterior = document.getElementById(config.setaAnterior);
    const setaSeguinte = document.getElementById(config.setaSeguinte);

    if (!raiz || !lista) return;

    const slides = lista.querySelectorAll('.' + config.classeSlide);
    const total = slides.length;
    if (total === 0) return;

    let indice = 0;
    let temporizador = null;
    const intervalo = config.intervalo || 8000;

    function irPara(novoIndice, porUtilizador) {
      indice = (novoIndice + total) % total;
      lista.style.transform = 'translateX(' + (-indice * 100) + '%)';
      slides.forEach((s, i) => s.classList.toggle('ativo', i === indice));
      numeros.forEach((n, i) => n.classList.toggle('ativo', i === indice));
      if (porUtilizador) reiniciar();
    }

    function seguinte(porUtilizador) { irPara(indice + 1, porUtilizador); }
    function anterior(porUtilizador) { irPara(indice - 1, porUtilizador); }

    function iniciar() {
      if (reduzido || total <= 1) return;
      parar();
      temporizador = window.setInterval(() => seguinte(false), intervalo);
    }
    function parar() {
      if (temporizador) { window.clearInterval(temporizador); temporizador = null; }
    }
    function reiniciar() { parar(); iniciar(); }

    if (setaSeguinte) setaSeguinte.addEventListener('click', () => seguinte(true));
    if (setaAnterior) setaAnterior.addEventListener('click', () => anterior(true));

    numeros.forEach((n) => {
      n.addEventListener('click', () => {
        const idx = parseInt(n.dataset.index, 10);
        if (!Number.isNaN(idx)) irPara(idx, true);
      });
    });

    raiz.addEventListener('mouseenter', parar);
    raiz.addEventListener('mouseleave', iniciar);

    irPara(0);
    iniciar();
  }

  iniciarCarrossel({
    raiz: 'heroGaleria',
    lista: 'galeriaLista',
    numeros: '#galeriaNumeros .numero',
    setaAnterior: 'galeriaAnterior',
    setaSeguinte: 'galeriaSeguinte',
    classeSlide: 'galeria-slide',
    intervalo: 9000
  });

  iniciarCarrossel({
    raiz: 'projetosCarrossel',
    lista: 'projetosLista',
    numeros: '#projetosNumeros .numero',
    setaAnterior: 'projetosAnterior',
    setaSeguinte: 'projetosSeguinte',
    classeSlide: 'projetos-slide',
    intervalo: 7000
  });

  iniciarCarrossel({
    raiz: 'galeriaEventos',
    lista: 'galeriaEventosLista',
    numeros: '#galeriaEventosNumeros .numero-evento',
    setaAnterior: 'galeriaEventosAnterior',
    setaSeguinte: 'galeriaEventosSeguinte',
    classeSlide: 'galeria-carrossel-slide',
    intervalo: 6000
  });

})();