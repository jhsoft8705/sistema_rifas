<?php
/**
 * Controlador de Perfil
 * Manejo de operaciones para el perfil del usuario autenticado
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Perfil.php');
require_once(__DIR__ . '/../helpers/AuthMiddleware.php');

class PerfilController
{
    private $perfil;

    public function __construct()
    {
        $this->perfil = new Perfil();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Verificar autenticación
        $authData = AuthMiddleware::verificarAutenticacion(true);
        if (!$authData['ok']) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'msj' => 'Acceso denegado: ' . $authData['msj']]);
            return;
        }

        $usuario = $authData['data'];
        $usuario_id = (int) $usuario['usuario_id'];
        $sede_id = (int) $usuario['sede_id'];

        switch ($action) {
            case 'getPerfil':
                $this->get_perfil($usuario_id, $sede_id);
                break;
            case 'updateDatos':
                $this->actualizar_datos($usuario_id, $sede_id);
                break;
            case 'cambiarPassword':
                $this->cambiar_password($usuario_id, $sede_id);
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    /**
     * Obtener perfil del usuario autenticado
     */
    private function get_perfil(int $usuario_id, int $sede_id): void
    {
        try {
            $resultado = $this->perfil->get_perfil($usuario_id, $sede_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_perfil: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener el perfil']);
        }
    }

    /**
     * Actualizar datos del perfil
     */
    private function actualizar_datos(int $usuario_id, int $sede_id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $input = $_POST;
            }

            // Validar campos requeridos
            $camposRequeridos = ['email', 'primer_nombre', 'apellido_paterno'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($input[$campo]) || trim($input[$campo]) === '') {
                    http_response_code(400);
                    echo json_encode(['ok' => false, 'msj' => "El campo $campo es obligatorio"]);
                    return;
                }
            }

            // Validar formato de email
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El formato del correo electrónico no es válido']);
                return;
            }

            $data = [
                'usuario_id' => $usuario_id,
                'sede_id' => $sede_id,
                'email' => trim($input['email']),
                'primer_nombre' => trim($input['primer_nombre']),
                'apellido_paterno' => trim($input['apellido_paterno']),
                'apellido_materno' => isset($input['apellido_materno']) ? trim($input['apellido_materno']) : '',
                'telefono' => isset($input['telefono']) ? trim($input['telefono']) : '',
                'modificado_por' => $usuario_id . ' - ' . ($input['primer_nombre'] ?? 'Usuario')
            ];

            $resultado = $this->perfil->actualizar_datos($data);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en actualizar_datos: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al actualizar los datos']);
        }
    }

    /**
     * Cambiar contraseña
     */
    private function cambiar_password(int $usuario_id, int $sede_id): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $input = $_POST;
            }

            // Validar campos requeridos
            $camposRequeridos = ['password_actual', 'password_nueva', 'password_nueva_confirmar'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($input[$campo]) || trim($input[$campo]) === '') {
                    http_response_code(400);
                    echo json_encode(['ok' => false, 'msj' => "El campo $campo es obligatorio"]);
                    return;
                }
            }

            // Validar que las contraseñas nuevas coincidan
            if ($input['password_nueva'] !== $input['password_nueva_confirmar']) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Las contraseñas nuevas no coinciden']);
                return;
            }

            // Validar longitud mínima de contraseña
            if (strlen($input['password_nueva']) < 6) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'La contraseña debe tener al menos 6 caracteres']);
                return;
            }

            $data = [
                'usuario_id' => $usuario_id,
                'sede_id' => $sede_id,
                'password_actual' => $input['password_actual'],
                'password_nueva' => $input['password_nueva'],
                'modificado_por' => $usuario_id . ' - Usuario'
            ];

            $resultado = $this->perfil->cambiar_password($data);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en cambiar_password: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al cambiar la contraseña']);
        }
    }
}
