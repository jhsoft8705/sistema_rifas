<?php
/**
 * Modelo Perfil
 * Manejo de operaciones para el perfil del usuario autenticado
 */
class Perfil extends Conectar
{
    /**
     * Obtener perfil del usuario autenticado
     */
    public function get_perfil(int $usuario_id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL get_perfil_usuario(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $usuario_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Perfil obtenido correctamente' : 'Perfil no encontrado',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en Perfil::get_perfil: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener el perfil',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar datos del perfil (sin contraseña)
     */
    public function actualizar_datos(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL update_perfil_datos(?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            
            $query->bindValue(1, $this->getValue($data, 'usuario_id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(3, trim($this->getValue($data, 'email')), PDO::PARAM_STR);
            $query->bindValue(4, trim($this->getValue($data, 'primer_nombre')), PDO::PARAM_STR);
            $query->bindValue(5, trim($this->getValue($data, 'apellido_paterno')), PDO::PARAM_STR);
            $this->bindNullable($query, 6, trim($this->getValue($data, 'apellido_materno', '')), PDO::PARAM_STR);
            $this->bindNullable($query, 7, trim($this->getValue($data, 'telefono', '')), PDO::PARAM_STR);
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
            error_log("Error en Perfil::actualizar_datos: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar los datos del perfil',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Cambiar contraseña del usuario
     */
    public function cambiar_password(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            
            // Primero verificar que la contraseña actual sea correcta
            $usuario_id = $this->getValue($data, 'usuario_id');
            $sede_id = $this->getValue($data, 'sede_id');
            $password_actual = $this->getValue($data, 'password_actual');
            
            // Obtener el hash de la contraseña actual del usuario
            $sqlHash = "SELECT password_hash FROM usuarios WHERE id = ? AND sede_id = ? AND estado = 1";
            $queryHash = $conectar->prepare($sqlHash);
            $queryHash->bindValue(1, $usuario_id, PDO::PARAM_INT);
            $queryHash->bindValue(2, $sede_id, PDO::PARAM_INT);
            $queryHash->execute();
            $usuario = $queryHash->fetch(PDO::FETCH_ASSOC);
            $queryHash->closeCursor();
            
            if (!$usuario) {
                return [
                    'ok' => false,
                    'msj' => 'Usuario no encontrado'
                ];
            }
            
            // Verificar contraseña actual
            if (!password_verify($password_actual, $usuario['password_hash'])) {
                return [
                    'ok' => false,
                    'msj' => 'La contraseña actual es incorrecta'
                ];
            }
            
            // Hash de la nueva contraseña
            $password_nueva_hash = password_hash($this->getValue($data, 'password_nueva'), PASSWORD_DEFAULT);
            
            // Llamar al procedimiento almacenado
            $sql = "CALL cambiar_password_perfil(?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            
            $query->bindValue(1, $usuario_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, $password_actual, PDO::PARAM_STR); // Se pasa pero no se usa realmente
            $query->bindValue(4, $password_nueva_hash, PDO::PARAM_STR);
            $query->bindValue(5, trim($this->getValue($data, 'modificado_por')), PDO::PARAM_STR);
            
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
            error_log("Error en Perfil::cambiar_password: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al cambiar la contraseña',
                'detalle' => $e->getMessage()
            ];
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
