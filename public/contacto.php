<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

render('layout/header', ['titulo' => 'Contacto', 'paginaActiva' => 'contacto']);
?>

    <main class="page">
        <div class="container">
            <div class="page-header">
                <div>
                    <p class="eyebrow"><i class="fas fa-envelope"></i> Contacto</p>
                    <h1 class="page-title">Hablemos</h1>
                    <p class="page-subtitle">¿Tienes preguntas, sugerencias o necesitas soporte? Escríbenos y te responderemos lo antes posible.</p>
                </div>
            </div>

            <div class="grid grid--sidebar">
                <div class="stack">
                    <div class="card contact-item">
                        <span class="icon-badge icon-badge--navy"><i class="fas fa-envelope"></i></span>
                        <div>
                            <p class="contact-item__label">Correo electrónico</p>
                            <a class="contact-item__value" href="mailto:emma-padillaj@unilibre.edu.co">emma-padillaj@unilibre.edu.co</a>
                        </div>
                    </div>
                    <div class="card contact-item">
                        <span class="icon-badge icon-badge--navy"><i class="fas fa-phone"></i></span>
                        <div>
                            <p class="contact-item__label">Teléfono</p>
                            <a class="contact-item__value" href="tel:+573225377936">+57 322 537 7936</a>
                        </div>
                    </div>
                </div>

                <form class="card" id="contactForm" novalidate>
                    <div class="card__header">
                        <h2 class="card__title"><i class="fas fa-paper-plane"></i> Envíanos un mensaje</h2>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="name">Nombre</label>
                            <input type="text" id="name" name="name" class="form-control" autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Correo electrónico</label>
                            <input type="email" id="email" name="email" class="form-control" autocomplete="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="subject">Asunto <span class="text-muted">(opcional)</span></label>
                        <input type="text" id="subject" name="subject" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="message">Mensaje</label>
                        <textarea id="message" name="message" class="form-control" rows="6" required></textarea>
                    </div>
                    <button type="submit" class="btn btn--dark"><i class="fas fa-paper-plane"></i> Enviar mensaje</button>
                </form>
            </div>
        </div>
    </main>

<?php render('layout/footer', ['scripts' => ['contacto.js']]); ?>
