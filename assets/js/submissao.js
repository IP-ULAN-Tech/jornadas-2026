(function () {
  'use strict';

  const form = document.getElementById('formSubmissao');
  if (!form) return;

  const tipo = document.getElementById('tipo');
  const campoEixo = document.getElementById('campoEixo');
  const camposInova = document.getElementById('camposInova');
  const areaInova = document.getElementById('area_inova');
  const equipa = document.getElementById('elementos_equipa');
  const resumo = document.getElementById('resumo');
  const contador = document.getElementById('contadorResumo');
  const botao = document.getElementById('botaoSubmeter');
  const resultado = document.getElementById('resultadoSubmissao');
  const rede = document.getElementById('redeSubmeter');

  if (tipo) {
    tipo.addEventListener('change', () => {
      const eInova = tipo.value === 'projeto_inova';
      camposInova.hidden = !eInova;
      if (campoEixo) campoEixo.style.opacity = eInova ? 0.5 : 1;

      if (areaInova) areaInova.required = eInova;
      if (equipa) equipa.required = eInova;
    });
  }

  if (resumo && contador) {
    resumo.addEventListener('input', () => {
      const n = resumo.value.length;
      contador.textContent = n;
      contador.style.color = n > 2000 ? '#E5484D' : '';
    });
  }

  function construirRede() {
    if (!rede) return;

    const largura = 1600;
    const altura = 900;
    const pontos = [];
    const total = 26;

    for (let i = 0; i < total; i++) {
      pontos.push({
        x: Math.random() * largura,
        y: Math.random() * altura,
        vx: (Math.random() - 0.5) * 0.35,
        vy: (Math.random() - 0.5) * 0.35,
        r: 1.5 + Math.random() * 1.5,
      });
    }

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', '0 0 ' + largura + ' ' + altura);
    svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');

    const gLinhas = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    const gPontos = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    svg.appendChild(gLinhas);
    svg.appendChild(gPontos);

    const linhas = [];
    const circulos = [];

    for (let i = 0; i < total; i++) {
      const c = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
      c.setAttribute('r', pontos[i].r);
      c.setAttribute('fill', '#0A1F44');
      c.setAttribute('fill-opacity', '0.35');
      gPontos.appendChild(c);
      circulos.push(c);
    }

    for (let i = 0; i < total; i++) {
      for (let j = i + 1; j < total; j++) {
        const l = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        l.setAttribute('stroke', '#00A5C4');
        l.setAttribute('stroke-opacity', '0.16');
        l.setAttribute('stroke-width', '1');
        gLinhas.appendChild(l);
        linhas.push({ i: i, j: j, el: l });
      }
    }

    rede.appendChild(svg);

    let activo = true;

    function desenhar() {
      if (!activo) return;

      for (let k = 0; k < total; k++) {
        const p = pontos[k];
        p.x += p.vx;
        p.y += p.vy;

        if (p.x < 0 || p.x > largura) p.vx *= -1;
        if (p.y < 0 || p.y > altura) p.vy *= -1;

        circulos[k].setAttribute('cx', p.x);
        circulos[k].setAttribute('cy', p.y);
      }

      for (let k = 0; k < linhas.length; k++) {
        const a = pontos[linhas[k].i];
        const b = pontos[linhas[k].j];
        const dx = a.x - b.x;
        const dy = a.y - b.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        const opacidade = dist < 200 ? (1 - dist / 200) * 0.32 : 0;

        linhas[k].el.setAttribute('x1', a.x);
        linhas[k].el.setAttribute('y1', a.y);
        linhas[k].el.setAttribute('x2', b.x);
        linhas[k].el.setAttribute('y2', b.y);
        linhas[k].el.setAttribute('stroke-opacity', opacidade.toFixed(3));
      }

      requestAnimationFrame(desenhar);
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      activo = false;
      desenhar = function () { return; };
    }

    desenhar();
  }

  construirRede();

  form.addEventListener('submit', async (evento) => {
    evento.preventDefault();

    if (resultado) {
      resultado.hidden = true;
      resultado.classList.remove('erro');
      resultado.innerHTML = '';
    }

    botao.disabled = true;
    botao.querySelector('span').textContent = 'A enviar...';

    try {
      const resposta = await fetch('submeter-api.php', {
        method: 'POST',
        body: new FormData(form)
      });

      const dados = await resposta.json();

      if (!resposta.ok) {
        throw new Error(dados.erro || 'Não foi possível submeter.');
      }

      resultado.innerHTML = `
        <h2>Submissão recebida com sucesso</h2>
        <p class="resultado-numero">#${dados.id}</p>
        <p>
          O seu trabalho foi registado no sistema das Jornadas Técnico-Científicas do IPS — Edição 2026.
          <strong>Guarde o número de registo</strong> — vai precisar dele para consultar o estado da submissão.
        </p>
        <p>
          Foi enviada uma confirmação para <strong>${escapar(form.email.value)}</strong>.
          Se não receber em alguns minutos, verifique a pasta de spam.
        </p>
        <p class="resultado-rodape">
          A comissão organizadora irá analisar o trabalho e comunicará a decisão
          por email no prazo indicado (até 3 de Novembro de 2026).
        </p>
        <div class="resultado-acoes">
          <a href="consultar.php" class="resultado-botao">Consultar estado da submissão</a>
          <a href="submeter.php" class="resultado-botao secundario">Submeter outro trabalho</a>
        </div>
      `;
      resultado.hidden = false;
      resultado.scrollIntoView({ behavior: 'smooth', block: 'center' });

      form.reset();
      if (contador) contador.textContent = '0';
      if (camposInova) camposInova.hidden = true;

    } catch (erro) {
      resultado.innerHTML = `
        <h2>Não foi possível concluir a submissão</h2>
        <p>${escapar(erro.message || 'Erro desconhecido.')}</p>
        <p class="resultado-rodape">
          Verifique os dados introduzidos e tente novamente.
          Se o problema persistir, contacte a comissão organizadora.
        </p>
      `;
      resultado.classList.add('erro');
      resultado.hidden = false;
      resultado.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } finally {
      botao.disabled = false;
      botao.querySelector('span').textContent = 'Submeter Trabalho';
    }
  });

  function escapar(txt) {
    const d = document.createElement('div');
    d.textContent = txt == null ? '' : String(txt);
    return d.innerHTML;
  }

})();