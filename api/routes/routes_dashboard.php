<?php
/**
 * Rutas para el módulo de Dashboard
 */
require_once(__DIR__ . "/../../config/Conexion.php");
require_once(__DIR__ . "/../../controller/DashboardController.php");

function RoutesDashboard(string $url, string $method): void
{
    $controller = new DashboardController();

    $routes = [
        'api/dashboard/getDashboardCompleto' => ['GET'],
        'api/dashboard/getKPIsCompletos' => ['GET'],
        'api/dashboard/getGraficosVentasEstado' => ['GET'],
        'api/dashboard/getKPIsVentasTickets' => ['GET'],
        'api/dashboard/getKPIsEstadoOperativo' => ['GET'],
        'api/dashboard/getKPIsRifas' => ['GET'],
        'api/dashboard/getVentasTiempo' => ['GET'],
        'api/dashboard/getEstadoTickets' => ['GET'],
        'api/dashboard/getAvanceRifas' => ['GET'],
        'api/dashboard/getCanalesVenta' => ['GET'],
        'api/dashboard/getUltimosMovimientos' => ['GET'],
        'api/dashboard/getUltimosGanadores' => ['GET'],
        'api/dashboard/getTicketsAprobados' => ['GET']
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
        case 'api/dashboard/getDashboardCompleto':
            $controller->handleRequest('getDashboardCompleto');
            break;
        case 'api/dashboard/getKPIsCompletos':
            $controller->handleRequest('getKPIsCompletos');
            break;
        case 'api/dashboard/getGraficosVentasEstado':
            $controller->handleRequest('getGraficosVentasEstado');
            break;
        case 'api/dashboard/getKPIsVentasTickets':
            $controller->handleRequest('getKPIsVentasTickets');
            break;
        case 'api/dashboard/getKPIsEstadoOperativo':
            $controller->handleRequest('getKPIsEstadoOperativo');
            break;
        case 'api/dashboard/getKPIsRifas':
            $controller->handleRequest('getKPIsRifas');
            break;
        case 'api/dashboard/getVentasTiempo':
            $controller->handleRequest('getVentasTiempo');
            break;
        case 'api/dashboard/getEstadoTickets':
            $controller->handleRequest('getEstadoTickets');
            break;
        case 'api/dashboard/getAvanceRifas':
            $controller->handleRequest('getAvanceRifas');
            break;
        case 'api/dashboard/getCanalesVenta':
            $controller->handleRequest('getCanalesVenta');
            break;
        case 'api/dashboard/getUltimosMovimientos':
            $controller->handleRequest('getUltimosMovimientos');
            break;
        case 'api/dashboard/getUltimosGanadores':
            $controller->handleRequest('getUltimosGanadores');
            break;
        case 'api/dashboard/getTicketsAprobados':
            $controller->handleRequest('getTicketsAprobados');
            break;
    }
}
