<?php
/**
 * Rutas para el módulo de Cargos
 * Maneja el enrutamiento de peticiones HTTP para operaciones CRUD de cargos
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/CargoController.php");

/**
 * Función principal de enrutamiento para cargos
 * @param string $url URL de la petición
 * @param string $method Método HTTP (GET, POST, PUT, DELETE)
 */
function RoutesCargos($url, $method): void
{
    // Inicializar controlador
    $controller = new CargoController();
 
    // Definir rutas disponibles y sus métodos HTTP permitidos
    $routes = [
        'api/cargos/getAll'    => ['GET'],              // Listar todos los cargos de una sede
        'api/cargos/getById'   => ['GET'],              // Obtener un cargo específico
        'api/cargos/register'  => ['POST'],             // Registrar nuevo cargo
        'api/cargos/update'    => ['PUT', 'POST'],      // Actualizar cargo existente
        'api/cargos/delete'    => ['DELETE', 'POST']    // Eliminar cargo (lógico)
    ];
 
    // Verificar si la ruta existe
    if (!array_key_exists($url, $routes)) {
        header("HTTP/1.1 404 Not Found");
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => false,
            'msj' => 'Ruta no encontrada',
            'ruta_solicitada' => $url
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    // Verificar si el método HTTP está permitido para esta ruta
    $allowedMethods = $routes[$url];
    if (!in_array($method, $allowedMethods)) {
        header("HTTP/1.1 405 Method Not Allowed");
        header('Content-Type: application/json; charset=utf-8');
        header('Allow: ' . implode(', ', $allowedMethods));
        echo json_encode([
            'ok' => false,
            'msj' => "Método $method no permitido para esta ruta",
            'metodos_permitidos' => $allowedMethods
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    // Enrutar a la acción correspondiente
    switch ($url) {
        case 'api/cargos/getAll':
            $controller->handleRequest('getAll');
            break;
        
        case 'api/cargos/getById':
            $controller->handleRequest('getById');
            break;

        case 'api/cargos/register':
            $controller->handleRequest('register');
            break;

        case 'api/cargos/update':
            $controller->handleRequest('update');
            break;

        case 'api/cargos/delete':
            $controller->handleRequest('delete');
            break;
    }
}
