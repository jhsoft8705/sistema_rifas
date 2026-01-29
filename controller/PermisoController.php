<?php
/**
 * Controlador de Permisos
 * Manejo de operaciones CRUD para permisos
 */
require_once(__DIR__ . '/../config/Conexion.php');
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Permiso.php');
require_once(__DIR__ . '/../helpers/AuthMiddleware.php');

class PermisoController
{
    private $permiso;

    public function __construct()
    {
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
            case 'getPermisosUsuario':
                $this->obtener_permisos_usuario($sede_id);
                break;
            case 'register':
                $this->registrar($sede_id, $usuario);
                break;
            case 'update':
                $this->actualizar($sede_id, $usuario);
                break;
            case 'verificarPermiso':
                $this->verificar_permiso($sede_id);
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    /**
     * Listar permisos
     */
    private function listar(int $sede_id): void
    {
        try {
            $resultado = $this->permiso->listar_permisos($sede_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en listar permisos: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al listar permisos']);
        }
    }

    /**
     * Obtener permiso por ID
     */
    private function obtener_por_id(int $sede_id): void
    {
        try {
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro id es obligatorio']);
                return;
            }

            $permiso_id = (int) $_GET['id'];
            $resultado = $this->permiso->get_by_id($permiso_id, $sede_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en obtener permiso: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener el permiso']);
        }
    }

    /**
     * Obtener permisos del usuario autenticado
     */
    private function obtener_permisos_usuario(int $sede_id): void
    {
        try {
            $authData = AuthMiddleware::verificarAutenticacion(true);
            $usuario_id = (int) $authData['data']['usuario_id'];
            
            $resultado = $this->permiso->get_permisos_usuario($usuario_id, $sede_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en obtener permisos usuario: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener permisos']);
        }
    }

    /**
     * Registrar nuevo permiso
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
                echo json_encode(['ok' => false, 'msj' => 'El nombre del permiso es obligatorio']);
                return;
            }

            if (!isset($input['modulo']) || trim($input['modulo']) === '') {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El módulo es obligatorio']);
                return;
            }

            if (!isset($input['accion']) || trim($input['accion']) === '') {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'La acción es obligatoria']);
                return;
            }

            $data = [
                'sede_id' => $sede_id,
                'nombre' => trim($input['nombre']),
                'descripcion' => isset($input['descripcion']) ? trim($input['descripcion']) : '',
                'modulo' => trim($input['modulo']),
                'accion' => trim($input['accion']),
                'creado_por' => $usuario['usuario_id'] . ' - ' . ($usuario['nombre_completo'] ?? 'Usuario')
            ];

            $resultado = $this->permiso->registrar($data);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en registrar permiso: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al registrar el permiso']);
        }
    }

    /**
     * Actualizar permiso
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
                echo json_encode(['ok' => false, 'msj' => 'El id del permiso es obligatorio']);
                return;
            }

            $data = [
                'id' => (int) $input['id'],
                'sede_id' => $sede_id,
                'nombre' => trim($input['nombre']),
                'descripcion' => isset($input['descripcion']) ? trim($input['descripcion']) : '',
                'modulo' => trim($input['modulo']),
                'accion' => trim($input['accion']),
                'estado' => isset($input['estado']) ? (int) $input['estado'] : 1,
                'modificado_por' => $usuario['usuario_id'] . ' - ' . ($usuario['nombre_completo'] ?? 'Usuario')
            ];

            $resultado = $this->permiso->actualizar($data);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en actualizar permiso: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al actualizar el permiso']);
        }
    }

    /**
     * Verificar si usuario tiene un permiso
     */
    private function verificar_permiso(int $sede_id): void
    {
        try {
            if (!isset($_GET['permiso'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro permiso es obligatorio']);
                return;
            }

            $authData = AuthMiddleware::verificarAutenticacion(true);
            $usuario_id = (int) $authData['data']['usuario_id'];
            $permiso_nombre = trim($_GET['permiso']);

            $tiene_permiso = $this->permiso->verificar_permiso($usuario_id, $sede_id, $permiso_nombre);
            
            http_response_code(200);
            echo json_encode([
                'ok' => true,
                'tiene_permiso' => $tiene_permiso,
                'permiso' => $permiso_nombre
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en verificar permiso: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al verificar permiso']);
        }
    }
}
