<?php
/**
 * Rutas para el módulo de Tickets y Comprobantes
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/TicketController.php");

function RoutesTickets(string $url, string $method): void
{
    $controller = new TicketController();

    $routes = [
        'api/tickets/create'              => ['POST'],
        'api/tickets/getAll'              => ['GET'],
        'api/tickets/getByCodigo'         => ['GET'],
        'api/tickets/uploadComprobante'   => ['POST'],
        'api/tickets/getComprobantes'     => ['GET'],
        'api/tickets/validarComprobante'  => ['POST'],
        'api/tickets/listVentas'          => ['GET'],
        'api/tickets/getComprobante'      => ['GET']
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
        case 'api/tickets/create':
            $controller->handleRequest('create');
            break;
        case 'api/tickets/getAll':
            $controller->handleRequest('getAll');
            break;
        case 'api/tickets/getByCodigo':
            $controller->handleRequest('getByCodigo');
            break;
        case 'api/tickets/uploadComprobante':
            $controller->handleRequest('uploadComprobante');
            break;
        case 'api/tickets/getComprobantes':
            $controller->handleRequest('getComprobantes');
            break;
        case 'api/tickets/validarComprobante':
            $controller->handleRequest('validarComprobante');
            break;
        case 'api/tickets/listVentas':
            $controller->handleRequest('listVentas');
            break;
        case 'api/tickets/getComprobante':
            $controller->handleRequest('getComprobante');
            break;
    }
}

