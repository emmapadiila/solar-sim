// Login y registro (index.php). Esta página no carga app.js.
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginMensaje = document.getElementById('loginMensaje');
    const registroMensaje = document.getElementById('registroMensaje');

    async function postJson(url, body) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        return response.json();
    }

    function mostrarMensaje(elemento, texto, tipo = 'error') {
        elemento.textContent = texto;
        elemento.className = `form-message form-message--${tipo} is-visible`;
    }

    function ocultarMensajes() {
        [loginMensaje, registroMensaje].forEach(el => el.classList.remove('is-visible'));
    }

    function mostrarFormulario(mostrar, ocultar) {
        ocultarMensajes();
        ocultar.classList.add('oculto');
        mostrar.classList.remove('oculto');
        mostrar.querySelector('input').focus();
    }

    function bloquear(form, bloqueado) {
        form.querySelector('button[type="submit"]').disabled = bloqueado;
    }

    document.getElementById('showRegister').addEventListener('click', function(e) {
        e.preventDefault();
        mostrarFormulario(registerForm, loginForm);
    });

    document.getElementById('showLogin').addEventListener('click', function(e) {
        e.preventDefault();
        mostrarFormulario(loginForm, registerForm);
    });

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const nombre = document.getElementById('loginNombre').value.trim();
        const password = document.getElementById('loginPassword').value;

        if (!nombre || !password) {
            mostrarMensaje(loginMensaje, 'Escribe tu usuario y tu contraseña.');
            return;
        }

        bloquear(loginForm, true);
        try {
            const data = await postJson('api/auth/login.php', { nombre, password });
            if (data.success) {
                window.location.href = data.redirect || 'dashboard.php';
                return;
            }
            mostrarMensaje(loginMensaje, data.message || 'No se pudo iniciar sesión.');
        } catch (error) {
            console.error('Error:', error);
            mostrarMensaje(loginMensaje, 'No se pudo conectar con el servidor. Intenta de nuevo.');
        }
        bloquear(loginForm, false);
    });

    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = {
            nombre: document.getElementById('regNombre').value.trim(),
            contrasena: document.getElementById('regPassword').value,
            direccion: document.getElementById('regDireccion').value.trim(),
            edad: parseInt(document.getElementById('regEdad').value, 10)
        };

        if (!formData.nombre || !formData.contrasena || !formData.direccion) {
            mostrarMensaje(registroMensaje, 'Completa todos los campos.');
            return;
        }
        if (formData.contrasena.trim().length < 6) {
            mostrarMensaje(registroMensaje, 'La contraseña debe tener al menos 6 caracteres.');
            return;
        }
        if (isNaN(formData.edad) || formData.edad < 1 || formData.edad > 120) {
            mostrarMensaje(registroMensaje, 'Ingresa una edad válida.');
            return;
        }

        bloquear(registerForm, true);
        try {
            const data = await postJson('api/auth/registro.php', formData);
            if (data.success) {
                // El servidor ya inició la sesión: entrar directamente
                window.location.href = data.redirect || 'dashboard.php';
                return;
            } else {
                mostrarMensaje(registroMensaje, data.message || 'No se pudo crear la cuenta.');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarMensaje(registroMensaje, 'No se pudo conectar con el servidor. Intenta de nuevo.');
        }
        bloquear(registerForm, false);
    });
});
