// Login y registro (index.php). Esta página no carga app.js.
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    async function postJson(url, body) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        return response.json();
    }

    function mostrarFormulario(mostrar, ocultar) {
        ocultar.classList.add('oculto');
        mostrar.classList.remove('oculto');
    }

    // Alternar entre login y registro
    document.getElementById('showRegister').addEventListener('click', function(e) {
        e.preventDefault();
        mostrarFormulario(registerForm, loginForm);
    });

    document.getElementById('showLogin').addEventListener('click', function(e) {
        e.preventDefault();
        mostrarFormulario(loginForm, registerForm);
    });

    // Manejar login
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const nombre = document.getElementById('loginNombre').value.trim();
        const password = document.getElementById('loginPassword').value;

        try {
            const data = await postJson('api/auth/login.php', { nombre, password });
            if (data.success) {
                window.location.href = 'dashboard.php';
            } else {
                alert(data.message || 'Error al iniciar sesión');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
        }
    });

    // Manejar registro
    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = {
            nombre: document.getElementById('regNombre').value.trim(),
            contrasena: document.getElementById('regPassword').value,
            direccion: document.getElementById('regDireccion').value.trim(),
            edad: parseInt(document.getElementById('regEdad').value, 10)
        };

        if (!formData.nombre || !formData.contrasena || !formData.direccion) {
            alert('Por favor complete todos los campos');
            return;
        }

        if (formData.contrasena.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres');
            return;
        }

        if (isNaN(formData.edad) || formData.edad < 1 || formData.edad > 120) {
            alert('Por favor ingrese una edad válida');
            return;
        }

        try {
            const data = await postJson('api/auth/registro.php', formData);

            if (data.success) {
                alert('Registro exitoso. Por favor inicia sesión.');
                registerForm.reset();
                mostrarFormulario(loginForm, registerForm);
            } else {
                alert(data.message || 'Error al registrar');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
        }
    });
});
