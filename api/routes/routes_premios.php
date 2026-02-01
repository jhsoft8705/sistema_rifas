<?php
/**
 * Rutas para el módulo de Premios
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/PremioController.php");

function RoutesPremios(string $url, string $method): void
{
    $controller = new PremioController();

    $routes = [
        'api/premios/getAll'   => ['GET'],
        'api/premios/getById'  => ['GET'],
        'api/premios/destacados' => ['GET'], // Ruta pública para premios destacados
        'api/premios/register' => ['POST'],
        'api/premios/update'   => ['PUT', 'POST'],
        'api/premios/delete'   => ['DELETE', 'POST']
    ];

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

    $allowedMethods = $routes[$url];
    if (!in_array($method, $allowedMethods, true)) {
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

    switch ($url) {
        case 'api/premios/getAll':
            $controller->handleRequest('getAll');
            break;
        case 'api/premios/getById':
            $controller->handleRequest('getById');
            break;
        case 'api/premios/destacados':
            $controller->handleRequest('destacados');
            break;
        case 'api/premios/register':
            $controller->handleRequest('register');
            break;
        case 'api/premios/update':
            $controller->handleRequest('update');
            break;
        case 'api/premios/delete':
            $controller->handleRequest('delete');
            break;
    }
}


