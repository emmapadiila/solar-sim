<?php
/**
 * Acceso a tbl_simulacion, tbl_resultados, tbl_historial y tbl_detalle_historial.
 */
final class SimulacionRepository
{
    private const SELECT_BASE = '
        SELECT s.id_simulacion, s.fecha, s.ubicacion, s.estrato, s.area_disponible,
               s.consumo_mensual, s.tipo_energia,
               r.energia_generada, r.ahorro_mensual, r.ahorro_anual, r.retorno_inversion
        FROM tbl_simulacion s
        LEFT JOIN tbl_resultados r ON s.id_simulacion = r.id_simulacionFK';

    /** Guarda simulación + resultados y actualiza el historial del usuario en una transacción. */
    public static function crear(int $idUsuario, array $datos, array $resultados): int
    {
        $db = Database::get();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare(
                'INSERT INTO tbl_simulacion (ubicacion, estrato, area_disponible, consumo_mensual, tipo_energia, id_usuarioFK)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $datos['ubicacion'], $datos['estrato'], $datos['area_disponible'],
                $datos['consumo_mensual'], $datos['tipo_energia'], $idUsuario,
            ]);
            $idSimulacion = (int)$db->lastInsertId();

            $stmt = $db->prepare(
                'INSERT INTO tbl_resultados (energia_generada, ahorro_mensual, retorno_inversion, id_simulacionFK)
                 VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $resultados['energiaGenerada'], $resultados['ahorroMensual'],
                $resultados['retornoInversion'], $idSimulacion,
            ]);

            $idHistorial = self::incrementarHistorial($idUsuario);

            $descripcion = sprintf(
                'Nueva simulación de paneles solares - Ubicación: %s, Consumo: %s kWh/mes',
                $datos['ubicacion'], $datos['consumo_mensual']
            );
            $stmt = $db->prepare(
                "INSERT INTO tbl_detalle_historial (tipo_accion, descripcion, id_historialFK, id_simulacionFK)
                 VALUES ('creacion', ?, ?, ?)"
            );
            $stmt->execute([$descripcion, $idHistorial, $idSimulacion]);

            $db->commit();
            return $idSimulacion;
        } catch (Throwable $ex) {
            $db->rollBack();
            throw $ex;
        }
    }

    public static function listarPorUsuario(int $idUsuario): array
    {
        $stmt = Database::get()->prepare(self::SELECT_BASE . ' WHERE s.id_usuarioFK = ? ORDER BY s.fecha DESC');
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll();
    }

    /** Devuelve la simulación solo si pertenece al usuario indicado. */
    public static function buscarDeUsuario(int $idSimulacion, int $idUsuario): ?array
    {
        $stmt = Database::get()->prepare(self::SELECT_BASE . ' WHERE s.id_simulacion = ? AND s.id_usuarioFK = ?');
        $stmt->execute([$idSimulacion, $idUsuario]);
        return $stmt->fetch() ?: null;
    }

    private static function incrementarHistorial(int $idUsuario): int
    {
        $db = Database::get();

        $stmt = $db->prepare('SELECT id_historial FROM tbl_historial WHERE id_usuarioFK = ?');
        $stmt->execute([$idUsuario]);
        $idHistorial = $stmt->fetchColumn();

        if ($idHistorial) {
            $db->prepare('UPDATE tbl_historial SET total_simulaciones = total_simulaciones + 1 WHERE id_historial = ?')
               ->execute([$idHistorial]);
            return (int)$idHistorial;
        }

        $db->prepare('INSERT INTO tbl_historial (total_simulaciones, id_usuarioFK) VALUES (1, ?)')->execute([$idUsuario]);
        return (int)$db->lastInsertId();
    }
}
