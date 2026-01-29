<?php
/**
 * Controlador de Roles
 * Manejo de operaciones CRUD para roles y asignación de permisos
 */
require_once(__DIR__ . '/../config/Conexion.php');
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Rol.php');
require_once(__DIR__ . '/../models/Permiso.php');
require_once(__DIR__ . '/../helpers/AuthMiddleware.php');

class RolController
{
    private $rol;
    private $permiso;

    public function __construct()
    {
        $this->rol = new Rol();
        $this->permiso = new Permiso();
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
        $sede_id = (int) $usuario['sede_id'];

        switch ($action) {
            case 'getAll':
                $this->listar($sede_id);
                break;
            case 'getById':
                $this->obtener_por_id($sede_id);
                break;
            case 'register':
                $this->registrar($sede_id, $usuario);
                break;
            case 'update':
                $this->actualizar($sede_id, $usuario);
                break;
            case 'getPermisos':
                $this->obtener_permisos($sede_id);
                break;
            case 'asignarPermisos':
                $this->asignar_permisos($sede_id, $usuario);
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    /**
     * Listar roles
     */
    private function listar(int $sede_id): void
    {
        try {
            $resultado = $this->rol->listar_completo($sede_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en listar roles: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al listar roles']);
        }
    }

    /**
     * Obtener rol por ID
     */
    private function obtener_por_id(int $sede_id): void
    {
        try {
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro id es obligatorio']);
                return;
            }

            $rol_id = (int) $_GET['id'];
            $resultado = $this->rol->get_by_id($rol_id, $sede_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en obtener rol: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener el rol']);
        }
    }

    /**
     * Registrar nuevo rol
     */
    private function registrar(int $sede_id, array $usuario): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            // Validar campos requeridos
            if (!isset($input['nombre']) || trim($input['nombre']) === '') {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El nombre del rol es obligatorio']);
                return;
            }

            $data = [
                'sede_id' => $sede_id,
                'nombre' => trim($input['nombre']),
                'descripcion' => isset($input['descripcion']) ? trim($input['descripcion']) : '',
                'nivel_acceso' => isset($input['nivel_acceso']) ? (int) $input['nivel_acceso'] : 1,
                'creado_por' => $usuario['usuario_id'] . ' - ' . ($usuario['nombre_completo'] ?? 'Usuario')
            ];

            $resultado = $this->rol->registrar($data);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en registrar rol: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al registrar el rol']);
        }
    }

    /**
     * Actualizar rol
     */
    private function actualizar(int $sede_id, array $usuario): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            if (!isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El id del rol es obligatorio']);
                return;
            }

            $data = [
                'id' => (int) $input['id'],
                'sede_id' => $sede_id,
                'nombre' => trim($input['nombre']),
                'descripcion' => isset($input['descripcion']) ? trim($input['descripcion']) : '',
                'nivel_acceso' => isset($input['nivel_acceso']) ? (int) $input['nivel_acceso'] : 1,
                'estado' => isset($input['estado']) ? (int) $input['estado'] : 1,
                'modificado_por' => $usuario['usuario_id'] . ' - ' . ($usuario['nombre_completo'] ?? 'Usuario')
            ];

            $resultado = $this->rol->actualizar($data);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en actualizar rol: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al actualizar el rol']);
        }
    }

    /**
     * Obtener permisos de un rol
     */
    private function obtener_permisos(int $sede_id): void
    {
        try {
            if (!isset($_GET['rol_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro rol_id es obligatorio']);
                return;
            }

            $rol_id = (int) $_GET['rol_id'];
            $resultado = $this->rol->get_permisos_rol($rol_id, $sede_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en obtener permisos del rol: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener permisos']);
        }
    }

    /**
     * Asignar permisos a un rol
     */
    private function asignar_permisos(int $sede_id, array $usuario): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            if (!isset($input['rol_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El rol_id es obligatorio']);
                return;
            }

            $rol_id = (int) $input['rol_id'];
            $permisos_ids = isset($input['permisos_ids']) && is_array($input['permisos_ids']) 
                ? $input['permisos_ids'] 
                : [];

            $asignado_por = $usuario['usuario_id'] . ' - ' . ($usuario['nombre_completo'] ?? 'Usuario');

            $resultado = $this->rol->asignar_permisos($rol_id, $sede_id, $permisos_ids, $asignado_por);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en asignar permisos: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al asignar permisos']);
        }
    }
}
