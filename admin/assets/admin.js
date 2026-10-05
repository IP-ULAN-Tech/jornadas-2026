(function () {
  'use strict';

  const menuAdmin = document.getElementById('menuAdmin');
  const menuAdminToggle = document.getElementById('menuAdminToggle');

  if (menuAdmin && menuAdminToggle) {
    const setMenuAdmin = (aberto) => {
      menuAdmin.classList.toggle('aberto', aberto);
      menuAdminToggle.setAttribute('aria-expanded', String(aberto));
      menuAdminToggle.setAttribute('aria-label', aberto ? 'Fechar menu administrativo' : 'Abrir menu administrativo');
    };

    menuAdminToggle.addEventListener('click', () => {
      setMenuAdmin(menuAdminToggle.getAttribute('aria-expanded') !== 'true');
    });

    menuAdmin.querySelectorAll('.barra-lateral-menu a').forEach((link) => {
      link.addEventListener('click', () => setMenuAdmin(false));
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && menuAdminToggle.getAttribute('aria-expanded') === 'true') {
        setMenuAdmin(false);
        menuAdminToggle.focus();
      }
    });

    document.addEventListener('click', (event) => {
      if (!menuAdmin.contains(event.target)) setMenuAdmin(false);
    });

    window.addEventListener('resize', () => {
      if (window.innerWidth > 860) setMenuAdmin(false);
    });
  }

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
