<?php
/**
 * Rutas para el módulo de Roles
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/RolController.php");

function RoutesRoles(string $url, string $method): void
{
    $controller = new RolController();

    $routes = [
        'api/roles/getAll' => ['GET'],
        'api/roles/getById' => ['GET'],
        'api/roles/register' => ['POST'],
        'api/roles/update' => ['PUT', 'POST'],
        'api/roles/getPermisos' => ['GET'],
        'api/roles/asignarPermisos' => ['PUT', 'POST']
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
        case 'api/roles/getAll':
            $controller->handleRequest('getAll');
            break;
        case 'api/roles/getById':
            $controller->handleRequest('getById');
            break;
        case 'api/roles/register':
            $controller->handleRequest('register');
            break;
        case 'api/roles/update':
            $controller->handleRequest('update');
            break;
        case 'api/roles/getPermisos':
            $controller->handleRequest('getPermisos');
            break;
        case 'api/roles/asignarPermisos':
            $controller->handleRequest('asignarPermisos');
            break;
    }
}
