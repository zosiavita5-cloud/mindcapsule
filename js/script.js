
document.addEventListener("DOMContentLoaded", () => {
    // Confirmation dialogs
    document.querySelectorAll("[data-confirm]").forEach((button) => {
        button.addEventListener("click", (event) => {
            const message = button.dataset.confirm || "Continue?";

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // Mood selection
    const moodButtons = document.querySelectorAll("[data-mood]");
    const selectedMoodField = document.querySelector("#selectedMood");

    moodButtons.forEach((button) => {
        button.addEventListener("click", () => {
            moodButtons.forEach((item) => {
                item.classList.remove("active");
                item.setAttribute("aria-pressed", "false");
            });

            button.classList.add("active");
            button.setAttribute("aria-pressed", "true");

            if (selectedMoodField) {
                selectedMoodField.value = button.dataset.mood || "";
            }
        });
    });
});
