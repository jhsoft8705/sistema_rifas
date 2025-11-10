<?php
/**
 * Rutas para el módulo de Categorías de Premios
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/CategoriaPremioController.php");

function RoutesCategoriasPremios(string $url, string $method): void
{
    $controller = new CategoriaPremioController();

    $routes = [
        'api/categorias/getAll'   => ['GET'],
        'api/categorias/getById'  => ['GET'],
        'api/categorias/register' => ['POST'],
        'api/categorias/update'   => ['PUT', 'POST'],
        'api/categorias/delete'   => ['DELETE', 'POST']
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
        case 'api/categorias/getAll':
            $controller->handleRequest('getAll');
            break;
        case 'api/categorias/getById':
            $controller->handleRequest('getById');
            break;
        case 'api/categorias/register':
            $controller->handleRequest('register');
            break;
        case 'api/categorias/update':
            $controller->handleRequest('update');
            break;
        case 'api/categorias/delete':
            $controller->handleRequest('delete');
            break;
    }
}


