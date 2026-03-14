<?php
/**
 * Rutas para el módulo de Perfil
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/PerfilController.php");

function RoutesPerfil(string $url, string $method): void
{
    $controller = new PerfilController();

    $routes = [
        'api/perfil/getPerfil' => ['GET'],
        'api/perfil/updateDatos' => ['PUT', 'POST'],
        'api/perfil/cambiarPassword' => ['PUT', 'POST']
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
        case 'api/perfil/getPerfil':
            $controller->handleRequest('getPerfil');
            break;
        case 'api/perfil/updateDatos':
            $controller->handleRequest('updateDatos');
            break;
        case 'api/perfil/cambiarPassword':
            $controller->handleRequest('cambiarPassword');
            break;
    }
}
