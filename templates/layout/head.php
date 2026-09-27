<?php
/**
 * <head> común.
 * @var string $titulo
 * @var bool   $usarGraficos  carga Chart.js (opcional)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f1d33">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%23f59e0b'/%3E%3Ccircle cx='32' cy='32' r='11' fill='%230f1d33'/%3E%3Cg stroke='%230f1d33' stroke-width='5' stroke-linecap='round'%3E%3Cpath d='M32 9v6M32 49v6M9 32h6M49 32h6M15.7 15.7l4.2 4.2M44.1 44.1l4.2 4.2M15.7 48.3l4.2-4.2M44.1 19.9l4.2-4.2'/%3E%3C/g%3E%3C/svg%3E">
    <title><?= e($titulo) ?> · <?= e(config('app.nombre')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        // Las tarjetas empiezan ocultas desde el primer pintado para que app.js las haga aparecer
        // sin que antes se vean, desaparezcan y vuelvan a aparecer.
        if ('IntersectionObserver' in window && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.documentElement.classList.add('con-apariciones');
            // Si app.js no llega a ejecutarse, se muestra todo igual
            setTimeout(function () {
                if (!window.SolarSim) document.documentElement.classList.remove('con-apariciones');
            }, 3000);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (!empty($usarGraficos)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <?php endif; ?>
</head>
