<?php
/**
 * Conexión única a MySQL/MariaDB (PDO). Se crea la primera vez que se pide.
 */
final class Database
{
    private static ?PDO $conexion = null;

    public static function get(): PDO
    {
        if (self::$conexion === null) {
            $db = config('db');
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $db['host'], $db['port'], $db['nombre']);

            self::$conexion = new PDO($dsn, $db['usuario'], $db['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$conexion;
    }
}
