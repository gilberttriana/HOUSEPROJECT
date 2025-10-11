// MODAL LOGIN
const loginModal = document.getElementById('loginModal');
const loginContent = loginModal.querySelector('.modal-content');
const closeLoginBtn = loginModal.querySelector('.close-btn');

// MODAL REGISTRO
const registerModal = document.getElementById('loginModal1');
const registerContent = registerModal.querySelector('.modal-content1');
const closeRegisterBtn = registerModal.querySelector('.close-btn');

// FUNCIONES DE CIERRE
function closeModal() {
    loginContent.style.transform = 'scale(0.8)';
    loginContent.style.opacity = '0';
    setTimeout(() => {
        loginModal.style.display = 'none';
        loginContent.style.transform = 'scale(1)';
        loginContent.style.opacity = '1';
    }, 300);
}

function closeModal1() {
    registerContent.style.transform = 'scale(0.8)';
    registerContent.style.opacity = '0';
    setTimeout(() => {
        registerModal.style.display = 'none';
        registerContent.style.transform = 'scale(1)';
        registerContent.style.opacity = '1';

    document.getElementById('loginForm1').reset(); // borra los inputs
    document.getElementById('loginMessage1').textContent = ''; 
    }, 300);
}

// EVENTOS CIERRE LOGIN
closeLoginBtn.addEventListener('click', closeModal);
loginModal.addEventListener('click', e => { if(e.target === loginModal) closeModal(); });

// EVENTOS CIERRE REGISTRO
closeRegisterBtn.addEventListener('click', closeModal1);
registerModal.addEventListener('click', e => { if(e.target === registerModal) closeModal1(); });

// ABRIR LOGIN
document.querySelectorAll('.login-btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        loginModal.classList.add('show');
        loginModal.style.display = 'flex';
    });
});

// ABRIR REGISTRO desde LOGIN
document.querySelectorAll('.registri-btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        // Abrir registro
        registerModal.classList.add('show');
        registerModal.style.display = 'flex';
        // Cerrar login si está abierto
        if(loginModal.classList.contains('show')){
            closeModal();
        }
    });
});

// OPCIONAL: cerrar ambos al hacer scroll
window.addEventListener('scroll', () => {
    if(loginModal.classList.contains('show')) closeModal();
    if(registerModal.classList.contains('show')) closeModal1();
});


