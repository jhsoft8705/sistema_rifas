<?php
/**
 * Modelo Reporte
 * Reporte de recaudación por rifa y ganadores
 */
class Reporte extends Conectar
{
    /**
     * Obtener recaudación de una rifa en un rango de fechas
     */
    public function get_recaudacion_rifa(int $sede_id, int $rifa_id, string $fecha_desde, string $fecha_hasta): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL reporte_recaudacion_rifa(?, ?, ?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->bindValue(2, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(3, $fecha_desde, PDO::PARAM_STR);
            $query->bindValue(4, $fecha_hasta, PDO::PARAM_STR);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'Datos obtenidos',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en Reporte::get_recaudacion_rifa: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener recaudación',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener ganadores de una rifa
     */
    public function get_ganadores_rifa(int $sede_id, int $rifa_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL reporte_ganadores_rifa(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->bindValue(2, $rifa_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Ganadores obtenidos' : 'No hay ganadores registrados para esta rifa',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en Reporte::get_ganadores_rifa: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener ganadores',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }
}
