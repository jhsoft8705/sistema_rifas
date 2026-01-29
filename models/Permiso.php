<?php
/**
 * Modelo Permiso
 * Manejo de operaciones CRUD para permisos
 */
class Permiso extends Conectar
{
    /**
     * Listar permisos por sede
     */
    public function listar_permisos(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_permisos(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Permisos obtenidos' : 'No hay permisos',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en Permiso::listar_permisos: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al listar permisos',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener permiso por ID
     */
    public function get_by_id(int $permiso_id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "SELECT * FROM permisos WHERE id = ? AND sede_id = ?";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $permiso_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Permiso obtenido' : 'Permiso no encontrado',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en Permiso::get_by_id: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener el permiso',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar nuevo permiso
     */
    public function registrar(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL register_permiso(?, ?, ?, ?, ?, ?, @permiso_id, @mensaje)";
            $query = $conectar->prepare($sql);
            
            $query->bindValue(1, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(2, trim($this->getValue($data, 'nombre')), PDO::PARAM_STR);
            $this->bindNullable($query, 3, trim($this->getValue($data, 'descripcion', '')), PDO::PARAM_STR);
            $query->bindValue(4, trim($this->getValue($data, 'modulo')), PDO::PARAM_STR);
            $query->bindValue(5, trim($this->getValue($data, 'accion')), PDO::PARAM_STR);
            $query->bindValue(6, trim($this->getValue($data, 'creado_por')), PDO::PARAM_STR);
            
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @permiso_id AS permiso_id, @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje,
                'permiso_id' => $result['permiso_id'] ?? null
            ];
        } catch (PDOException $e) {
            error_log("Error en Permiso::registrar: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar el permiso',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar permiso
     */
    public function actualizar(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL update_permiso(?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            
            $query->bindValue(1, $this->getValue($data, 'id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(3, trim($this->getValue($data, 'nombre')), PDO::PARAM_STR);
            $this->bindNullable($query, 4, trim($this->getValue($data, 'descripcion', '')), PDO::PARAM_STR);
            $query->bindValue(5, trim($this->getValue($data, 'modulo')), PDO::PARAM_STR);
            $query->bindValue(6, trim($this->getValue($data, 'accion')), PDO::PARAM_STR);
            $estado = isset($data['estado']) && $data['estado'] !== '' ? (int) $data['estado'] : 1;
            $query->bindValue(7, $estado, PDO::PARAM_INT);
            $query->bindValue(8, trim($this->getValue($data, 'modificado_por')), PDO::PARAM_STR);
            
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
            error_log("Error en Permiso::actualizar: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar el permiso',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener permisos de un usuario
     */
    public function get_permisos_usuario(int $usuario_id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL get_permisos_usuario(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $usuario_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'Permisos obtenidos correctamente',
                'data' => $data ?: []
            ];
        } catch (PDOException $e) {
            error_log("Error en Permiso::get_permisos_usuario: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener permisos del usuario',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar si usuario tiene un permiso específico
     */
    public function verificar_permiso(int $usuario_id, int $sede_id, string $permiso_nombre): bool
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL verificar_permiso_usuario(?, ?, ?, @tiene_permiso)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $usuario_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, $permiso_nombre, PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $resultStmt = $conectar->query("SELECT @tiene_permiso AS tiene_permiso");
            $result = $resultStmt->fetch(PDO::FETCH_ASSOC);
            $resultStmt->closeCursor();

            return (int)($result['tiene_permiso'] ?? 0) === 1;
        } catch (PDOException $e) {
            error_log("Error en Permiso::verificar_permiso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper para obtener valor de array con default
     */
    private function getValue(array $data, string $key, $default = null)
    {
        return isset($data[$key]) ? $data[$key] : $default;
    }

    /**
     * Helper para bindear valores nullable
     */
    private function bindNullable($query, int $param, $value, int $type): void
    {
        if ($value === null || $value === '') {
            $query->bindValue($param, null, PDO::PARAM_NULL);
        } else {
            $query->bindValue($param, $value, $type);
        }
    }
}
