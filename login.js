document.addEventListener('DOMContentLoaded', function() {
    // Alternar entre login y registro
    document.getElementById('showRegister').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('loginForm').classList.add('oculto');
        document.getElementById('registerForm').classList.remove('oculto');
    });

    document.getElementById('showLogin').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('registerForm').classList.add('oculto');
        document.getElementById('loginForm').classList.remove('oculto');
    });

    // Manejar login
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const nombre = document.getElementById('loginNombre').value;
        const password = document.getElementById('loginPassword').value;

        try {
            const response = await fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ nombre, password })
            });

            const data = await response.json();
            if (data.success) {
                localStorage.setItem('usuario', JSON.stringify(data.usuario));
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
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            nombre: document.getElementById('regNombre').value.trim(),
            contrasena: document.getElementById('regPassword').value.trim(),
            direccion: document.getElementById('regDireccion').value.trim(),
            edad: parseInt(document.getElementById('regEdad').value)
        };

        if (!formData.nombre || !formData.contrasena || !formData.direccion || !formData.edad) {
            alert('Por favor complete todos los campos');
            return;
        }

        if (isNaN(formData.edad) || formData.edad < 1) {
            alert('Por favor ingrese una edad válida');
            return;
        }

        try {
            const response = await fetch('registro_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            
            if (data.success) {
                alert('Registro exitoso. Por favor inicia sesión.');
                document.getElementById('registerForm').reset();
                document.getElementById('registerForm').classList.add('oculto');
                document.getElementById('loginForm').classList.remove('oculto');
            } else {
                alert(data.message || 'Error al registrar');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
        }
    });
}); 