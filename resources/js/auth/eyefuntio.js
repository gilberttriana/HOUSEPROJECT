// Seleccionar elementos
const input = document.getElementById("contraseña");
const eye = document.querySelector("#MOOD i"); // <-- aquí usamos querySelector
const wrapper = document.querySelector(".password-wrapper");

function togglePasswordVisibility() {
  if (input.type === "password") {
    input.type = "text";
    eye.classList.remove("fa-eye-slash");
    eye.classList.add("fa-eye");
  } else {
    input.type = "password";
    eye.classList.remove("fa-eye");
    eye.classList.add("fa-eye-slash");
  }
}

// Cerrar si hago click fuera del formulario
document.addEventListener("click", (e) => {
  if (!wrapper.contains(e.target)) {
    input.type = "password";
    eye.classList.remove("fa-eye");
    eye.classList.add("fa-eye-slash");
  }
});

// Cerrar si hago scroll
window.addEventListener("scroll", () => {
  input.type = "password";
  eye.classList.remove("fa-eye");
  eye.classList.add("fa-eye-slash");
});
