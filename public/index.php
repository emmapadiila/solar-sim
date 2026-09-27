<?php
require __DIR__ . '/../src/bootstrap.php';

if (Auth::check()) {
    header('Location: dashboard.php');
    exit;
}

$aviso = flash_pull();

render('layout/head', ['titulo' => 'Iniciar sesión']);
?>
<body>
    <div class="auth">
        <aside class="auth__aside">
            <a href="index.php" class="logo">
                <span class="logo__mark"><i class="fas fa-sun"></i></span>
                <span><?= e(config('app.nombre')) ?></span>
            </a>

            <div>
                <h1 class="auth__headline">Transforma la energía del sol en <span>ahorro real</span></h1>
                <p class="auth__lead">Simula cuántos paneles necesita tu hogar, cuánto ahorrarías en tu factura y en cuánto tiempo recuperas la inversión.</p>
            </div>

            <ul class="auth__features">
                <li><i class="fas fa-calculator"></i> Simulación según tu ciudad, estrato y consumo</li>
                <li><i class="fas fa-chart-line"></i> Ahorro mensual, anual y retorno de inversión</li>
                <li><i class="fas fa-clock-rotate-left"></i> Historial de tus simulaciones</li>
            </ul>
        </aside>

        <main class="auth__main">
            <div class="auth__panel">
                <form id="loginForm" novalidate>
                    <h2 class="auth__title">Iniciar sesión</h2>
                    <p class="auth__subtitle">Ingresa con tu usuario para continuar.</p>

                    <?php if ($aviso): ?>
                        <div class="form-message form-message--<?= e($aviso['tipo']) ?> is-visible" id="loginMensaje" role="alert"><?= e($aviso['mensaje']) ?></div>
                    <?php else: ?>
                        <div class="form-message form-message--error" id="loginMensaje" role="alert"></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label" for="loginNombre">Usuario</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="loginNombre" name="nombre" class="form-control" placeholder="Tu nombre de usuario" autocomplete="username" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="loginPassword">Contraseña</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Tu contraseña" autocomplete="current-password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn--dark btn--lg btn--block">Ingresar <i class="fas fa-arrow-right"></i></button>
                    <p class="auth__switch">¿No tienes cuenta? <a href="#" id="showRegister">Crear cuenta</a></p>
                </form>

                <form id="registerForm" class="oculto" novalidate>
                    <h2 class="auth__title">Crear cuenta</h2>
                    <p class="auth__subtitle">Regístrate gratis para guardar tus simulaciones.</p>

                    <div class="form-message form-message--error" id="registroMensaje" role="alert"></div>

                    <div class="form-group">
                        <label class="form-label" for="regNombre">Usuario</label>
                        <input type="text" id="regNombre" name="nombre" class="form-control" maxlength="50" autocomplete="username" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="regPassword">Contraseña</label>
                        <input type="password" id="regPassword" name="contrasena" class="form-control" minlength="6" autocomplete="new-password" required>
                        <small class="form-hint">Mínimo 6 caracteres.</small>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="regDireccion">Dirección</label>
                            <input type="text" id="regDireccion" name="direccion" class="form-control" autocomplete="street-address" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="regEdad">Edad</label>
                            <input type="number" id="regEdad" name="edad" class="form-control" min="1" max="120" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn--dark btn--lg btn--block">Crear cuenta</button>
                    <p class="auth__switch">¿Ya tienes cuenta? <a href="#" id="showLogin">Iniciar sesión</a></p>
                </form>
            </div>
        </main>
    </div>
    <script src="assets/js/login.js"></script>
</body>
</html>
