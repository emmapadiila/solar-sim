<?php
/**
 * Manejo de la sesión del usuario y control de acceso.
 */
final class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['usuario']) ? (int)$_SESSION['usuario']['id_usuario'] : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['rol'] ?? null) === 'admin';
    }

    /** Inicia sesión con una fila de tbl_usuarios y devuelve los datos públicos guardados en sesión. */
    public static function login(array $usuario): array
    {
        $rol = strtolower(trim((string)($usuario['rol'] ?? '')));

        $datos = [
            'id_usuario' => (int)$usuario['id_usuario'],
            'nombre'     => $usuario['nombre'],
            'direccion'  => $usuario['direccion'],
            'edad'       => (int)$usuario['edad'],
            'rol'        => $rol === 'admin' ? 'admin' : 'usuario',
        ];

        session_regenerate_id(true);
        $_SESSION['usuario'] = $datos;

        return $datos;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /** Para páginas: redirige al login si no hay sesión. */
    public static function requireLogin(): array
    {
        if (!self::check()) {
            header('Location: index.php');
            exit;
        }
        return self::user();
    }

    /** Para páginas: solo administradores. */
    public static function requireAdmin(): array
    {
        $usuario = self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: dashboard.php');
            exit;
        }
        return $usuario;
    }

    /** Para endpoints JSON: responde 401 si no hay sesión. */
    public static function requireLoginApi(): array
    {
        if (!self::check()) {
            json_response(['success' => false, 'message' => 'Usuario no autenticado'], 401);
        }
        return self::user();
    }
}
