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

  if (tipo) {
    tipo.addEventListener('change', () => {
      const eInova = tipo.value === 'projeto_inova';
      if (camposInova) camposInova.hidden = !eInova;
      if (campoEixo) campoEixo.style.opacity = eInova ? 0.4 : 1;
    });
  }

  if (resumo && contador) {
    resumo.addEventListener('input', () => {
      contador.textContent = resumo.value.length;
      contador.style.color = resumo.value.length > 2000 ? '#FF6B6F' : '';
    });
  }

  form.addEventListener('submit', async (evento) => {
    evento.preventDefault();

    if (mensagem) {
      mensagem.hidden = true;
      mensagem.classList.remove('erro');
      mensagem.innerHTML = '';
    }

    botao.disabled = true;
    botao.textContent = 'A enviar...';

    try {
      const resposta = await fetch('submeter.php', {
        method: 'POST',
        body: new FormData(form)
      });

      const dados = await resposta.json();

      if (!resposta.ok) {
        throw new Error(dados.erro || 'Não foi possível submeter.');
      }

      // Limpar o formulário
      form.reset();
      if (contador) contador.textContent = '0';
      if (camposInova) camposInova.hidden = true;

      // Mostrar mensagem de sucesso bonita
      const tituloEvento = dados.evento || 'Jornadas Técnico-Científicas do IPS 2026';

      mensagem.innerHTML =
        '<h3>Submissão recebida com sucesso</h3>' +
        '<p class="mensagem-numero">Número de registo #' + dados.id + '</p>' +
        '<p class="mensagem-corpo">' +
          'O seu trabalho foi registado no sistema das ' + tituloEvento + '. ' +
          'Guarde o número de registo — vai precisar dele para qualquer consulta posterior.' +
        '</p>' +
        '<p class="mensagem-rodape">' +
          'A comissão organizadora irá analisar a submissão e comunicará a decisão através do email indicado. ' +
          'Se não receber notificação no prazo de duas semanas, contacte a organização.' +
        '</p>';

      mensagem.hidden = false;

      // Scroll suave até à mensagem
      mensagem.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } catch (erro) {
      mensagem.innerHTML =
        '<h3>Não foi possível concluir a submissão</h3>' +
        '<p class="mensagem-corpo">' + (erro.message || 'Erro desconhecido.') + '</p>' +
        '<p class="mensagem-rodape">Verifique os dados introduzidos e tente novamente. ' +
        'Se o problema persistir, contacte a comissão organizadora.</p>';
      mensagem.classList.add('erro');
      mensagem.hidden = false;

      mensagem.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } finally {
      botao.disabled = false;
      botao.textContent = 'Submeter Trabalho';
    }
  });

})();