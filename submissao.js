(function () {
  'use strict';
  const form = document.getElementById('formSubmissao');
  if (!form) return;

  const tipo = document.getElementById('tipo');
  const campoEixo = document.getElementById('campoEixo');
  const camposInova = document.getElementById('camposInova');
  const resumo = document.getElementById('resumo');
  const contador = document.getElementById('contadorResumo');
  const botao = document.getElementById('botaoSubmeter');
  const mensagem = document.getElementById('formMensagem');

  tipo.addEventListener('change', () => {
    const eInova = tipo.value === 'projeto_inova';
    camposInova.hidden = !eInova;
    campoEixo.style.opacity = eInova ? 0.4 : 1;
  });

  resumo.addEventListener('input', () => contador.textContent = resumo.value.length);

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();
    mensagem.hidden = true;
    mensagem.classList.remove('erro');
    botao.disabled = true;
    botao.textContent = 'A enviar...';

    try {
      const r = await fetch('submeter.php', { method: 'POST', body: new FormData(form) });
      const d = await r.json();
      if (!r.ok) throw new Error(d.erro || 'Não foi possível submeter.');

      mensagem.textContent = 'Submissão recebida com o número #' + d.id + '. ' + d.mensagem;
      form.reset();
      contador.textContent = '0';
      camposInova.hidden = true;
    } catch (err) {
      mensagem.textContent = err.message;
      mensagem.classList.add('erro');
      mensagem.hidden = false;
    } finally {
      botao.disabled = false;
      botao.textContent = 'Submeter Trabalho';
    }
  });
})();
