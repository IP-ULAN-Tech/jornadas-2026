(function () {
  'use strict';

  const form = document.getElementById('formLogin');
  const botao = document.getElementById('botaoEntrar');
  const mensagem = document.getElementById('mensagemLogin');

  form.addEventListener('submit', async (evento) => {
    evento.preventDefault();

    botao.disabled = true;
    botao.textContent = 'A verificar...';
    mensagem.hidden = true;

    try {
      const resposta = await fetch('../api/admin.php?acao=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
          email:    document.getElementById('email').value,
          password: document.getElementById('password').value
        })
      });

      const dados = await resposta.json();
      if (!resposta.ok) throw new Error(dados.erro || 'Falha ao autenticar.');

      window.location.href = 'dashboard.php';

    } catch (erro) {
      mensagem.textContent = erro.message;
      mensagem.hidden = false;
      botao.disabled = false;
      botao.textContent = 'Entrar';
    }
  });
})();