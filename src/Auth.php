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

    /** Para páginas: redirige al login si no hay sesión, recordando a dónde quería ir. */
    public static function requireLogin(): array
    {
        if (!self::check()) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $pagina = basename($_SERVER['SCRIPT_NAME']);
                $query = $_SERVER['QUERY_STRING'] ?? '';
                $_SESSION['destino'] = $pagina . ($query !== '' ? '?' . $query : '');
            }
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
            flash('Esa sección es solo para administradores.');
            header('Location: dashboard.php');
            exit;
        }
        return $usuario;
    }

    /**
     * Página a la que ir después de iniciar sesión: la que se pidió antes del login
     * (solo páginas propias de public/) o el panel.
     */
    public static function destinoTrasLogin(): string
    {
        $destino = $_SESSION['destino'] ?? '';
        unset($_SESSION['destino']);

        if (preg_match('/^([a-z_]+\.php)(\?[\w=&%.-]*)?$/', $destino, $m)
            && $m[1] !== 'index.php'
            && is_file(ROOT_PATH . '/public/' . $m[1])) {
            return $destino;
        }
        return 'dashboard.php';
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
