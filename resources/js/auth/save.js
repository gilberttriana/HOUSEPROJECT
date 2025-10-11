document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('loginForm1');

    // Función para mostrar alertas tipo toast
    function showAlert(container, message, type = 'success', duration = 3000) {
        const alert = document.createElement('div');
        alert.className = `alert-message alert-${type} alert-show`;

        // Icono según el tipo
        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        alert.appendChild(icon);

        const text = document.createElement('span');
        text.textContent = message;
        alert.appendChild(text);

        container.appendChild(alert);

        setTimeout(() => {
            alert.classList.remove('alert-show');
            setTimeout(() => container.removeChild(alert), 400);
        }, duration);
    }

    // LOGIN
    if (loginForm) {
        const container = document.getElementById('loginAlertContainer');
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(loginForm);

            fetch(loginForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                showAlert(container, data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    // Redirigir si es correcto
                    setTimeout(() => window.location.href = data.redirect, 1000);
                } else {
                    // Limpiar campos si hay error
                    loginForm.reset();
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showAlert(container, 'Error en el servidor o credenciales incorrectas.', 'error');
                loginForm.reset();
            });
        });
    }

    // REGISTRO
    if (registerForm) {
        const container = document.getElementById('registerAlertContainer');
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(registerForm);

            fetch(registerForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                showAlert(container, data.message || 'Error al registrar.', data.success ? 'success' : 'error');
                if (data.success) {
                    // Redirigir si es correcto
                    setTimeout(() => window.location.href = data.redirect, 1000);
                } else {
                    // Limpiar campos si hay error
                    registerForm.reset();
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showAlert(container, 'Error en el servidor.', 'error');
                registerForm.reset();
            });
        });
    }
});
