<?php
/**
 * Rutas para el módulo de Juegos
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/JuegoController.php");

function RoutesJuegos(string $url, string $method): void
{
    $controller = new JuegoController();

    $routes = [
        'api/juegos/getRifasParaJugar' => ['GET'],
        'api/juegos/getPremiosRifa' => ['GET'],
        'api/juegos/getParticipantes'  => ['GET'],
        'api/juegos/jugar' => ['POST'],
        'api/juegos/getInfoJuego'   => ['GET'],
        'api/juegos/registrarGanador' => ['POST'],
        'api/juegos/verificarRifaCompleta' => ['GET'],
        'api/juegos/getNumerosGanador' => ['GET'],
        'api/juegos/ganadoresPublicos' => ['GET']
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
        case 'api/juegos/getRifasParaJugar':
            $controller->handleRequest('getRifasParaJugar');
            break;
        case 'api/juegos/getPremiosRifa':
            $controller->handleRequest('getPremiosRifa');
            break;
        case 'api/juegos/getParticipantes':
            $controller->handleRequest('getParticipantes');
            break;
        case 'api/juegos/jugar':
            $controller->handleRequest('jugar');
            break;
        case 'api/juegos/getInfoJuego':
            $controller->handleRequest('getInfoJuego');
            break;
        case 'api/juegos/registrarGanador':
            $controller->handleRequest('registrarGanador');
            break;
        case 'api/juegos/verificarRifaCompleta':
            $controller->handleRequest('verificarRifaCompleta');
            break;
        case 'api/juegos/getNumerosGanador':
            $controller->handleRequest('getNumerosGanador');
            break;
        case 'api/juegos/ganadoresPublicos':
            $controller->handleRequest('ganadoresPublicos');
            break;
    }
}
