<?php
require __DIR__ . '/../src/bootstrap.php';

if (Auth::check()) {
    header('Location: dashboard.php');
    exit;
}

render('layout/head', ['titulo' => 'Iniciar Sesión']);
?>
<body>
    <div class="login-container">
        <div class="auth-box">
            <div class="auth-header">
                <i class="fas fa-solar-panel logo-icon"></i>
                <h1>Sistema de Paneles Solares</h1>
                <p>Control y gestión de energía solar</p>
            </div>

            <form id="loginForm" class="auth-form">
                <h2>Iniciar Sesión</h2>
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="loginNombre" name="nombre" placeholder="Nombre de usuario" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="loginPassword" name="password" placeholder="Contraseña" required>
                </div>
                <div class="form-options">
                    <label>
                        <input type="checkbox"> Recordarme
                    </label>
                    <a href="#">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="auth-btn">Ingresar <i class="fas fa-arrow-right"></i></button>
                <p class="auth-toggle">¿No tienes cuenta? <a href="#" id="showRegister">Regístrate</a></p>
            </form>

            <form id="registerForm" class="auth-form oculto">
                <h2>Crear Cuenta</h2>
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="regNombre" name="nombre" placeholder="Nombre completo" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="regPassword" name="contrasena" placeholder="Contraseña (mínimo 6 caracteres)" minlength="6" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-home"></i>
                    <input type="text" id="regDireccion" name="direccion" placeholder="Dirección" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-birthday-cake"></i>
                    <input type="number" id="regEdad" name="edad" placeholder="Edad" min="1" required>
                </div>
                <button type="submit" class="auth-btn">Registrarse <i class="fas fa-user-plus"></i></button>
                <p class="auth-toggle">¿Ya tienes cuenta? <a href="#" id="showLogin">Inicia sesión</a></p>
            </form>
        </div>
    </div>
    <script src="assets/js/login.js"></script>
</body>
</html>
