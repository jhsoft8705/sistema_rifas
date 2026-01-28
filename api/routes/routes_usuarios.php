<?php
/**
 * Rutas para el módulo de Usuarios
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/UsuarioController.php");

function RoutesUsuarios(string $url, string $method): void
{
    $controller = new UsuarioController();

    $routes = [
        'api/usuarios/getAll'    => ['GET'],
        'api/usuarios/getById'   => ['GET'],
        'api/usuarios/getRoles'  => ['GET'],
        'api/usuarios/register'  => ['POST'],
        'api/usuarios/update'    => ['PUT', 'POST'],
        'api/usuarios/disable'   => ['DELETE', 'POST']
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
        case 'api/usuarios/getAll':
            $controller->handleRequest('getAll');
            break;
        case 'api/usuarios/getById':
            $controller->handleRequest('getById');
            break;
        case 'api/usuarios/getRoles':
            $controller->handleRequest('getRoles');
            break;
        case 'api/usuarios/register':
            $controller->handleRequest('register');
            break;
        case 'api/usuarios/update':
            $controller->handleRequest('update');
            break;
        case 'api/usuarios/disable':
            $controller->handleRequest('disable');
            break;
    }
}
