<?php
/**
 * Middleware de Autenticación
 * Valida que el usuario tenga un token válido antes de acceder a rutas protegidas
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Auth.php');

class AuthMiddleware
{
    /**
     * Verificar autenticación por token
     * @param bool $return_data Si es true, retorna los datos del usuario en lugar de enviar respuesta
     * @return array|void Datos del usuario si $return_data es true, void en caso contrario
     */
    public static function verificarAutenticacion($return_data = false)
    {
        // Obtener token del header Authorization
        $headers = getallheaders();
        $token = null;

        // Buscar token en el header Authorization (Bearer token)
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        // Si no hay token en Authorization, buscar en header X-Auth-Token
        if (!$token && isset($headers['X-Auth-Token'])) {
            $token = $headers['X-Auth-Token'];
        }

        // Si no hay token, denegar acceso
        if (!$token) {
            if ($return_data) {
                return ['ok' => false, 'msj' => 'Token no proporcionado'];
            }
            
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok' => false,
                'msj' => 'Acceso denegado: Token no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Verificar token con el modelo Auth
        $auth = new Auth();
        $resultado = $auth->verificar_token($token);

        if (!$resultado['ok']) {
            if ($return_data) {
                return $resultado;
            }
            
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok' => false,
                'msj' => 'Acceso denegado: ' . $resultado['msj']
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Token válido
        if ($return_data) {
            return $resultado;
        }

        // Guardar datos del usuario en una variable global para usarlos en los controladores
        $GLOBALS['usuario_autenticado'] = $resultado['data'];
    }

    /**
     * Obtener datos del usuario autenticado
     * @return array|null Datos del usuario o null si no está autenticado
     */
    public static function obtenerUsuarioAutenticado()
    {
        return $GLOBALS['usuario_autenticado'] ?? null;
    }

    /**
     * Verificar que el usuario pertenece a la sede especificada
     * @param int $sede_id ID de la sede a verificar
     * @return bool True si pertenece, False si no
     */
    public static function verificarSede($sede_id)
    {
        $usuario = self::obtenerUsuarioAutenticado();
        
        if (!$usuario) {
            return false;
        }

        return (int)$usuario['sede_id'] === (int)$sede_id;
    }
}


