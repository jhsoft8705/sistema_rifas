<?php
/**
 * Modelo Organizacion (Sede)
 * Mantenimiento: listado y actualización de la organización (sede)
 */
class Organizacion extends Conectar
{
    /**
     * Obtener sede (organización) por ID
     */
    public function get_by_id(int $id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL get_sede_by_id(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Organización obtenida' : 'Organización no encontrada',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en Organizacion::get_by_id: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener la organización',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Listar sedes (para combos; opcional por estado)
     */
    public function listar_sedes(?int $estado = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_sedes(?)";
            $query = $conectar->prepare($sql);
            if ($estado === null) {
                $query->bindValue(1, null, PDO::PARAM_NULL);
            } else {
                $query->bindValue(1, $estado, PDO::PARAM_INT);
            }
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Sedes obtenidas' : 'No hay sedes',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en Organizacion::listar_sedes: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al listar sedes',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar sede (organización)
     */
    public function actualizar(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL update_sede(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $this->getValue($data, 'id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'codigo'), PDO::PARAM_STR);
            $query->bindValue(3, $this->getValue($data, 'nombre'), PDO::PARAM_STR);
            $query->bindValue(4, $this->getValue($data, 'pais'), PDO::PARAM_STR);
            $this->bindNullable($query, 5, $this->getValue($data, 'descripcion'), PDO::PARAM_STR);
            $this->bindNullable($query, 6, $this->getValue($data, 'direccion'), PDO::PARAM_STR);
            $this->bindNullable($query, 7, $this->getValue($data, 'telefono'), PDO::PARAM_STR);
            $this->bindNullable($query, 8, $this->getValue($data, 'email'), PDO::PARAM_STR);
            $this->bindNullable($query, 9, $this->getValue($data, 'es_principal'), PDO::PARAM_INT);
            $this->bindNullable($query, 10, $this->getValue($data, 'url_logo'), PDO::PARAM_STR);
            $this->bindNullable($query, 11, $this->getValue($data, 'url_favicon'), PDO::PARAM_STR);
            $this->bindNullable($query, 12, $this->getValue($data, 'url_landing'), PDO::PARAM_STR);
            $this->bindNullable($query, 13, $this->getValue($data, 'moneda'), PDO::PARAM_STR);
            $this->bindNullable($query, 14, $this->getValue($data, 'simbolo_moneda'), PDO::PARAM_STR);
            $this->bindNullable($query, 15, $this->getValue($data, 'codigo_moneda'), PDO::PARAM_STR);
            $this->bindNullable($query, 16, $this->getValue($data, 'zona_horaria'), PDO::PARAM_STR);
            $this->bindNullable($query, 17, $this->getValue($data, 'requiere_aprobacion_manual'), PDO::PARAM_INT);
            $this->bindNullable($query, 18, $this->getValue($data, 'dias_validez_ticket'), PDO::PARAM_INT);
            $this->bindNullable($query, 19, $this->getValue($data, 'estado'), PDO::PARAM_INT);
            $query->bindValue(20, $this->getValue($data, 'modificado_por'), PDO::PARAM_STR);
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
            error_log("Error en Organizacion::actualizar: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar la organización',
                'detalle' => $e->getMessage()
            ];
        }
    }

    private function getValue(array $data, string $key, $default = null)
    {
        return isset($data[$key]) ? $data[$key] : $default;
    }

    private function bindNullable(PDOStatement $query, int $param, $value, int $type): void
    {
        if ($value === null || $value === '') {
            $query->bindValue($param, null, PDO::PARAM_NULL);
        } else {
            $query->bindValue($param, $value, $type);
        }
    }
}
