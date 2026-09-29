(function () {
  'use strict';

  const API = '../api/admin.php';
  const CSRF = document.querySelector('meta[name="csrf"]').content;

  const corpoTabela  = document.getElementById('corpoTabela');
  const textoVazio   = document.getElementById('textoVazio');
  const estatisticas = document.getElementById('estatisticas');
  const busca        = document.getElementById('busca');
  const filtroEstado = document.getElementById('filtroEstado');
  const filtroTipo   = document.getElementById('filtroTipo');
  const modalFundo   = document.getElementById('modalFundo');
  const modalConteudo= document.getElementById('modalConteudo');
  const modalFechar  = document.getElementById('modalFechar');

  const rotulosEstado = {
    pendente: 'Pendente', em_analise: 'Em análise',
    aceite: 'Aceite', rejeitado: 'Rejeitado'
  };
  const rotulosTipo = {
    comunicacao: 'Comunicação', poster: 'Poster', projeto_inova: 'INOVA IPS'
  };

  let temporizador = null;

  function escapar(txt) {
    const d = document.createElement('div');
    d.textContent = txt == null ? '' : String(txt);
    return d.innerHTML;
  }

  async function carregarEstatisticas() {
    const r = await fetch(`${API}?acao=estatisticas`, { credentials: 'same-origin' });
    if (!r.ok) return;
    const d = await r.json();
    const mapa = {};
    d.porEstado.forEach(l => mapa[l.status] = l.total);

    estatisticas.innerHTML = `
      <div class="estat"><p class="estat-rotulo">Total</p><p class="estat-num">${d.total}</p></div>
      <div class="estat"><p class="estat-rotulo">Pendentes</p><p class="estat-num">${mapa.pendente || 0}</p></div>
      <div class="estat"><p class="estat-rotulo">Em análise</p><p class="estat-num">${mapa.em_analise || 0}</p></div>
      <div class="estat"><p class="estat-rotulo">Aceites</p><p class="estat-num">${mapa.aceite || 0}</p></div>
      <div class="estat"><p class="estat-rotulo">Rejeitadas</p><p class="estat-num">${mapa.rejeitado || 0}</p></div>
    `;
  }

  async function carregarLista() {
    const params = new URLSearchParams({ acao: 'listar' });
    if (busca.value.trim())   params.set('q', busca.value.trim());
    if (filtroEstado.value)   params.set('status', filtroEstado.value);
    if (filtroTipo.value)     params.set('tipo', filtroTipo.value);

    const r = await fetch(`${API}?${params}`, { credentials: 'same-origin' });
    const d = await r.json();

    corpoTabela.innerHTML = '';

    if (!d.submissoes || !d.submissoes.length) {
      textoVazio.hidden = false;
      return;
    }
    textoVazio.hidden = true;

    d.submissoes.forEach(s => {
      const autor = (s.autores || '').split(';')[0].trim();
      const data  = new Date(s.criado_em.replace(' ', 'T')).toLocaleDateString('pt-PT');

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${s.id}</td>
        <td>${data}</td>
        <td>${rotulosTipo[s.tipo] || s.tipo}</td>
        <td class="col-titulo">${escapar(s.titulo)}</td>
        <td>${escapar(autor)}</td>
        <td><span class="etiqueta etiqueta-${s.status}">${rotulosEstado[s.status]}</span></td>
        <td><button class="botao-ver" data-id="${s.id}">Ver</button></td>
      `;
      corpoTabela.appendChild(tr);
    });

    corpoTabela.querySelectorAll('.botao-ver').forEach(b => {
      b.addEventListener('click', () => abrirDetalhe(b.dataset.id));
    });
  }

  async function abrirDetalhe(id) {
    const r = await fetch(`${API}?acao=detalhe&id=${id}`, { credentials: 'same-origin' });
    const s = await r.json();

    modalConteudo.innerHTML = `
      <p class="modal-rotulo">Submissão #${s.id}</p>
      <h2 class="modal-titulo">${escapar(s.titulo)}</h2>

      <dl class="modal-dados">
        <div><dt>Tipo</dt><dd>${rotulosTipo[s.tipo] || s.tipo}</dd></div>
        ${s.eixo ? `<div><dt>Eixo</dt><dd>${escapar(s.eixo)}</dd></div>` : ''}
        <div><dt>Autores</dt><dd>${escapar(s.autores)}</dd></div>
        ${s.instituicao ? `<div><dt>Instituição</dt><dd>${escapar(s.instituicao)}</dd></div>` : ''}
        <div><dt>Email</dt><dd><a href="mailto:${s.email}">${s.email}</a></dd></div>
        ${s.telefone ? `<div><dt>Telefone</dt><dd>${escapar(s.telefone)}</dd></div>` : ''}
        <div><dt>Submetido em</dt><dd>${s.criado_em}</dd></div>
        ${s.area_inova ? `<div><dt>Área INOVA</dt><dd>${escapar(s.area_inova)}</dd></div>` : ''}
        ${s.elementos_equipa ? `<div><dt>Equipa</dt><dd>${escapar(s.elementos_equipa)}</dd></div>` : ''}
      </dl>

      <h3 class="modal-subtitulo">Resumo</h3>
      <p class="modal-resumo">${escapar(s.resumo)}</p>

      ${s.palavras_chave ? `<p class="modal-palavras"><strong>Palavras-chave:</strong> ${escapar(s.palavras_chave)}</p>` : ''}

      ${s.ficheiro_guardado
        ? `<a href="../api/ficheiro.php?id=${s.id}" target="_blank" class="botao-ficheiro">Abrir PDF (${escapar(s.ficheiro_original)})</a>`
        : '<p class="modal-aviso">Sem ficheiro anexado.</p>'}

      <div class="modal-acoes">
        <label>Estado</label>
        <select id="modalEstado">
          <option value="pendente"   ${s.status === 'pendente'   ? 'selected' : ''}>Pendente</option>
          <option value="em_analise" ${s.status === 'em_analise' ? 'selected' : ''}>Em análise</option>
          <option value="aceite"     ${s.status === 'aceite'     ? 'selected' : ''}>Aceite</option>
          <option value="rejeitado"  ${s.status === 'rejeitado'  ? 'selected' : ''}>Rejeitado</option>
        </select>

        <label>Observações internas</label>
        <textarea id="modalObs" rows="3">${escapar(s.observacoes || '')}</textarea>

        <button class="botao-guardar" id="modalGuardar">Guardar alterações</button>
      </div>
    `;

    modalFundo.hidden = false;

    document.getElementById('modalGuardar').addEventListener('click', async () => {
      await fetch(`${API}?acao=atualizar&id=${s.id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
          csrf: CSRF,
          status: document.getElementById('modalEstado').value,
          observacoes: document.getElementById('modalObs').value
        })
      });
      modalFundo.hidden = true;
      carregarLista();
      carregarEstatisticas();
    });
  }

  modalFechar.addEventListener('click', () => modalFundo.hidden = true);
  modalFundo.addEventListener('click', (e) => {
    if (e.target === modalFundo) modalFundo.hidden = true;
  });

  busca.addEventListener('input', () => {
    clearTimeout(temporizador);
    temporizador = setTimeout(carregarLista, 300);
  });
  filtroEstado.addEventListener('change', carregarLista);
  filtroTipo.addEventListener('change', carregarLista);

  document.getElementById('botaoSair').addEventListener('click', async () => {
    await fetch(`${API}?acao=logout`, { method: 'POST', credentials: 'same-origin' });
    window.location.href = 'login.php';
  });

  carregarEstatisticas();
  carregarLista();

})();