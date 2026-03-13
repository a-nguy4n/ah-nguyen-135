
const dropdowns = document.querySelectorAll(".dropdown");

function closeAllDropdowns(except = null){
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

        closeAllDropdowns(dropdown);

        dropdown.classList.toggle("open", !isOpen);
        btn.setAttribute("aria-expanded", String(!isOpen));
    });
});

document.addEventListener("click", (e) => {
    if (!e.target.closest(".dropdown")) {
        closeAllDropdowns();
    }
});

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeAllDropdowns();
});

