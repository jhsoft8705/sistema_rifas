<?php
/**
 * Controlador de Tickets y Comprobantes
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Ticket.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class TicketController
{
    private $ticket;

    public function __construct()
    {
        $this->ticket = new Ticket();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'create':
                $this->crear_ticket();
                break;
            case 'getAll':
                $this->listar_tickets();
                break;
            case 'getByCodigo':
                $this->obtener_ticket_por_codigo();
                break;
            case 'uploadComprobante':
                $this->subir_comprobante();
                break;
            case 'getComprobantes':
                $this->listar_comprobantes();
                break;
            case 'validarComprobante':
                $this->validar_comprobante();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    /**
     * Crear ticket (compra de usuario final)
     */
    private function crear_ticket(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'sede_id',
                'rifa_id',
                'nombres',
                'apellidos',
                'tipo_documento',
                'numero_documento',
                'email',
                'telefono',
                'precio_pagado',
                'cantidad_tickets'
            ]);
            
            // Validar formato de email
            if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El correo electrónico no es válido']);
                return;
            }

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            // Obtener IP del cliente
            $input['ip_compra'] = $_SERVER['REMOTE_ADDR'] ?? null;
            $input['canal_venta'] = $input['canal_venta'] ?? 'WEB';

            $resultado = $this->ticket->crear_ticket($input);
            http_response_code($resultado['ok'] ? 201 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en crear_ticket: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al crear el ticket']);
        }
    }

    /**
     * Listar tickets (admin)
     */
    private function listar_tickets(): void
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio']);
                return;
            }

            $sede_id = (int) $_GET['sede_id'];
            $rifa_id = isset($_GET['rifa_id']) ? (int) $_GET['rifa_id'] : null;
            $estado = isset($_GET['estado']) ? trim($_GET['estado']) : null;

            $resultado = $this->ticket->listar_tickets($sede_id, $rifa_id, $estado);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar_tickets: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los tickets']);
        }
    }

    /**
     * Obtener ticket por código (usuario final)
     */
    private function obtener_ticket_por_codigo(): void
    {
        try {
            if (!isset($_GET['codigo_ticket'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro codigo_ticket es obligatorio']);
                return;
            }

            $codigo_ticket = trim($_GET['codigo_ticket']);
            $resultado = $this->ticket->obtener_ticket_por_codigo($codigo_ticket);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en obtener_ticket_por_codigo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener el ticket']);
        }
    }

    /**
     * Subir comprobante de pago
     */
    private function subir_comprobante(): void
    {
        try {
            // Manejar subida de archivo
            if (!isset($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Error al subir el archivo']);
                return;
            }

            $file = $_FILES['comprobante'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowedTypes)) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Tipo de archivo no permitido. Solo se permiten JPG, PNG y PDF']);
                return;
            }

            if ($file['size'] > $maxSize) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El archivo es demasiado grande. Máximo 5MB']);
                return;
            }

            // Crear directorio si no existe
            $uploadDir = __DIR__ . '/../uploads/comprobantes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'comprobante_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'msj' => 'Error al guardar el archivo']);
                return;
            }

            // Obtener datos del formulario
            $input = [
                'sede_id' => (int) $_POST['sede_id'],
                'ticket_id' => (int) $_POST['ticket_id'],
                'metodo_pago_id' => isset($_POST['metodo_pago_id']) ? (int) $_POST['metodo_pago_id'] : null,
                'numero_operacion' => $_POST['numero_operacion'] ?? null,
                'monto' => (float) $_POST['monto'],
                'fecha_pago' => $_POST['fecha_pago'] ?? null,
                'archivo_comprobante' => 'uploads/comprobantes/' . $fileName,
                'tipo_archivo' => $extension,
                'tamano_archivo' => $file['size'],
                'banco_origen' => $_POST['banco_origen'] ?? null,
                'cuenta_origen' => $_POST['cuenta_origen'] ?? null,
                'titular_origen' => $_POST['titular_origen'] ?? null,
                'observaciones' => $_POST['observaciones'] ?? null,
                'creado_por' => $_POST['creado_por'] ?? 'Usuario'
            ];

            $resultado = $this->ticket->registrar_comprobante($input);
            http_response_code($resultado['ok'] ? 201 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en subir_comprobante: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al subir el comprobante']);
        }
    }

    /**
     * Listar comprobantes pendientes (admin)
     */
    private function listar_comprobantes(): void
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio']);
                return;
            }

            $sede_id = (int) $_GET['sede_id'];
            $estado = isset($_GET['estado']) ? trim($_GET['estado']) : null;

            $resultado = $this->ticket->listar_comprobantes_pendientes($sede_id, $estado);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar_comprobantes: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los comprobantes']);
        }
    }

    /**
     * Validar comprobante (aprobar o rechazar)
     */
    private function validar_comprobante(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'comprobante_id',
                'sede_id',
                'estado',
                'validado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            // Si se rechaza, motivo_rechazo es obligatorio
            if ($input['estado'] === 'RECHAZADO' && empty($input['motivo_rechazo'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El motivo de rechazo es obligatorio']);
                return;
            }

            $resultado = $this->ticket->validar_comprobante($input);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en validar_comprobante: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al validar el comprobante']);
        }
    }
}

