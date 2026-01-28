<?php
/**
 * Modelo Usuario
 * Mantenimiento: listar, registrar, actualizar, dar de baja
 */
class Usuario extends Conectar
{
    /**
     * Listar usuarios por sede (opcionalmente por estado)
     */
    public function listar_usuarios(int $sede_id, ?int $estado = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_usuarios(?, ?)";
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
                'msj' => !empty($data) ? 'Usuarios obtenidos' : 'No hay usuarios',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en Usuario::listar_usuarios: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al listar usuarios',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener usuario por ID y sede
     */
    public function get_by_id(int $id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL get_usuario_by_id(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Usuario obtenido' : 'Usuario no encontrado',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en Usuario::get_by_id: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener el usuario',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar usuario (password_hash ya debe venir hasheado)
     */
    public function registrar(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL register_usuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $this->getVal($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(2, trim($this->getVal($data, 'username')), PDO::PARAM_STR);
            $query->bindValue(3, $this->getVal($data, 'password_hash'), PDO::PARAM_STR);
            $query->bindValue(4, trim($this->getVal($data, 'email')), PDO::PARAM_STR);
            $query->bindValue(5, trim($this->getVal($data, 'primer_nombre')), PDO::PARAM_STR);
            $query->bindValue(6, trim($this->getVal($data, 'apellido_paterno')), PDO::PARAM_STR);
            $am = $this->nullStr($data['apellido_materno'] ?? null);
            $tel = $this->nullStr($data['telefono'] ?? null);
            $query->bindValue(7, $am, $am === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $query->bindValue(8, $tel, $tel === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $rolId = isset($data['rol_id']) && $data['rol_id'] !== '' ? (int) $data['rol_id'] : null;
            $query->bindValue(9, $rolId, $rolId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $query->bindValue(10, trim($this->getVal($data, 'creado_por')), PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $stmt = $conectar->query("SELECT @mensaje AS mensaje");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $mensaje = $row['mensaje'] ?? 'Error desconocido';

            return [
                'ok' => stripos($mensaje, 'correctamente') !== false,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en Usuario::registrar: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar el usuario',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar usuario (sin cambiar contraseña)
     */
    public function actualizar(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL update_usuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $this->getVal($data, 'id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getVal($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(3, trim($this->getVal($data, 'username')), PDO::PARAM_STR);
            $query->bindValue(4, trim($this->getVal($data, 'email')), PDO::PARAM_STR);
            $query->bindValue(5, trim($this->getVal($data, 'primer_nombre')), PDO::PARAM_STR);
            $query->bindValue(6, trim($this->getVal($data, 'apellido_paterno')), PDO::PARAM_STR);
            $am = $this->nullStr($data['apellido_materno'] ?? null);
            $tel = $this->nullStr($data['telefono'] ?? null);
            $query->bindValue(7, $am, $am === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $query->bindValue(8, $tel, $tel === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $est = isset($data['estado']) && $data['estado'] !== '' ? (int) $data['estado'] : null;
            $query->bindValue(9, $est, $est === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $rolId = isset($data['rol_id']) && $data['rol_id'] !== '' ? (int) $data['rol_id'] : null;
            $query->bindValue(10, $rolId, $rolId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $query->bindValue(11, trim($this->getVal($data, 'modificado_por')), PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $stmt = $conectar->query("SELECT @mensaje AS mensaje");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $mensaje = $row['mensaje'] ?? 'Error desconocido';

            return [
                'ok' => stripos($mensaje, 'correctamente') !== false,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en Usuario::actualizar: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar el usuario',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Dar de baja usuario (estado = 0)
     */
    public function dar_de_baja(int $id, int $sede_id, string $modificado_por): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL disable_usuario(?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, $modificado_por, PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $stmt = $conectar->query("SELECT @mensaje AS mensaje");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $mensaje = $row['mensaje'] ?? 'Error desconocido';

            return [
                'ok' => stripos($mensaje, 'correctamente') !== false,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en Usuario::dar_de_baja: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al dar de baja al usuario',
                'detalle' => $e->getMessage()
            ];
        }
    }

    private function getVal(array $data, string $key, $default = null)
    {
        return $data[$key] ?? $default;
    }

    private function nullStr($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        return trim((string) $value);
    }
}
