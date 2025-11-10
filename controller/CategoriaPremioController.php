<?php
/**
 * Controlador de Categorías de Premios
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/CategoriaPremio.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class CategoriaPremioController
{
    private $categoria;

    public function __construct()
    {
        $this->categoria = new CategoriaPremio();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'getAll':
                $this->listar_categorias();
                break;
            case 'getById':
                $this->obtener_categoria_por_id();
                break;
            case 'register':
                $this->registrar_categoria();
                break;
            case 'update':
                $this->actualizar_categoria();
                break;
            case 'delete':
                $this->eliminar_categoria();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    private function listar_categorias(): void
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio']);
                return;
            }

            $sede_id = (int) $_GET['sede_id'];
            $estado = isset($_GET['estado']) ? (int) $_GET['estado'] : null;

            $resultado = $this->categoria->listar_categorias($sede_id, $estado);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar_categorias: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener las categorías']);
        }
    }

    private function obtener_categoria_por_id(): void
    {
        try {
            if (!isset($_GET['id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros id y sede_id son obligatorios']);
                return;
            }

            $id = (int) $_GET['id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->categoria->obtener_categoria_por_id($id, $sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en obtener_categoria_por_id: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener la categoría']);
        }
    }

    private function registrar_categoria(): void
    {
        try {
            $input = $this->obtenerDatosRequest();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $validation = Validator::validarCamposRequeridos($input, [
            'sede_id', 'nombre', 'creado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation);
            return;
        }

        $sedeId = (int) $input['sede_id'];

        $resultado = $this->categoria->registrar_categoria(
            $sedeId,
            trim($input['nombre']),
            $this->nullIfEmpty($input['descripcion'] ?? null),
            $this->nullIfEmpty($input['icono'] ?? null),
            $this->sanitizeColor($input['color_hex'] ?? null),
            $this->parseNullableInt($input['orden'] ?? null),
            trim($input['creado_por'])
        );

        http_response_code($resultado['ok'] ? 201 : 400);
        echo json_encode($resultado);
    }

    private function actualizar_categoria(): void
    {
        try {
            $input = $this->obtenerDatosRequest();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $validation = Validator::validarCamposRequeridos($input, [
            'id', 'sede_id', 'nombre', 'estado', 'modificado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation);
            return;
        }

        $resultado = $this->categoria->actualizar_categoria(
            (int) $input['id'],
            (int) $input['sede_id'],
            trim($input['nombre']),
            $this->nullIfEmpty($input['descripcion'] ?? null),
            $this->nullIfEmpty($input['icono'] ?? null),
            $this->sanitizeColor($input['color_hex'] ?? null),
            $this->parseNullableInt($input['orden'] ?? null),
            $this->parseNullableInt($input['estado'] ?? null),
            trim($input['modificado_por'])
        );

        http_response_code($resultado['ok'] ? 200 : 400);
        echo json_encode($resultado);
    }

    private function eliminar_categoria(): void
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

        $resultado = $this->categoria->eliminar_categoria(
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
        return $value;
    }

    private function parseNullableInt($value): ?int
    {
        $value = $this->nullIfEmpty($value);
        return $value !== null ? (int) $value : null;
    }

    private function sanitizeColor($value): ?string
    {
        $value = $this->nullIfEmpty($value);
        if ($value === null) {
            return null;
        }

        $value = strtoupper($value);
        if (!preg_match('/^#[0-9A-F]{6}$/', $value)) {
            return null;
        }

        return $value;
    }
}


