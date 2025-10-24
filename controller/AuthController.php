<?php
/**
 * Controlador de Autenticación
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Auth.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class AuthController
{
    private $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    public function handleRequest($action)
    {
        header('Content-Type: application/json; charset=utf-8');
        
        switch ($action) {
            case 'login':
                $this->login();
                break;
            
            case 'logout':
                $this->logout();
                break;
            
            case 'verificar':
                $this->verificar_sesion();
                break;
            
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    /**
     * Login de usuario
     * POST /api/auth/login
     * Body: { username, password, sede_id? }
     */
    private function login()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            // Validar campos requeridos
            $validation = Validator::validarCamposRequeridos($input, ['username', 'password']);
            
            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            // Obtener IP y User Agent
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            // Preparar datos
            $username = trim($input['username']);
            $password = $input['password']; // No hacer trim a la contraseña
            $sede_id = isset($input['sede_id']) ? (int) $input['sede_id'] : null;

            // Intentar login
            $resultado = $this->auth->login($username, $password, $ip_address, $user_agent, $sede_id);

            if ($resultado['ok']) {
                // Guardar en sesión
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['usuario'] = $resultado['data'];
                $_SESSION['token'] = $resultado['data']['token'];

                http_response_code(200);
                echo json_encode($resultado);
            } else {
                // Determinar código de error según el mensaje
                $status_code = 401; // Por defecto unauthorized
                
                if (stripos($resultado['msj'], 'no encontrado') !== false) {
                    $status_code = 404;
                } elseif (stripos($resultado['msj'], 'bloqueada') !== false) {
                    $status_code = 403;
                } elseif (stripos($resultado['msj'], 'inactivo') !== false) {
                    $status_code = 403;
                }

                http_response_code($status_code);
                echo json_encode($resultado);
            }

        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al procesar la autenticación']);
        }
    }

    /**
     * Logout de usuario
     * POST /api/auth/logout
     * Body: { token }
     */
    private function logout()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            // Validar campos requeridos
            $validation = Validator::validarCamposRequeridos($input, ['token']);
            
            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            // Obtener IP
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $token = trim($input['token']);

            // Realizar logout
            $resultado = $this->auth->logout($token, $ip_address);

            // Destruir sesión PHP
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_destroy();

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);

        } catch (Exception $e) {
            error_log("Error en logout: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al cerrar sesión']);
        }
    }

    /**
     * Verificar sesión activa
     * POST /api/auth/verificar
     * Body: { token }
     */
    private function verificar_sesion()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            // Validar campos requeridos
            $validation = Validator::validarCamposRequeridos($input, ['token']);
            
            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $token = trim($input['token']);

            // Verificar sesión
            $resultado = $this->auth->verificar_sesion($token);

            http_response_code($resultado['ok'] ? 200 : 401);
            echo json_encode($resultado);

        } catch (Exception $e) {
            error_log("Error en verificar_sesion: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al verificar la sesión']);
        }
    }
}
