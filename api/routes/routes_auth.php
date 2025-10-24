<?php
/**
 * Rutas para el módulo de Autenticación
 * Maneja el enrutamiento de peticiones HTTP para autenticación de usuarios
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../controller/AuthController.php");

/**
 * Función principal de enrutamiento para autenticación
 * @param string $url URL de la petición
 * @param string $method Método HTTP (GET, POST, PUT, DELETE)
 */
function RoutesAuth($url, $method): void
{
    // Inicializar controlador
    $controller = new AuthController();

    // Definir rutas disponibles y sus métodos HTTP permitidos
    $routes = [
        'api/auth/login' => ['POST'],        // Login de usuario
        'api/auth/logout' => ['POST'],       // Logout de usuario
        'api/auth/verificar' => ['POST']     // Verificar sesión activa
    ];

    // Verificar si la ruta existe
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

    // Verificar si el método HTTP está permitido para esta ruta
    $allowedMethods = $routes[$url];
    if (!in_array($method, $allowedMethods)) {
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

    // Enrutar a la acción correspondiente
    switch ($url) {
        case 'api/auth/login':
            $controller->handleRequest('login');
            break;

        case 'api/auth/logout':
            $controller->handleRequest('logout');
            break;

        case 'api/auth/verificar':
            $controller->handleRequest('verificar');
            break;
    }
}
