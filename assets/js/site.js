(function () {
  'use strict';

  /* ---------- Cabeçalho ---------- */
  const cabecalho = document.getElementById('cabecalho');
  if (cabecalho) {
    const aoRolar = () => cabecalho.classList.toggle('rolado', window.scrollY > 12);
    window.addEventListener('scroll', aoRolar, { passive: true });
    aoRolar();
  }

  /* ---------- Menu mobile ---------- */
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

  /* ---------- Rede molecular ---------- */
  const ID_PADRAO = 'rede-molecular-pattern-' + Math.random().toString(36).slice(2, 8);

  function criarDefsPadraoMolecular(id) {
    return '' +
      '<svg width="0" height="0" style="position:absolute" aria-hidden="true">' +
        '<defs>' +
          '<pattern id="' + id + '" x="0" y="0" width="320" height="320" patternUnits="userSpaceOnUse">' +
            '<line x1="70" y1="60" x2="140" y2="90" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>' +
            '<line x1="140" y1="90" x2="230" y2="60" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>' +
            '<line x1="140" y1="90" x2="130" y2="180" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>' +
            '<line x1="140" y1="90" x2="250" y2="160" stroke="rgba(255,255,255,0.35)" stroke-width="1"/>' +
            '<line x1="130" y1="180" x2="60" y2="250" stroke="rgba(255,255,255,0.35)" stroke-width="1"/>' +
            '<line x1="130" y1="180" x2="220" y2="240" stroke="rgba(255,255,255,0.35)" stroke-width="1"/>' +
            '<line x1="220" y1="240" x2="290" y2="280" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>' +
            '<line x1="60" y1="250" x2="20" y2="300" stroke="rgba(255,255,255,0.28)" stroke-width="1"/>' +
            '<circle cx="70" cy="60" r="3.5" fill="rgba(255,255,255,0.75)"/>' +
            '<circle cx="230" cy="60" r="3.5" fill="rgba(255,255,255,0.75)"/>' +
            '<circle cx="140" cy="90" r="5" fill="rgba(184,230,0,0.95)"/>' +
            '<circle cx="250" cy="160" r="3.5" fill="rgba(255,255,255,0.7)"/>' +
            '<circle cx="130" cy="180" r="4" fill="rgba(255,255,255,0.8)"/>' +
            '<circle cx="60" cy="250" r="3.5" fill="rgba(255,255,255,0.7)"/>' +
            '<circle cx="220" cy="240" r="3.5" fill="rgba(184,230,0,0.8)"/>' +
            '<circle cx="290" cy="280" r="3" fill="rgba(255,255,255,0.55)"/>' +
            '<circle cx="20" cy="300" r="3" fill="rgba(255,255,255,0.55)"/>' +
          '</pattern>' +
        '</defs>' +
      '</svg>';
  }

  document.body.insertAdjacentHTML('beforeend', criarDefsPadraoMolecular(ID_PADRAO));

  document.querySelectorAll('.rede-molecular').forEach((el) => {
    el.innerHTML =
      '<svg width="100%" height="100%" aria-hidden="true">' +
        '<rect width="100%" height="100%" fill="url(#' + ID_PADRAO + ')"/>' +
      '</svg>';
  });

  /* ---------- Reveal ---------- */
  const alvos = document.querySelectorAll(
    '.secao-topo, .evento-texto, .evento-imagem, .eixo, .programa, ' +
    '.inova-topo, .inova-imagem, .projetos-carrossel, .inova-bloco, .inova-detalhe, .criterios, ' +
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

  /* ---------- Scroll suave ---------- */
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

  /* ---------- Carrossel genérico ---------- */
  function iniciarCarrossel(config) {
    const raiz = document.getElementById(config.raiz);
    const lista = document.getElementById(config.lista);
    const numeros = document.querySelectorAll(config.numeros);
    const setaAnterior = document.getElementById(config.setaAnterior);
    const setaSeguinte = document.getElementById(config.setaSeguinte);
    const classeSlide = config.classeSlide;
    const classeNumero = config.classeNumero;

    if (!raiz || !lista) return;

    const slides = lista.querySelectorAll('.' + classeSlide);
    const total = slides.length;
    if (total === 0) return;

    let indice = 0;
    let temporizador = null;
    const intervalo = config.intervalo || 8000;
    const reduzido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function irPara(novoIndice, porUtilizador) {
      indice = (novoIndice + total) % total;
      lista.style.transform = 'translateX(' + (-indice * 100) + '%)';
      slides.forEach((s, i) => s.classList.toggle('ativo', i === indice));
      numeros.forEach((n, i) => n.classList.toggle(classeNumero, i === indice));
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
    raiz.addEventListener('focusin', parar);
    raiz.addEventListener('focusout', iniciar);

    raiz.addEventListener('keydown', (evento) => {
      if (evento.key === 'ArrowRight') { evento.preventDefault(); seguinte(true); }
      if (evento.key === 'ArrowLeft')  { evento.preventDefault(); anterior(true); }
    });

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) parar(); else iniciar();
    });

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
    classeNumero: 'ativo',
    intervalo: 9000
  });

  iniciarCarrossel({
    raiz: 'projetosCarrossel',
    lista: 'projetosLista',
    numeros: '#projetosNumeros .numero',
    setaAnterior: 'projetosAnterior',
    setaSeguinte: 'projetosSeguinte',
    classeSlide: 'projetos-slide',
    classeNumero: 'ativo',
    intervalo: 7000
  });

  iniciarCarrossel({
    raiz: 'galeriaEventos',
    lista: 'galeriaEventosLista',
    numeros: '#galeriaEventosNumeros .numero-evento',
    setaAnterior: 'galeriaEventosAnterior',
    setaSeguinte: 'galeriaEventosSeguinte',
    classeSlide: 'galeria-carrossel-slide',
    classeNumero: 'ativo',
    intervalo: 6000
  });

})();