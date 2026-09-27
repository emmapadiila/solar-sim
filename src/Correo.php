<?php
/**
 * Envío de correos por SMTP con PHPMailer (src/lib/phpmailer).
 * La configuración está en config('correo'); ver config/config.local.example.php.
 */

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

final class Correo
{
    /** true si hay credenciales SMTP configuradas. */
    public static function configurado(): bool
    {
        return config('correo.smtp_usuario', '') !== '' && config('correo.smtp_password', '') !== '';
    }

    /**
     * Avisa al equipo de un mensaje recibido en Contacto.
     * Nunca lanza excepciones: devuelve false y deja el motivo en el log del servidor.
     */
    public static function avisarMensajeContacto(string $nombre, string $email, ?string $asunto, string $mensaje): bool
    {
        if (!self::configurado()) {
            error_log('[SolarSim] Correo no configurado: el mensaje de contacto solo quedó en la base de datos.');
            return false;
        }

        $asuntoCorreo = 'Nuevo mensaje de contacto' . ($asunto ? ': ' . $asunto : '');
        [$fechaCorta, $hora] = self::fechaEnEspanol(time());
        $fecha = "$fechaCorta, $hora";

        $html = self::plantilla('email/contacto', [
            'nombre'  => $nombre,
            'email'   => $email,
            'asunto'  => $asunto,
            'mensaje' => $mensaje,
            'fecha'   => $fechaCorta,
            'hora'    => $hora,
        ]);

        $texto = "Nuevo mensaje de contacto en SolarSim\n\n"
            . "De: $nombre <$email>\n"
            . 'Asunto: ' . ($asunto ?: 'Sin asunto') . "\n\n"
            . "$mensaje\n\n"
            . "Recibido el $fecha. Responde a este correo para contestar directamente.";

        try {
            $correo = self::crear();
            $correo->addAddress(config('correo.destino'));
            $correo->addReplyTo($email, $nombre); // "Responder" le escribe directamente a quien envió el mensaje
            $correo->Subject = $asuntoCorreo;
            $correo->Body = $html;
            $correo->AltBody = $texto;
            $correo->send();
            return true;
        } catch (PHPMailerException $ex) {
            error_log('[SolarSim] No se pudo enviar el aviso de contacto: ' . $ex->getMessage());
            return false;
        }
    }

    /** Renderiza una plantilla de templates/ y devuelve el HTML como texto. */
    private static function plantilla(string $nombre, array $datos): string
    {
        ob_start();
        render($nombre, $datos);
        return (string)ob_get_clean();
    }

    /** ["27 sep 2026", "7:10 a. m."] */
    private static function fechaEnEspanol(int $momento): array
    {
        $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        $fecha = date('j', $momento) . ' ' . $meses[(int)date('n', $momento) - 1] . ' ' . date('Y', $momento);
        $hora = date('g:i', $momento) . (date('a', $momento) === 'am' ? ' a. m.' : ' p. m.');
        return [$fecha, $hora];
    }

    private static function crear(): PHPMailer
    {
        require_once SRC_PATH . '/lib/phpmailer/Exception.php';
        require_once SRC_PATH . '/lib/phpmailer/PHPMailer.php';
        require_once SRC_PATH . '/lib/phpmailer/SMTP.php';

        $c = config('correo');
        $correo = new PHPMailer(true);
        $correo->isSMTP();
        $correo->Host = $c['smtp_host'];
        $correo->Port = (int)$c['smtp_puerto'];
        $correo->SMTPAuth = true;
        $correo->Username = $c['smtp_usuario'];
        $correo->Password = $c['smtp_password'];
        $correo->SMTPSecure = match ($c['smtp_seguridad']) {
            'ssl'   => PHPMailer::ENCRYPTION_SMTPS,
            'tls'   => PHPMailer::ENCRYPTION_STARTTLS,
            default => '',
        };
        $correo->SMTPAutoTLS = $c['smtp_seguridad'] !== '';
        $correo->Timeout = 10; // no dejar esperando a quien envía el formulario
        $correo->CharSet = PHPMailer::CHARSET_UTF8;
        // Gmail solo permite enviar como la propia cuenta autenticada
        $correo->setFrom($c['smtp_usuario'], $c['remitente_nombre']);
        $correo->isHTML(true);

        return $correo;
    }
}
