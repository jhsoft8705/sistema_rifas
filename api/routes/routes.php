<?php
/**
 * Enrutador Principal
 * Determina qué módulo debe manejar cada petición
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../helpers/AuthMiddleware.php");
require_once(__DIR__ . "/routes_cargos.php");
require_once(__DIR__ . "/routes_auth.php");

function Routes(): void
{
    $basePath = Conectar::obtenerBaseUrl();
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Eliminar la base del proyecto si existe en la URL solicitada
    if (strpos($url, $basePath) === 0) {
        $url = substr($url, strlen($basePath));
    }
    
    $url = trim($url, '/');  
    $method = $_SERVER['REQUEST_METHOD']; 

    // ====================================
    // RUTAS PÚBLICAS (No requieren autenticación)
    // ====================================
    $rutasPublicas = [
        'api/auth/login',
        'api/auth/verificar'
    ];

    // Si NO es una ruta pública, verificar autenticación
    if (!in_array($url, $rutasPublicas)) {
        AuthMiddleware::verificarAutenticacion();
    }

    // ====================================
    // ENRUTAMIENTO POR MÓDULO
    // ====================================
    switch (true) {
        case strpos($url, 'api/auth') === 0:
            RoutesAuth($url, $method);
            break;
            
        case strpos($url, 'api/cargos') === 0:
            RoutesCargos($url, $method);
            break;
 
        default:
            header("HTTP/1.1 404 Not Found");
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok' => false,
                'msj' => 'Ruta no encontrada',
                'ruta_solicitada' => $url
            ], JSON_UNESCAPED_UNICODE);
            break;
    }
}
