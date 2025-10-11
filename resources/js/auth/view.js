document.addEventListener('DOMContentLoaded', () => {

    const toggles = document.querySelectorAll(".toggle-password");

    toggles.forEach(toggleBtn => {
        toggleBtn.addEventListener("click", () => {
            const passwordInput = toggleBtn.previousElementSibling; // asume que el input está justo antes
            const icon = toggleBtn.querySelector("i");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        });
    });

});
