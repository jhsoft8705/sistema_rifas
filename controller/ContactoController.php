<?php
/**
 * Controlador de Contacto (Contáctanos - Landing)
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Contacto.php');

class ContactoController
{
    private $contacto;

    public function __construct()
    {
        $this->contacto = new Contacto();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'register':
                $this->registrar();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida'], JSON_UNESCAPED_UNICODE);
                break;
        }
    }

    /**
     * Registrar mensaje de contacto desde la landing
     */
    private function registrar(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $input = $_POST;
            }
            if (empty($input)) {
                $input = $_POST;
            }

            $nombre = trim($input['nombre'] ?? $input['name'] ?? '');
            $email = trim($input['email'] ?? '');
            $telefono = trim($input['telefono'] ?? $input['phone'] ?? '');
            $asunto = trim($input['asunto'] ?? $input['subject'] ?? '');
            $mensaje = trim($input['mensaje'] ?? $input['comments'] ?? $input['message'] ?? '');

            if ($nombre === '') {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El nombre es obligatorio'], JSON_UNESCAPED_UNICODE);
                return;
            }
            if ($email === '') {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El correo electrónico es obligatorio'], JSON_UNESCAPED_UNICODE);
                return;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El correo electrónico no es válido'], JSON_UNESCAPED_UNICODE);
                return;
            }
            if ($asunto === '') {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El asunto es obligatorio'], JSON_UNESCAPED_UNICODE);
                return;
            }
            if ($mensaje === '') {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El mensaje es obligatorio'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $data = [
                'sede_id' => isset($input['sede_id']) ? (int) $input['sede_id'] : null,
                'nombre' => $nombre,
                'email' => $email,
                'telefono' => $telefono !== '' ? $telefono : null,
                'asunto' => $asunto,
                'mensaje' => $mensaje,
                'ip_origen' => $_SERVER['REMOTE_ADDR'] ?? null
            ];

            $resultado = $this->contacto->registrar($data);

            http_response_code($resultado['ok'] ? 201 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en ContactoController::registrar: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'msj' => 'Error al enviar el mensaje. Intenta de nuevo más tarde.'
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
