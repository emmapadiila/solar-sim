<?php
/**
 * Scripts comunes y cierre del documento.
 * @var string[] $scripts  scripts propios de la página, relativos a assets/js/ (opcional)
 */
?>
    <script src="https://cdn.botpress.cloud/webchat/v3.0/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2025/06/16/15/20250616155429-DTJTHRK0.js"></script>
    <script src="assets/js/app.js"></script>
    <?php foreach ($scripts ?? [] as $script): ?>
    <script src="assets/js/<?= e($script) ?>"></script>
    <?php endforeach; ?>
</body>
</html>
