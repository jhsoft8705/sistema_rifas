<?php
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controllers/PruebaController.php");

function RoutesPrueba($url, $method): void
{
    $controller = new PruebaController();

    // Rutas y los métodos permitidos
    $routes = [
        'api/prueba/getAll' => ['GET'],
        'api/prueba/getById' => ['GET'],
        'api/prueba/registrar' => ['POST'],
        'api/prueba/actualizar' => ['POST'],
        'api/prueba/eliminar' => ['POST'],
    ];

    // Verificar si la URL está en las rutas definidas
    if (array_key_exists($url, $routes)) {
        $allowedMethods = $routes[$url];
        if (in_array($method, $allowedMethods)) {
            switch ($url) {
                case 'api/prueba/getAll':
                    $controller->handleRequest('get');
                    break;
                case 'api/prueba/getById':
                    $controller->handleRequest('getById');
                    break;
                case 'api/prueba/registrar':
                    $controller->handleRequest('registrar');
                    break;
                case 'api/prueba/actualizar':
                    $controller->handleRequest('actualizar');
                    break;
                case 'api/prueba/eliminar':
                    $controller->handleRequest('eliminar');
                    break;
            }
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode([
                'status' => 'error',
                'message' => "Método $method no permitido para la ruta $url"
            ]);
        }
    } else {
        // Ruta no encontrada
        header("HTTP/1.1 404 Not Found");
        echo json_encode([
            'status' => 'error',
            'message' => 'Ruta no encontrada'
        ]);
    }
}
