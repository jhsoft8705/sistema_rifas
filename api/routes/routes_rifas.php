<?php
/**
 * Rutas para el módulo de Rifas
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/RifaController.php");

function RoutesRifas(string $url, string $method): void
{
    $controller = new RifaController();

    $routes = [
        'api/rifas/getAll'            => ['GET'],
        'api/rifas/getById'           => ['GET'],
        'api/rifas/register'          => ['POST'],
        'api/rifas/update'            => ['PUT', 'POST'],
        'api/rifas/delete'            => ['DELETE', 'POST'],
        'api/rifas/premios/get'       => ['GET'],
        'api/rifas/premios/register'  => ['POST'],
        'api/rifas/premios/update'    => ['PUT', 'POST'],
        'api/rifas/premios/delete'    => ['DELETE', 'POST'],
        'api/rifas/numeros/get'       => ['GET'],
        'api/rifas/numeros/update'    => ['PUT', 'POST']
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
        case 'api/rifas/getAll':
            $controller->handleRequest('getAll');
            break;
        case 'api/rifas/getById':
            $controller->handleRequest('getById');
            break;
        case 'api/rifas/register':
            $controller->handleRequest('register');
            break;
        case 'api/rifas/update':
            $controller->handleRequest('update');
            break;
        case 'api/rifas/delete':
            $controller->handleRequest('delete');
            break;
        case 'api/rifas/premios/get':
            $controller->handleRequest('getPremios');
            break;
        case 'api/rifas/premios/register':
            $controller->handleRequest('addPremio');
            break;
        case 'api/rifas/premios/update':
            $controller->handleRequest('updatePremio');
            break;
        case 'api/rifas/premios/delete':
            $controller->handleRequest('deletePremio');
            break;
        case 'api/rifas/numeros/get':
            $controller->handleRequest('getNumeros');
            break;
        case 'api/rifas/numeros/update':
            $controller->handleRequest('updateNumero');
            break;
    }
}


