"use strict";
(() => {
    const button = document.querySelector("#menuToggle");
    const menu = document.querySelector(".menu");
    if (!button || !menu)
        return;
    const setOpen = (open) => {
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
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && button.getAttribute("aria-expanded") === "true") {
            setOpen(false);
            button.focus();
        }
    });
    document.addEventListener("click", (event) => {
        if (!button.contains(event.target) && !menu.contains(event.target)) {
            setOpen(false);
        }
    });
    window.addEventListener("resize", () => {
        if (window.innerWidth > 1300 && button.getAttribute("aria-expanded") === "true") {
            setOpen(false);
        }
    });
})();
