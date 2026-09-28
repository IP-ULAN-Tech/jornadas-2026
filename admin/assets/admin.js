(function () {
  'use strict';

  // Confirmação de eliminação
  document.querySelectorAll('[data-confirmar]').forEach((el) => {
    el.addEventListener('click', (ev) => {
      const msg = el.dataset.confirmar || 'Tem a certeza?';
      if (!confirm(msg)) ev.preventDefault();
    });
  });

  // Pré-visualização de imagens
  document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
    input.addEventListener('change', () => {
      const alvo = document.querySelector(input.dataset.preview);
      if (!alvo) return;
      const ficheiro = input.files && input.files[0];
      if (!ficheiro) return;
      const url = URL.createObjectURL(ficheiro);
      if (alvo.tagName === 'IMG') {
        alvo.src = url;
      } else {
        alvo.innerHTML = '';
        const img = document.createElement('img');
        img.src = url;
        img.style.maxWidth = '200px';
        img.style.border = '1px solid #E1E5EB';
        alvo.appendChild(img);
      }
    });
  });

  // Fechar modais com Escape
  document.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape') {
      document.querySelectorAll('.modal-fundo').forEach((m) => m.hidden = true);
    }
  });

})();
