<?php
/**
 * Modelo Auth
 * Manejo de autenticación de usuarios
 */
class Auth extends Conectar
{
    /**
     * Login de usuario
     * @param string $username Usuario o correo
     * @param string $password Contraseña en texto plano
     * @param string $ip_address IP del cliente
     * @param string $user_agent User agent del navegador
     * @param int|null $sede_id ID de la sede (opcional)
     * @return array Resultado del login
     */
    public function login($username, $password, $ip_address, $user_agent, $sede_id = null)
    {
        try {
            $conectar = parent::Conexion();
            
            // Obtener el hash de la contraseña del usuario
            $sqlHash = "SELECT password_hash FROM usuarios WHERE username = ?";
            $queryHash = $conectar->prepare($sqlHash);
            $queryHash->bindValue(1, $username, PDO::PARAM_STR);
            $queryHash->execute();
            $usuario = $queryHash->fetch(PDO::FETCH_ASSOC);
            
            // Si no existe el usuario o la contraseña es incorrecta, 
            // dejamos que el stored procedure maneje el error
            $passwordToSend = $password;
            
            // Si el usuario existe, verificamos la contraseña con password_verify()
            if ($usuario && !empty($usuario['password_hash'])) {
                if (password_verify($password, $usuario['password_hash'])) {
                    // Contraseña correcta: enviamos el hash para que el SP lo acepte
                    $passwordToSend = $usuario['password_hash'];
                }
                // Si es incorrecta, enviamos la contraseña en texto plano
                // y el SP manejará el error
            }
            
            // Llamar al stored procedure
            $sql = "CALL sp_Login(?, ?, ?, ?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $username, PDO::PARAM_STR);
            $query->bindValue(2, $passwordToSend, PDO::PARAM_STR);
            $query->bindValue(3, $ip_address, PDO::PARAM_STR);
            $query->bindValue(4, $user_agent, PDO::PARAM_STR);
            if ($sede_id === null) {
                $query->bindValue(5, null, PDO::PARAM_NULL);
            } else {
                $query->bindValue(5, $sede_id, PDO::PARAM_INT);
            }
            $query->execute();
            
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            
            if ($data && $data['resultado'] == 1) {
                return [
                    'ok' => true,
                    'msj' => $data['mensaje'],
                    'data' => [
                        'usuario_id' => $data['usuario_id'],
                        'token' => $data['token_sesion'],
                        'fecha_expiracion' => $data['fecha_expiracion'],
                        'sede_id' => $data['sede_id'],
                        'sede_nombre' => $data['sede_nombre'],
                        'empleado_id' => $data['empleado_id'],
                        'nombre_completo' => $data['nombre_completo'],
                        'rol_id' => $data['rol_id'],
                        'rol_nombre' => $data['rol_nombre'],
                        'debe_cambiar_password' => $data['debe_cambiar_password']
                    ]
                ];
            } else {
                return [
                    'ok' => false,
                    'msj' => $data['mensaje'] ?? 'Error en el login',
                    'data' => null
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al procesar el login',
                'data' => null
            ];
        }
    }

    /**
     * Verificar token de sesión
     * @param string $token Token de sesión
     * @return array Resultado de la verificación
     */
    public function verificar_token($token)
    {
        try {
            $conectar = parent::Conexion();
            
            $sql = "SELECT 
                        s.usuario_id,
                        s.sede_id,
                        s.fecha_expiracion,
                        s.activa,
                        u.username,
                        u.primer_nombre,
                        u.apellido_paterno,
                        u.estado AS usuario_estado
                    FROM sesiones s
                    INNER JOIN usuarios u ON s.usuario_id = u.id
                    WHERE s.token_sesion = ? 
                      AND s.activa = 1
                      AND s.fecha_expiracion > NOW()";
            
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $token, PDO::PARAM_STR);
            $query->execute();
            
            $data = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($data && $data['usuario_estado'] == 1) {
                // Actualizar última actividad
                $sqlUpdate = "UPDATE sesiones 
                             SET fecha_ultima_actividad = NOW() 
                             WHERE token_sesion = ?";
                $queryUpdate = $conectar->prepare($sqlUpdate);
                $queryUpdate->bindValue(1, $token, PDO::PARAM_STR);
                $queryUpdate->execute();
                
                return [
                    'ok' => true,
                    'msj' => 'Token válido',
                    'data' => [
                        'usuario_id' => $data['usuario_id'],
                        'sede_id' => $data['sede_id'],
                        'username' => $data['username'],
                        'nombre_completo' => trim($data['primer_nombre'] . ' ' . $data['apellido_paterno'])
                    ]
                ];
            } else {
                return [
                    'ok' => false,
                    'msj' => 'Token inválido o expirado',
                    'data' => null
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error en verificar_token: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al verificar el token',
                'data' => null
            ];
        }
    }

    /**
     * Logout de usuario
     * @param string $token Token de sesión
     * @param string|null $ip_address IP del cliente
     * @return array Resultado del logout
     */
    public function logout($token, $ip_address = null)
    {
        try {
            $conectar = parent::Conexion();
            
            $sql = "CALL sp_Logout(?, ?)";
            
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $token, PDO::PARAM_STR);
            if ($ip_address === null) {
                $query->bindValue(2, null, PDO::PARAM_NULL);
            } else {
                $query->bindValue(2, $ip_address, PDO::PARAM_STR);
            }
            $query->execute();
            
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            
            if ($data && $data['resultado'] == 1) {
                return [
                    'ok' => true,
                    'msj' => $data['mensaje']
                ];
            } else {
                return [
                    'ok' => false,
                    'msj' => $data['mensaje'] ?? 'Error en el logout'
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error en logout: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al procesar el logout'
            ];
        }
    }

    /**
     * Verificar sesión activa
     * @param string $token Token de sesión
     * @return array Resultado de la verificación
     */
    public function verificar_sesion($token)
    {
        try {
            $conectar = parent::Conexion();
            
            $sql = "CALL sp_ValidarSesion(?, ?)";
            
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $token, PDO::PARAM_STR);
            $query->bindValue(2, null, PDO::PARAM_NULL);
            $query->execute();
            
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            
            if ($data && $data['resultado'] == 1) {
                return [
                    'ok' => true,
                    'msj' => $data['mensaje'],
                    'data' => [
                        'usuario_id' => $data['usuario_id'],
                        'sede_id' => $data['sede_id'],
                        'empleado_id' => null,
                        'nombre_completo' => $data['nombre_completo'] ?? null
                    ]
                ];
            } else {
                return [
                    'ok' => false,
                    'msj' => $data['mensaje'] ?? 'Sesión inválida',
                    'data' => null
                ];
            }
            
        } catch (PDOException $e) {
            error_log("Error en verificar_sesion: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al verificar la sesión',
                'data' => null
            ];
        }
    }
}
