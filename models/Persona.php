<?php
/**
 * Modelo Persona
 * Maneja operaciones CRUD utilizando procedimientos almacenados
 */
class Persona extends Conectar
{
    /**
     * Listar personas por sede
     */
    public function listar_personas(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_personas(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Personas obtenidas correctamente' : 'No hay personas registradas',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_personas: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener las personas',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener persona por ID
     */
    public function obtener_persona_por_id(int $id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_persona_by_id(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Persona encontrada' : 'Persona no existe en esta sede',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en obtener_persona_por_id: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener la persona',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar nueva persona
     */
    public function registrar_persona(
        int $sede_id,
        string $nombres,
        string $apellidos,
        string $tipo_documento,
        string $numero_documento,
        ?string $email,
        ?string $telefono,
        ?string $direccion,
        ?string $ciudad,
        ?string $pais,
        string $creado_por
    ): array {
        try {
            $conectar = parent::Conexion();
            $placeholders = implode(', ', array_fill(0, 11, '?'));
            $sql = "CALL register_persona($placeholders, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->bindValue(2, trim($nombres), PDO::PARAM_STR);
            $query->bindValue(3, trim($apellidos), PDO::PARAM_STR);
            $query->bindValue(4, trim($tipo_documento), PDO::PARAM_STR);
            $query->bindValue(5, trim($numero_documento), PDO::PARAM_STR);
            $this->bindNullable($query, 6, $this->nullIfEmpty($email), PDO::PARAM_STR);
            $this->bindNullable($query, 7, $this->nullIfEmpty($telefono), PDO::PARAM_STR);
            $this->bindNullable($query, 8, $this->nullIfEmpty($direccion), PDO::PARAM_STR);
            $this->bindNullable($query, 9, $this->nullIfEmpty($ciudad), PDO::PARAM_STR);
            $this->bindNullable($query, 10, $this->nullIfEmpty($pais), PDO::PARAM_STR);
            $query->bindValue(11, trim($creado_por), PDO::PARAM_STR);
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
            error_log("Error en registrar_persona: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar la persona',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar persona
     */
    public function actualizar_persona(
        int $id,
        int $sede_id,
        string $nombres,
        string $apellidos,
        string $tipo_documento,
        string $numero_documento,
        ?string $email,
        ?string $telefono,
        ?string $direccion,
        ?string $ciudad,
        ?string $pais,
        string $modificado_por
    ): array {
        try {
            $conectar = parent::Conexion();
            $placeholders = implode(', ', array_fill(0, 11, '?'));
            $sql = "CALL update_persona($placeholders, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, trim($nombres), PDO::PARAM_STR);
            $query->bindValue(4, trim($apellidos), PDO::PARAM_STR);
            $query->bindValue(5, trim($tipo_documento), PDO::PARAM_STR);
            $query->bindValue(6, trim($numero_documento), PDO::PARAM_STR);
            $this->bindNullable($query, 7, $this->nullIfEmpty($email), PDO::PARAM_STR);
            $this->bindNullable($query, 8, $this->nullIfEmpty($telefono), PDO::PARAM_STR);
            $this->bindNullable($query, 9, $this->nullIfEmpty($direccion), PDO::PARAM_STR);
            $this->bindNullable($query, 10, $this->nullIfEmpty($ciudad), PDO::PARAM_STR);
            $this->bindNullable($query, 11, $this->nullIfEmpty($pais), PDO::PARAM_STR);
            $query->bindValue(12, trim($modificado_por), PDO::PARAM_STR);
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
            error_log("Error en actualizar_persona: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar la persona',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar persona
     */
    public function eliminar_persona(int $id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL delete_persona(?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
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
            error_log("Error en eliminar_persona: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al eliminar la persona',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Helper bind nullable
     */
    private function bindNullable(PDOStatement $statement, int $position, $value, int $type): void
    {
        if ($value === null || $value === '') {
            $statement->bindValue($position, null, PDO::PARAM_NULL);
            return;
        }
        $statement->bindValue($position, $value, $type);
    }

    /**
     * Helper para convertir valores vacíos a null
     */
    private function nullIfEmpty($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }
        return $value;
    }
}
