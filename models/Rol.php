<?php
/**
 * Modelo Rol
 * Listar roles por sede (para combos de usuarios)
 */
class Rol extends Conectar
{
    /**
     * Listar roles por sede (activos)
     */
    public function listar_por_sede(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_roles(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Roles obtenidos' : 'No hay roles',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en Rol::listar_por_sede: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al listar roles',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }
}
