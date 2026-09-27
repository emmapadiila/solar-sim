<?php
/**
 * Acceso a tbl_usuarios.
 */
final class UsuarioRepository
{
    public static function buscarPorNombre(string $nombre): ?array
    {
        $stmt = Database::get()->prepare(
            'SELECT id_usuario, nombre, contrasena, direccion, edad, rol
             FROM tbl_usuarios WHERE LOWER(nombre) = LOWER(?) LIMIT 1'
        );
        $stmt->execute([$nombre]);
        return $stmt->fetch() ?: null;
    }

    public static function existeNombre(string $nombre): bool
    {
        $stmt = Database::get()->prepare('SELECT 1 FROM tbl_usuarios WHERE LOWER(nombre) = LOWER(?) LIMIT 1');
        $stmt->execute([$nombre]);
        return (bool)$stmt->fetchColumn();
    }

    public static function crear(string $nombre, string $contrasenaPlana, string $direccion, int $edad, string $rol = 'usuario'): int
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO tbl_usuarios (nombre, contrasena, direccion, edad, rol) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$nombre, password_hash($contrasenaPlana, PASSWORD_DEFAULT), $direccion, $edad, $rol]);
        return (int)Database::get()->lastInsertId();
    }

    public static function actualizarContrasena(int $idUsuario, string $contrasenaPlana): void
    {
        $stmt = Database::get()->prepare('UPDATE tbl_usuarios SET contrasena = ? WHERE id_usuario = ?');
        $stmt->execute([password_hash($contrasenaPlana, PASSWORD_DEFAULT), $idUsuario]);
    }

    /**
     * Verifica la contraseña. Los usuarios semilla del .sql tienen la contraseña
     * en texto plano: si coincide, se migra a hash en ese mismo momento.
     */
    public static function verificarContrasena(array $usuario, string $contrasena): bool
    {
        $guardada = (string)$usuario['contrasena'];

        if (password_get_info($guardada)['algo'] === null) {
            if (!hash_equals($guardada, $contrasena)) {
                return false;
            }
            self::actualizarContrasena((int)$usuario['id_usuario'], $contrasena);
            return true;
        }

        if (!password_verify($contrasena, $guardada)) {
            return false;
        }
        if (password_needs_rehash($guardada, PASSWORD_DEFAULT)) {
            self::actualizarContrasena((int)$usuario['id_usuario'], $contrasena);
        }
        return true;
    }
}
