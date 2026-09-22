// Validación del formulario de registro
function validarRegistro() {
    const nombre = document.getElementById('regNombre').value;
    const contrasena = document.getElementById('regPassword').value;
    const direccion = document.getElementById('regDireccion').value;
    const edad = document.getElementById('regEdad').value;
    
    if (!nombre || !contrasena || !direccion || !edad) {
        alert('Todos los campos son obligatorios');
        return false;
    }
    
    if (contrasena.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }
    
    if (edad < 1 || edad > 120) {
        alert('Edad inválida');
        return false;
    }
    
    return true;
}

// Validación del formulario de login
function validarLogin() {
    const nombre = document.getElementById('loginNombre').value;
    const contrasena = document.getElementById('loginPassword').value;
    
    if (!nombre || !contrasena) {
        alert('Todos los campos son obligatorios');
        return false;
    }
    
    return true;
}

// Función para manejar el registro
async function registrarUsuario(event) {
    event.preventDefault();
    
    if (!validarRegistro()) {
        return;
    }
    
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('registro_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            window.location.href = 'login.html';
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('Error al registrar usuario');
    }
}

// Función para manejar el login
async function iniciarSesion(event) {
    event.preventDefault();
    
    if (!validarLogin()) {
        return;
    }
    
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('login_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = data.rol === 'admin' ? 'admin/dashboard.html' : 'usuario/dashboard.html';
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('Error al iniciar sesión');
    }
}

// Función para cerrar sesión
async function cerrarSesion() {
    try {
        const response = await fetch('logout.php');
        const data = await response.json();
        
        if (data.success) {
            window.location.href = 'login.html';
        }
    } catch (error) {
        alert('Error al cerrar sesión');
    }
} 