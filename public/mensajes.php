<?php
require __DIR__ . '/../src/bootstrap.php';

Auth::requireAdmin();

$mensajes = MensajeRepository::listar();
$noLeidos = count(array_filter($mensajes, fn($m) => !$m['leido']));

render('layout/header', ['titulo' => 'Mensajes', 'paginaActiva' => 'mensajes']);
?>

    <main class="page">
        <div class="container">
            <div class="page-header">
                <div>
                    <p class="eyebrow"><i class="fas fa-inbox"></i> Administración</p>
                    <h1 class="page-title">Mensajes de contacto</h1>
                    <p class="page-subtitle">
                        <?= $noLeidos ? ($noLeidos === 1 ? '1 mensaje sin leer.' : "$noLeidos mensajes sin leer.") : 'No hay mensajes pendientes.' ?>
                        Responde directamente al correo de cada persona.
                    </p>
                </div>
            </div>

            <?php if (!$mensajes): ?>
                <div class="card">
                    <div class="empty-state">
                        <span class="icon-badge"><i class="fas fa-inbox"></i></span>
                        <h2>Todavía no hay mensajes</h2>
                        <p>Los mensajes que los usuarios envíen desde la página de contacto aparecerán aquí.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="stack">
                    <?php foreach ($mensajes as $m): ?>
                        <article class="card message<?= $m['leido'] ? ' message--read' : '' ?>" id="mensaje-<?= (int)$m['id_mensaje'] ?>">
                            <div class="message__header">
                                <div>
                                    <h2 class="message__subject"><?= e($m['asunto'] ?: 'Sin asunto') ?></h2>
                                    <p class="message__meta">
                                        <strong><?= e($m['nombre']) ?></strong> ·
                                        <a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a> ·
                                        <?= date('d/m/Y H:i', strtotime($m['fecha'])) ?>
                                    </p>
                                </div>
                                <?php if (!$m['leido']): ?>
                                    <span class="badge badge--sun">Nuevo</span>
                                <?php endif; ?>
                            </div>
                            <p class="message__body"><?= nl2br(e($m['mensaje'])) ?></p>
                            <?php if (!$m['leido']): ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn--secondary btn--sm" onclick="marcarLeido(<?= (int)$m['id_mensaje'] ?>)">
                                        <i class="fas fa-check"></i> Marcar como leído
                                    </button>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php render('layout/footer', ['scripts' => ['mensajes.js']]); ?>
