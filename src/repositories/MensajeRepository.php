<?php
/**
 * Acceso a tbl_mensajes_contacto (mensajes enviados desde la página de contacto).
 */
final class MensajeRepository
{
    public static function crear(?int $idUsuario, string $nombre, string $email, ?string $asunto, string $mensaje): int
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO tbl_mensajes_contacto (nombre, email, asunto, mensaje, id_usuarioFK) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$nombre, $email, $asunto, $mensaje, $idUsuario]);
        return (int)Database::get()->lastInsertId();
    }

    /** Todos los mensajes, primero los no leídos y los más recientes. */
    public static function listar(): array
    {
        return Database::get()->query(
            'SELECT id_mensaje, fecha, nombre, email, asunto, mensaje, leido
             FROM tbl_mensajes_contacto ORDER BY leido ASC, fecha DESC'
        )->fetchAll();
    }

    public static function marcarLeido(int $idMensaje): bool
    {
        $stmt = Database::get()->prepare('UPDATE tbl_mensajes_contacto SET leido = 1 WHERE id_mensaje = ?');
        $stmt->execute([$idMensaje]);
        return $stmt->rowCount() > 0;
    }
}
