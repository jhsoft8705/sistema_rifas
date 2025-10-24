<?php
require_once('../config/conexion.php');
require_once('../models/Prueba.php');

class PruebaController
{
    // Método que maneja las diferentes acciones del controlador de distrito
    public function handleRequest($action)
    {
        switch ($action) {
            case 'get':
                $this->listar_prueba();
                break;
            default:
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
                break;
        }
    }

    // Listar todos los distritos
    private function listar_prueba()
    {
        try {
            $prueba = new Prueba();
            $resultado = $prueba->listar_prueba();
            if ($resultado['ok']) {
                http_response_code(200);
            } else {
                http_response_code(400);
            }
            echo json_encode($resultado);
        } catch (Exception $e) {
            $respuesta = [
                'ok' => false,
                'msj' => 'Error al obtener datos',
                'prueba' => (object) []
            ];
            error_log("Error al listar prueba: " . $e->getMessage());
        }
    }

   
    
   
    
  

    
}
