<?php
/**
 * Consultas agregadas para el panel de estadísticas (solo admin).
 * Los valores numéricos se convierten a float: MySQL devuelve DECIMAL como
 * string y en JavaScript "1.00" + "2.00" concatena en vez de sumar.
 */
final class EstadisticasRepository
{
    private const FROM = 'FROM tbl_simulacion s JOIN tbl_resultados r ON s.id_simulacion = r.id_simulacionFK';

    /** @return array<string, float> ciudad => ahorro mensual promedio */
    public static function ahorroPorCiudad(): array
    {
        $filas = Database::get()->query('SELECT ubicacion, AVG(ahorro_mensual) AS valor ' . self::FROM . ' GROUP BY ubicacion ORDER BY ubicacion');
        $resultado = [];
        foreach ($filas as $fila) {
            $resultado[$fila['ubicacion']] = (float)$fila['valor'];
        }
        return $resultado;
    }

    /** @return array<string, float> ciudad => energía generada promedio */
    public static function energiaPorCiudad(): array
    {
        $filas = Database::get()->query('SELECT ubicacion, AVG(energia_generada) AS valor ' . self::FROM . ' GROUP BY ubicacion ORDER BY ubicacion');
        $resultado = [];
        foreach ($filas as $fila) {
            $resultado[$fila['ubicacion']] = (float)$fila['valor'];
        }
        return $resultado;
    }

    /** @return array<string, float> ciudad => años de retorno promedio */
    public static function retornoPorCiudad(): array
    {
        $filas = Database::get()->query('SELECT ubicacion, AVG(retorno_inversion) AS valor ' . self::FROM . ' GROUP BY ubicacion ORDER BY ubicacion');
        $resultado = [];
        foreach ($filas as $fila) {
            $resultado[$fila['ubicacion']] = (float)$fila['valor'];
        }
        return $resultado;
    }

    /** @return list<array{ciudad: string, x: float, y: float}> puntos área (m²) vs energía (kWh/mes) */
    public static function puntosAreaEnergia(): array
    {
        $filas = Database::get()->query(
            'SELECT ubicacion, area_disponible, AVG(energia_generada) AS energia ' . self::FROM . ' GROUP BY ubicacion, area_disponible'
        );
        $puntos = [];
        foreach ($filas as $fila) {
            $puntos[] = [
                'ciudad' => $fila['ubicacion'],
                'x'      => (float)$fila['area_disponible'],
                'y'      => round((float)$fila['energia'], 2),
            ];
        }
        return $puntos;
    }

    public static function ultimasSimulaciones(int $limite = 10): array
    {
        $stmt = Database::get()->prepare(
            'SELECT s.fecha, s.ubicacion, s.estrato, s.area_disponible, r.ahorro_mensual, r.retorno_inversion,
                    u.nombre AS nombre_usuario ' . self::FROM . '
             JOIN tbl_usuarios u ON s.id_usuarioFK = u.id_usuario
             ORDER BY s.fecha DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
