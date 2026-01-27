<?php
/**
 * Controlador de Personas
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Persona.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class PersonaController
{
    private $persona;

    public function __construct()
    {
        $this->persona = new Persona();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'getAll':
                $this->listar_personas();
                break;
            case 'getById':
                $this->obtener_persona_por_id();
                break;
            case 'register':
                $this->registrar_persona();
                break;
            case 'update':
                $this->actualizar_persona();
                break;
            case 'delete':
                $this->eliminar_persona();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    private function listar_personas(): void
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio']);
                return;
            }

            $sede_id = (int) $_GET['sede_id'];
            $resultado = $this->persona->listar_personas($sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en listar_personas: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener las personas'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function obtener_persona_por_id(): void
    {
        try {
            if (!isset($_GET['id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros id y sede_id son obligatorios']);
                return;
            }

            $id = (int) $_GET['id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->persona->obtener_persona_por_id($id, $sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en obtener_persona_por_id: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener la persona'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function registrar_persona(): void
    {
        try {
            $input = $this->obtenerDatosRequest();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $validation = Validator::validarCamposRequeridos($input, [
            'sede_id', 'nombres', 'apellidos', 'tipo_documento', 'numero_documento', 'creado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation, JSON_UNESCAPED_UNICODE);
            return;
        }

        $resultado = $this->persona->registrar_persona(
            (int) $input['sede_id'],
            trim($input['nombres']),
            trim($input['apellidos']),
            trim($input['tipo_documento']),
            trim($input['numero_documento']),
            $this->nullIfEmpty($input['email'] ?? null),
            $this->nullIfEmpty($input['telefono'] ?? null),
            $this->nullIfEmpty($input['direccion'] ?? null),
            $this->nullIfEmpty($input['ciudad'] ?? null),
            $this->nullIfEmpty($input['pais'] ?? null),
            trim($input['creado_por'])
        );

        http_response_code($resultado['ok'] ? 201 : 400);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    private function actualizar_persona(): void
    {
        try {
            $input = $this->obtenerDatosRequest();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $validation = Validator::validarCamposRequeridos($input, [
            'id', 'sede_id', 'nombres', 'apellidos', 'tipo_documento', 'numero_documento', 'modificado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation, JSON_UNESCAPED_UNICODE);
            return;
        }

        $resultado = $this->persona->actualizar_persona(
            (int) $input['id'],
            (int) $input['sede_id'],
            trim($input['nombres']),
            trim($input['apellidos']),
            trim($input['tipo_documento']),
            trim($input['numero_documento']),
            $this->nullIfEmpty($input['email'] ?? null),
            $this->nullIfEmpty($input['telefono'] ?? null),
            $this->nullIfEmpty($input['direccion'] ?? null),
            $this->nullIfEmpty($input['ciudad'] ?? null),
            $this->nullIfEmpty($input['pais'] ?? null),
            trim($input['modificado_por'])
        );

        http_response_code($resultado['ok'] ? 200 : 400);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    private function eliminar_persona(): void
    {
        try {
            $input = $this->obtenerDatosRequest();

            $validation = Validator::validarCamposRequeridos($input, [
                'id', 'sede_id'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation, JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $this->persona->eliminar_persona(
                (int) $input['id'],
                (int) $input['sede_id']
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en eliminar_persona: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al eliminar la persona'], JSON_UNESCAPED_UNICODE);
        }
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
}
