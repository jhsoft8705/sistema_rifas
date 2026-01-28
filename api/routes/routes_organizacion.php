<?php
/**
 * Rutas para el módulo de Organización (Sede)
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/OrganizacionController.php");

function RoutesOrganizacion(string $url, string $method): void
{
    $controller = new OrganizacionController();

    $routes = [
        'api/organizacion/getById' => ['GET'],
        'api/organizacion/update'   => ['PUT', 'POST']
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
        case 'api/organizacion/getById':
            $controller->handleRequest('getById');
            break;
        case 'api/organizacion/update':
            $controller->handleRequest('update');
            break;
    }
}
