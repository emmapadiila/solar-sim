<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

render('layout/header', ['titulo' => 'Contacto', 'paginaActiva' => 'contacto']);
?>

    <main class="content-page">
        <section class="contact-section">
            <div class="contact-container">
                <h1 class="page-title">Contáctanos</h1>
                <p class="intro-text">¿Tienes preguntas, sugerencias o necesitas soporte? No dudes en contactarnos. Estamos aquí para ayudarte.</p>

                <div class="contact-info-grid">
                    <div class="info-item">
                        <i class="fas fa-envelope icon-animated"></i>
                        <h3>Correo Electrónico</h3>
                        <p><a href="mailto:emma-padillaj@unilibre.edu.co">emma-padillaj@unilibre.edu.co</a></p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone icon-animated"></i>
                        <h3>Teléfono</h3>
                        <p><a href="tel:+573225377936">+57 3225377936</a></p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt icon-animated"></i>
                        <h3>Dirección</h3>
                        <p>Calle 42 A 6B 47</p>
                    </div>
                </div>

                <div class="contact-form-container">
                    <h2>Envíanos un Mensaje</h2>
                    <form class="contact-form" id="contactForm">
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Asunto</label>
                            <input type="text" id="subject" name="subject">
                        </div>
                        <div class="form-group">
                            <label for="message">Mensaje</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="auth-btn">Enviar Mensaje <i class="fas fa-paper-plane"></i></button>
                        <div id="contactMessage" class="message-area"></div>
                    </form>
                </div>
            </div>
        </section>
    </main>

<?php render('layout/footer', ['scripts' => ['contacto.js']]); ?>
