
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-confirm]").forEach((button) => {
        button.addEventListener("click", (event) => {
            const message = button.dataset.confirm || "Continue?";

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll("[data-mood]").forEach((button) => {
        button.addEventListener("click", () => {
            document.querySelectorAll("[data-mood]").forEach((item) => {
                item.classList.remove("active");
                item.setAttribute("aria-pressed", "false");
            });

            button.classList.add("active");
            button.setAttribute("aria-pressed", "true");

            const field = document.querySelector("#selectedMood");
            if (field) {
                field.value = button.dataset.mood;
            }
        });
    });
});
