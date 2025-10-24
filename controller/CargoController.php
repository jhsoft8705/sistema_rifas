<?php
/**
 * Controlador de Cargos
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Cargo.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class CargoController
{
    private $cargo;

    public function __construct()
    {
        $this->cargo = new Cargo();
    }

    public function handleRequest($action)
    {
        header('Content-Type: application/json; charset=utf-8');
        
        switch ($action) {
            case 'getAll':
                $this->listar_cargos();
                break;
            case 'getById':
                $this->obtener_cargo_por_id();
                break;
            case 'register':
                $this->registrar_cargo();
                break;
            case 'update':
                $this->actualizar_cargo();
                break;
            case 'delete':
                $this->eliminar_cargo();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    /**
     * Listar cargos de una sede
     * GET /api/cargos/getAll?sede_id=1
     */
    private function listar_cargos()
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio']);
                return;
            }

            $sede_id = (int) $_GET['sede_id'];
            $resultado = $this->cargo->listar_cargos($sede_id);
            
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en listar_cargos: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los cargos']);
        }
    }

    /**
     * Obtener cargo por ID
     * GET /api/cargos/getById?id=1&sede_id=1
     */
    private function obtener_cargo_por_id()
    {
        try {
            if (!isset($_GET['id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros id y sede_id son obligatorios']);
                return;
            }

            $id = (int) $_GET['id'];
            $sede_id = (int) $_GET['sede_id'];
            $resultado = $this->cargo->obtener_cargo_por_id($id, $sede_id);
            
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en obtener_cargo_por_id: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener el cargo']);
        }
    }

    /**
     * Registrar nuevo cargo
     * POST /api/cargos/register
     */
    private function registrar_cargo()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            // Validar campos requeridos usando el helper
            $validation = Validator::validarCamposRequeridos($input, [
                'sede_id', 'nombre_cargo', 'creado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $sede_id = (int) $input['sede_id'];
            $nombre_cargo = trim($input['nombre_cargo']);
            $descripcion = isset($input['descripcion']) ? trim($input['descripcion']) : null;
            $salario_base = isset($input['salario_base']) && $input['salario_base'] !== null ? (float) $input['salario_base'] : null;
            $creado_por = trim($input['creado_por']);

            $resultado = $this->cargo->registrar_cargo(
                $sede_id, 
                $nombre_cargo, 
                $descripcion, 
                $salario_base, 
                $creado_por
            );

            http_response_code($resultado['ok'] ? 201 : 400);
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en registrar_cargo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al registrar el cargo']);
        }
    }

    /**
     * Actualizar cargo
     * POST /api/cargos/update
     */
    private function actualizar_cargo()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            // Validar campos requeridos usando el helper
            $validation = Validator::validarCamposRequeridos($input, [
                'id', 'sede_id', 'nombre_cargo', 'estado', 'modificado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $id = (int) $input['id'];
            $sede_id = (int) $input['sede_id'];
            $nombre_cargo = trim($input['nombre_cargo']);
            $descripcion = isset($input['descripcion']) ? trim($input['descripcion']) : null;
            $salario_base = isset($input['salario_base']) && $input['salario_base'] !== null ? (float) $input['salario_base'] : null;
            $estado = (int) $input['estado'];
            $modificado_por = trim($input['modificado_por']);

            $resultado = $this->cargo->actualizar_cargo(
                $id, 
                $sede_id, 
                $nombre_cargo, 
                $descripcion, 
                $salario_base, 
                $estado, 
                $modificado_por
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en actualizar_cargo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al actualizar el cargo']);
        }
    }

    /**
     * Eliminar cargo
     * POST /api/cargos/delete
     */
    private function eliminar_cargo()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            // Validar campos requeridos usando el helper
            $validation = Validator::validarCamposRequeridos($input, [
                'id', 'sede_id', 'modificado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $id = (int) $input['id'];
            $sede_id = (int) $input['sede_id'];
            $modificado_por = trim($input['modificado_por']);
            $resultado = $this->cargo->eliminar_cargo($id, $sede_id, $modificado_por);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            error_log("Error en eliminar_cargo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al eliminar el cargo']);
        }
    }
}
