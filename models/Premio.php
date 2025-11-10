<?php
/**
 * Modelo Premio
 * Maneja operaciones CRUD utilizando procedimientos almacenados
 */
class Premio extends Conectar
{
    /**
     * Listar premios por sede (opcionalmente por estado)
     */
    public function listar_premios(int $sede_id, ?int $estado = null, ?string $fecha_inicio = null, ?string $fecha_fin = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_premios(?, ?, ?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $this->bindNullable($query, 2, $estado, PDO::PARAM_INT);
            $this->bindNullable($query, 3, $fecha_inicio, PDO::PARAM_STR);
            $this->bindNullable($query, 4, $fecha_fin, PDO::PARAM_STR);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Premios obtenidos correctamente' : 'No hay premios registrados',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_premios: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los premios',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener premio por ID
     */
    public function obtener_premio_por_id(int $id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_premio_by_id(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Premio encontrado' : 'Premio no existe en esta sede',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en obtener_premio_por_id: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener el premio',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar nuevo premio
     */
    public function registrar_premio(
        int $sede_id,
        ?int $categoria_id,
        string $codigo,
        string $nombre,
        ?string $descripcion,
        ?float $valor_estimado,
        ?string $imagen_principal,
        ?string $imagen_secundaria,
        ?string $galeria_imagenes,
        ?string $video_url,
        ?string $marca,
        ?string $modelo,
        ?string $color,
        ?string $especificaciones,
        ?string $terminos_condiciones,
        ?string $restricciones,
        ?int $es_destacado,
        ?int $orden_visualizacion,
        string $creado_por
    ): array {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL register_premio(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $this->bindNullable($query, 2, $categoria_id, PDO::PARAM_INT);
            $query->bindValue(3, $codigo, PDO::PARAM_STR);
            $query->bindValue(4, $nombre, PDO::PARAM_STR);
            $this->bindNullable($query, 5, $descripcion, PDO::PARAM_STR);
            $this->bindNullable($query, 6, $valor_estimado, PDO::PARAM_STR);
            $this->bindNullable($query, 7, $imagen_principal, PDO::PARAM_STR);
            $this->bindNullable($query, 8, $imagen_secundaria, PDO::PARAM_STR);
            $this->bindNullable($query, 9, $galeria_imagenes, PDO::PARAM_STR);
            $this->bindNullable($query, 10, $video_url, PDO::PARAM_STR);
            $this->bindNullable($query, 11, $marca, PDO::PARAM_STR);
            $this->bindNullable($query, 12, $modelo, PDO::PARAM_STR);
            $this->bindNullable($query, 13, $color, PDO::PARAM_STR);
            $this->bindNullable($query, 14, $especificaciones, PDO::PARAM_STR);
            $this->bindNullable($query, 15, $terminos_condiciones, PDO::PARAM_STR);
            $this->bindNullable($query, 16, $restricciones, PDO::PARAM_STR);
            $this->bindNullable($query, 17, $es_destacado, PDO::PARAM_INT);
            $this->bindNullable($query, 18, $orden_visualizacion, PDO::PARAM_INT);
            $query->bindValue(19, $creado_por, PDO::PARAM_STR);
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
            error_log("Error en registrar_premio: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar el premio',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar premio existente
     */
    public function actualizar_premio(
        int $id,
        int $sede_id,
        ?int $categoria_id,
        string $codigo,
        string $nombre,
        ?string $descripcion,
        ?float $valor_estimado,
        ?string $imagen_principal,
        ?string $imagen_secundaria,
        ?string $galeria_imagenes,
        ?string $video_url,
        ?string $marca,
        ?string $modelo,
        ?string $color,
        ?string $especificaciones,
        ?string $terminos_condiciones,
        ?string $restricciones,
        ?int $es_destacado,
        ?int $orden_visualizacion,
        ?int $estado,
        string $modificado_por
    ): array {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL update_premio(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $this->bindNullable($query, 3, $categoria_id, PDO::PARAM_INT);
            $query->bindValue(4, $codigo, PDO::PARAM_STR);
            $query->bindValue(5, $nombre, PDO::PARAM_STR);
            $this->bindNullable($query, 6, $descripcion, PDO::PARAM_STR);
            $this->bindNullable($query, 7, $valor_estimado, PDO::PARAM_STR);
            $this->bindNullable($query, 8, $imagen_principal, PDO::PARAM_STR);
            $this->bindNullable($query, 9, $imagen_secundaria, PDO::PARAM_STR);
            $this->bindNullable($query, 10, $galeria_imagenes, PDO::PARAM_STR);
            $this->bindNullable($query, 11, $video_url, PDO::PARAM_STR);
            $this->bindNullable($query, 12, $marca, PDO::PARAM_STR);
            $this->bindNullable($query, 13, $modelo, PDO::PARAM_STR);
            $this->bindNullable($query, 14, $color, PDO::PARAM_STR);
            $this->bindNullable($query, 15, $especificaciones, PDO::PARAM_STR);
            $this->bindNullable($query, 16, $terminos_condiciones, PDO::PARAM_STR);
            $this->bindNullable($query, 17, $restricciones, PDO::PARAM_STR);
            $this->bindNullable($query, 18, $es_destacado, PDO::PARAM_INT);
            $this->bindNullable($query, 19, $orden_visualizacion, PDO::PARAM_INT);
            $this->bindNullable($query, 20, $estado, PDO::PARAM_INT);
            $query->bindValue(21, $modificado_por, PDO::PARAM_STR);
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
            error_log("Error en actualizar_premio: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar el premio',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar (lógico) un premio
     */
    public function eliminar_premio(int $id, int $sede_id, string $modificado_por): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL delete_premio(?, ?, ?, @mensaje)";
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
            error_log("Error en eliminar_premio: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al eliminar el premio',
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


