<?php
/**
 * Controlador de Usuarios
 * Mantenimiento: listar, registrar, actualizar, dar de baja
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Usuario.php');
require_once(__DIR__ . '/../models/Rol.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class UsuarioController
{
    private $usuario;
    private $rol;

    public function __construct()
    {
        $this->usuario = new Usuario();
        $this->rol = new Rol();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'getAll':
                $this->listar();
                break;
            case 'getById':
                $this->obtener_por_id();
                break;
            case 'getRoles':
                $this->listar_roles();
                break;
            case 'register':
                $this->registrar();
                break;
            case 'update':
                $this->actualizar();
                break;
            case 'disable':
                $this->dar_de_baja();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    private function listar_roles(): void
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio']);
                return;
            }
            $sede_id = (int) $_GET['sede_id'];
            $resultado = $this->rol->listar_por_sede($sede_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar roles: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al listar roles']);
        }
    }

    private function listar(): void
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio']);
                return;
            }

            $sede_id = (int) $_GET['sede_id'];
            $estado = isset($_GET['estado']) ? (int) $_GET['estado'] : null;

            $resultado = $this->usuario->listar_usuarios($sede_id, $estado);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar usuarios: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al listar usuarios']);
        }
    }

    private function obtener_por_id(): void
    {
        try {
            if (!isset($_GET['id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros id y sede_id son obligatorios']);
                return;
            }

            $id = (int) $_GET['id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->usuario->get_by_id($id, $sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en obtener usuario: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener el usuario']);
        }
    }

    private function registrar(): void
    {
        try {
            $input = $this->obtenerDatosRequest();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $validation = Validator::validarCamposRequeridos($input, [
            'sede_id', 'username', 'password', 'email', 'primer_nombre', 'apellido_paterno', 'rol_id', 'creado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation);
            return;
        }

        $password_hash = password_hash(trim($input['password']), PASSWORD_DEFAULT);
        if ($password_hash === false) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => 'Error al generar contraseña']);
            return;
        }

        $data = [
            'sede_id' => (int) $input['sede_id'],
            'username' => trim($input['username']),
            'password_hash' => $password_hash,
            'email' => trim($input['email']),
            'primer_nombre' => trim($input['primer_nombre']),
            'apellido_paterno' => trim($input['apellido_paterno']),
            'apellido_materno' => $this->nullIfEmpty($input['apellido_materno'] ?? null),
            'telefono' => $this->nullIfEmpty($input['telefono'] ?? null),
            'rol_id' => isset($input['rol_id']) ? (int) $input['rol_id'] : null,
            'creado_por' => trim($input['creado_por'])
        ];

        $resultado = $this->usuario->registrar($data);

        http_response_code($resultado['ok'] ? 201 : 400);
        echo json_encode($resultado);
    }

    private function actualizar(): void
    {
        try {
            $input = $this->obtenerDatosRequest();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $validation = Validator::validarCamposRequeridos($input, [
            'id', 'sede_id', 'username', 'email', 'primer_nombre', 'apellido_paterno', 'modificado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation);
            return;
        }

        $data = [
            'id' => (int) $input['id'],
            'sede_id' => (int) $input['sede_id'],
            'username' => trim($input['username']),
            'email' => trim($input['email']),
            'primer_nombre' => trim($input['primer_nombre']),
            'apellido_paterno' => trim($input['apellido_paterno']),
            'apellido_materno' => $this->nullIfEmpty($input['apellido_materno'] ?? null),
            'telefono' => $this->nullIfEmpty($input['telefono'] ?? null),
            'estado' => isset($input['estado']) ? (int) $input['estado'] : null,
            'rol_id' => isset($input['rol_id']) ? (int) $input['rol_id'] : null,
            'modificado_por' => trim($input['modificado_por'])
        ];

        $resultado = $this->usuario->actualizar($data);

        http_response_code($resultado['ok'] ? 200 : 400);
        echo json_encode($resultado);
    }

    private function dar_de_baja(): void
    {
        try {
            $input = $this->obtenerDatosRequest();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $validation = Validator::validarCamposRequeridos($input, [
            'id', 'sede_id', 'modificado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation);
            return;
        }

        $resultado = $this->usuario->dar_de_baja(
            (int) $input['id'],
            (int) $input['sede_id'],
            trim($input['modificado_por'])
        );

        http_response_code($resultado['ok'] ? 200 : 400);
        echo json_encode($resultado);
    }

    private function obtenerDatosRequest(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON inválido');
            }
            return $this->sanitizeArray($input ?? []);
        }
        return $this->sanitizeArray($_POST ?? []);
    }

    private function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }
        return $data;
    }

    private function nullIfEmpty($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }
        return is_string($value) ? trim($value) : $value;
    }
}
