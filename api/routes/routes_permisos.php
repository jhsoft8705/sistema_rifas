<?php
/**
 * Rutas para el módulo de Permisos
 */
require_once(__DIR__ . "/../../config/Conexion.php");
require_once(__DIR__ . "/../../controller/PermisoController.php");

function RoutesPermisos(string $url, string $method): void
{
    $controller = new PermisoController();

    $routes = [
        'api/permisos/getAll' => ['GET'],
        'api/permisos/getById' => ['GET'],
        'api/permisos/getPermisosUsuario' => ['GET'],
        'api/permisos/register' => ['POST'],
        'api/permisos/update' => ['PUT', 'POST'],
        'api/permisos/verificarPermiso' => ['GET']
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
        case 'api/permisos/getAll':
            $controller->handleRequest('getAll');
            break;
        case 'api/permisos/getById':
            $controller->handleRequest('getById');
            break;
        case 'api/permisos/getPermisosUsuario':
            $controller->handleRequest('getPermisosUsuario');
            break;
        case 'api/permisos/register':
            $controller->handleRequest('register');
            break;
        case 'api/permisos/update':
            $controller->handleRequest('update');
            break;
        case 'api/permisos/verificarPermiso':
            $controller->handleRequest('verificarPermiso');
            break;
    }
}
