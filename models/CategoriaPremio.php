<?php
/**
 * Modelo CategoriaPremio
 * Maneja operaciones CRUD utilizando procedimientos almacenados
 */
class CategoriaPremio extends Conectar
{
    /**
     * Listar categorías por sede (opcionalmente por estado)
     */
    public function listar_categorias(int $sede_id, ?int $estado = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_categorias_premios(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            if ($estado === null) {
                $query->bindValue(2, null, PDO::PARAM_NULL);
            } else {
                $query->bindValue(2, $estado, PDO::PARAM_INT);
            }
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Categorías obtenidas correctamente' : 'No hay categorías registradas',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_categorias: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener las categorías',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener categoría por ID
     */
    public function obtener_categoria_por_id(int $id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_categoria_premio_by_id(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Categoría encontrada' : 'Categoría no existe en esta sede',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en obtener_categoria_por_id: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener la categoría',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar una nueva categoría
     */
    public function registrar_categoria(
        int $sede_id,
        string $nombre,
        ?string $descripcion,
        ?string $icono,
        ?string $color_hex,
        ?int $orden,
        string $creado_por
    ): array {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL register_categoria_premio(?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->bindValue(2, $nombre, PDO::PARAM_STR);
            $this->bindNullable($query, 3, $descripcion, PDO::PARAM_STR);
            $this->bindNullable($query, 4, $icono, PDO::PARAM_STR);
            $this->bindNullable($query, 5, $color_hex, PDO::PARAM_STR);
            $this->bindNullable($query, 6, $orden, PDO::PARAM_INT);
            $query->bindValue(7, $creado_por, PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en registrar_categoria: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar la categoría',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar categoría existente
     */
    public function actualizar_categoria(
        int $id,
        int $sede_id,
        string $nombre,
        ?string $descripcion,
        ?string $icono,
        ?string $color_hex,
        ?int $orden,
        ?int $estado,
        string $modificado_por
    ): array {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL update_categoria_premio(?, ?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, $nombre, PDO::PARAM_STR);
            $this->bindNullable($query, 4, $descripcion, PDO::PARAM_STR);
            $this->bindNullable($query, 5, $icono, PDO::PARAM_STR);
            $this->bindNullable($query, 6, $color_hex, PDO::PARAM_STR);
            $this->bindNullable($query, 7, $orden, PDO::PARAM_INT);
            $this->bindNullable($query, 8, $estado, PDO::PARAM_INT);
            $query->bindValue(9, $modificado_por, PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en actualizar_categoria: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar la categoría',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar (desactivar) una categoría
     */
    public function eliminar_categoria(int $id, int $sede_id, string $modificado_por): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL delete_categoria_premio(?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, $modificado_por, PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en eliminar_categoria: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al eliminar la categoría',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Helper para bindear valores opcionales
     */
    private function bindNullable(PDOStatement $statement, int $position, $value, int $type): void
    {
        if ($value === null || $value === '') {
            $statement->bindValue($position, null, PDO::PARAM_NULL);
            return;
        }

        $statement->bindValue($position, $value, $type);
    }
}


