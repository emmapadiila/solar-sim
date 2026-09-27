<?php
// Respaldo por si el servidor no tiene mod_rewrite: enviar al punto de entrada real.
header('Location: public/');
exit;
