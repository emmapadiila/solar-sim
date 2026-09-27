<?php
/**
 * Correo HTML que avisa al equipo de un mensaje de contacto.
 * Hecho con tablas y estilos en línea: es lo único que Gmail, Outlook y Apple Mail respetan igual.
 *
 * @var string      $nombre
 * @var string      $email
 * @var string|null $asunto
 * @var string      $mensaje
 * @var string      $fecha      p. ej. "27 sep 2026"
 * @var string      $hora       p. ej. "7:10 AM"
 */
$fuente = "font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;";
$primerNombre = explode(' ', trim($nombre))[0];
$responder = 'mailto:' . rawurlencode($email) . '?subject=' . rawurlencode('Re: ' . ($asunto ?: 'Tu mensaje a SolarSim'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>Nuevo mensaje de contacto</title>
</head>
<body style="margin: 0; padding: 0; background-color: #e5e7eb;">
    <!-- Texto de vista previa que Gmail muestra junto al asunto -->
    <div style="display: none; max-height: 0; overflow: hidden; opacity: 0;">
        <?= e($nombre) ?> te escribió: <?= e(mb_strimwidth(preg_replace('/\s+/', ' ', $mensaje), 0, 90, '…')) ?>
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #e5e7eb;">
        <tr>
            <td align="center" style="padding: 32px 12px;">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; background-color: #ffffff;">

                    <!-- Cabecera: marca y fecha -->
                    <tr>
                        <td style="background-color: #eef1f5; padding: 28px 40px 0 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td valign="middle" style="<?= $fuente ?>">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="28" height="28" align="center" valign="middle" style="background-color: #f59e0b; border-radius: 7px;">
                                                    <div style="width: 10px; height: 10px; background-color: #0f1d33; border-radius: 50%; font-size: 0; line-height: 0;">&nbsp;</div>
                                                </td>
                                                <td style="padding-left: 10px; <?= $fuente ?> font-size: 20px; font-weight: 700; color: #0f1d33; letter-spacing: -0.3px;">SolarSim</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td align="right" valign="middle" style="<?= $fuente ?> font-size: 12px; line-height: 16px; color: #64748b;">
                                        <?= e($fecha) ?><br><?= e($hora) ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Titular -->
                    <tr>
                        <td style="background-color: #eef1f5; padding: 40px 40px 36px 40px;">
                            <p style="margin: 0 0 6px 0; <?= $fuente ?> font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #d97706;">Nuevo mensaje de contacto</p>
                            <h1 style="margin: 0 0 14px 0; <?= $fuente ?> font-size: 32px; line-height: 38px; font-weight: 700; color: #0f172a;"><?= e($primerNombre) ?> quiere hablar con el equipo</h1>
                            <p style="margin: 0; <?= $fuente ?> font-size: 15px; line-height: 22px; color: #334155;">Recibiste un mensaje desde la página de contacto de SolarSim.</p>
                        </td>
                    </tr>

                    <!-- Datos del remitente -->
                    <tr>
                        <td style="padding: 32px 40px 8px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td colspan="2" style="padding-bottom: 14px; border-bottom: 1px solid #e5e7eb; <?= $fuente ?> font-size: 20px; font-weight: 700; color: #0f172a;">
                                        <?= e($asunto ?: 'Sin asunto') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 0; border-bottom: 1px solid #e5e7eb; <?= $fuente ?> font-size: 14px; color: #64748b;">Nombre</td>
                                    <td align="right" style="padding: 14px 0; border-bottom: 1px solid #e5e7eb; <?= $fuente ?> font-size: 14px; font-weight: 700; color: #0f172a;"><?= e($nombre) ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 0; border-bottom: 1px solid #e5e7eb; <?= $fuente ?> font-size: 14px; color: #64748b;">Correo</td>
                                    <td align="right" style="padding: 14px 0; border-bottom: 1px solid #e5e7eb; <?= $fuente ?> font-size: 14px; font-weight: 700;">
                                        <a href="mailto:<?= e($email) ?>" style="color: #1e3a64; text-decoration: none;"><?= e($email) ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 0; border-bottom: 1px solid #e5e7eb; <?= $fuente ?> font-size: 14px; color: #64748b;">Recibido</td>
                                    <td align="right" style="padding: 14px 0; border-bottom: 1px solid #e5e7eb; <?= $fuente ?> font-size: 14px; font-weight: 700; color: #0f172a;"><?= e($fecha) ?>, <?= e($hora) ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Mensaje -->
                    <tr>
                        <td style="padding: 24px 40px 8px 40px;">
                            <p style="margin: 0 0 12px 0; <?= $fuente ?> font-size: 18px; font-weight: 700; color: #0f172a;">Mensaje</p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color: #f8fafc; border-left: 4px solid #f59e0b; padding: 18px 20px; <?= $fuente ?> font-size: 15px; line-height: 23px; color: #1e293b;">
                                        <?= nl2br(e($mensaje)) ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Botón -->
                    <tr>
                        <td style="padding: 28px 40px 36px 40px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="background-color: #0f1d33; border-radius: 8px;">
                                        <a href="<?= e($responder) ?>" style="display: inline-block; padding: 14px 28px; <?= $fuente ?> font-size: 15px; font-weight: 700; color: #ffffff; text-decoration: none;">Responder a <?= e($primerNombre) ?></a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 14px 0 0 0; <?= $fuente ?> font-size: 13px; line-height: 19px; color: #64748b;">También puedes pulsar <strong>Responder</strong> en tu correo: la respuesta le llega directamente a <?= e($primerNombre) ?>.</p>
                        </td>
                    </tr>

                    <!-- Pie -->
                    <tr>
                        <td style="background-color: #0f1d33; padding: 24px 40px;">
                            <p style="margin: 0 0 4px 0; <?= $fuente ?> font-size: 14px; font-weight: 700; color: #ffffff;">SolarSim</p>
                            <p style="margin: 0; <?= $fuente ?> font-size: 12px; line-height: 18px; color: #94a3b8;">Simulador de ahorro con energía solar. Este aviso se envió automáticamente porque alguien usó el formulario de contacto; el mensaje también está guardado en la sección Mensajes del panel de administración.</p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
