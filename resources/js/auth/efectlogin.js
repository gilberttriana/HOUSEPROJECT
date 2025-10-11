const loginModal = document.getElementById('loginModal');
const modalContent = loginModal.querySelector('.modal-content');
const closeBtn = loginModal.querySelector('.close-btn');

// Abrir modal
document.querySelectorAll('.login-btn').forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.preventDefault();
    loginModal.classList.add('show');
    loginModal.style.display = 'flex';
  });
});

// Función para cerrar con animación
function closeModal() {
  modalContent.style.transform = 'scale(0.8)';
  modalContent.style.opacity = '0';

  setTimeout(() => {
    loginModal.style.display = 'none';
    modalContent.style.transform = 'scale(1)';
    modalContent.style.opacity = '1';
    document.getElementById('loginForm').reset(); // borra los inputs
    document.getElementById('loginMessage').textContent = ''; 
  }, 300); // igual al transition del CSS
}

// Cerrar con la X
closeBtn.addEventListener('click', closeModal);

// Cerrar al hacer click fuera del contenido
loginModal.addEventListener('click', function (e) {
  if (e.target === loginModal) {
    closeModal();
  }
});
// Cerrar modal al hacer clic en cualquier link de navegación
document.querySelectorAll('nav a').forEach(function(link){
    link.addEventListener('click', function(){
        closeModal();
    });
});
window.addEventListener('scroll', function() {
    const modal = document.getElementById('loginModal');
    if(modal.classList.contains('show')){
        closeModal();
    }
});

 