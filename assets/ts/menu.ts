(() => {
  const button = document.querySelector<HTMLButtonElement>("#menuToggle");
  const menu = document.querySelector<HTMLElement>(".menu");

  if (!button || !menu) return;

  const setOpen = (open: boolean): void => {
    menu.classList.toggle("aberto", open);
    button.classList.toggle("aberto", open);
    button.setAttribute("aria-expanded", String(open));
    button.setAttribute("aria-label", open ? "Fechar menu" : "Abrir menu");
  };

  button.addEventListener("click", () => {
    setOpen(button.getAttribute("aria-expanded") !== "true");
  });

  menu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => setOpen(false));
  });

  document.addEventListener("keydown", (event: KeyboardEvent) => {
    if (event.key === "Escape" && button.getAttribute("aria-expanded") === "true") {
      setOpen(false);
      button.focus();
    }
  });

  document.addEventListener("click", (event: MouseEvent) => {
    if (!button.contains(event.target as Node) && !menu.contains(event.target as Node)) {
      setOpen(false);
    }
  });

  window.addEventListener("resize", () => {
    if (window.innerWidth > 1300 && button.getAttribute("aria-expanded") === "true") {
      setOpen(false);
    }
  });
})();