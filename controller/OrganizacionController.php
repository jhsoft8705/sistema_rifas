<?php
/**
 * Controlador de Organización (Sede)
 * Mantenimiento: listado (getById) y actualización
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Organizacion.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class OrganizacionController
{
    private $organizacion;

    public function __construct()
    {
        $this->organizacion = new Organizacion();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'getById':
                $this->obtener_por_id();
                break;
            case 'update':
                $this->actualizar();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    private function obtener_por_id(): void
    {
        try {
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro id es obligatorio']);
                return;
            }

            $id = (int) $_GET['id'];
            $resultado = $this->organizacion->get_by_id($id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en obtener_por_id Organización: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener la organización']);
        }
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
            'id', 'codigo', 'nombre', 'modificado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation);
            return;
        }

        $data = [
            'id' => (int) $input['id'],
            'codigo' => trim($input['codigo']),
            'nombre' => trim($input['nombre']),
            'pais' => $this->nullIfEmpty($input['pais'] ?? null),
            'descripcion' => $this->nullIfEmpty($input['descripcion'] ?? null),
            'direccion' => $this->nullIfEmpty($input['direccion'] ?? null),
            'telefono' => $this->nullIfEmpty($input['telefono'] ?? null),
            'email' => $this->nullIfEmpty($input['email'] ?? null),
            'es_principal' => isset($input['es_principal']) ? (int) $input['es_principal'] : null,
            'url_logo' => $this->nullIfEmpty($input['url_logo'] ?? null),
            'url_favicon' => $this->nullIfEmpty($input['url_favicon'] ?? null),
            'url_landing' => $this->nullIfEmpty($input['url_landing'] ?? null),
            'moneda' => $this->nullIfEmpty($input['moneda'] ?? null),
            'simbolo_moneda' => $this->nullIfEmpty($input['simbolo_moneda'] ?? null),
            'codigo_moneda' => $this->nullIfEmpty($input['codigo_moneda'] ?? null),
            'zona_horaria' => $this->nullIfEmpty($input['zona_horaria'] ?? null),
            'requiere_aprobacion_manual' => isset($input['requiere_aprobacion_manual']) ? (int) $input['requiere_aprobacion_manual'] : null,
            'dias_validez_ticket' => isset($input['dias_validez_ticket']) ? (int) $input['dias_validez_ticket'] : null,
            'estado' => isset($input['estado']) ? (int) $input['estado'] : null,
            'modificado_por' => trim($input['modificado_por'])
        ];

        $resultado = $this->organizacion->actualizar($data);

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
