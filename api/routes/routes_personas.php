<?php
/**
 * Rutas para el módulo de Personas
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/PersonaController.php");

function RoutesPersonas(string $url, string $method): void
{
    $controller = new PersonaController();

    $routes = [
        'api/personas/getAll'   => ['GET'],
        'api/personas/getById'  => ['GET'],
        'api/personas/register' => ['POST'],
        'api/personas/update'   => ['PUT', 'POST'],
        'api/personas/delete'   => ['DELETE', 'POST']
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
        case 'api/personas/getAll':
            $controller->handleRequest('getAll');
            break;
        case 'api/personas/getById':
            $controller->handleRequest('getById');
            break;
        case 'api/personas/register':
            $controller->handleRequest('register');
            break;
        case 'api/personas/update':
            $controller->handleRequest('update');
            break;
        case 'api/personas/delete':
            $controller->handleRequest('delete');
            break;
    }
}
