
const dropdowns = document.querySelectorAll(".dropdown");

function closeAllDropdowns(except = null) {
    dropdowns.forEach(d => {
        if (d !== except) {
        d.classList.remove("open");
        const btn = d.querySelector(".dropdown-toggle");
        if (btn) btn.setAttribute("aria-expanded", "false");
        }
    });
}

dropdowns.forEach(dropdown => {
    const btn = dropdown.querySelector(".dropdown-toggle");
    if (!btn) return;

    btn.addEventListener("click", (e) => {
        e.preventDefault();

        const isOpen = dropdown.classList.contains("open");

        // close all others first
        closeAllDropdowns(dropdown);

        // toggle this one
        dropdown.classList.toggle("open", !isOpen);
        btn.setAttribute("aria-expanded", String(!isOpen));
    });
});

// click outside closes all
document.addEventListener("click", (e) => {
    if (!e.target.closest(".dropdown")) {
        closeAllDropdowns();
    }
});

// optional: ESC closes all
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeAllDropdowns();
});

