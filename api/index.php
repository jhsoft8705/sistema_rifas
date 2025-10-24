<?php
/**
 * Punto de entrada principal de la API
 * Maneja CORS y enruta las peticiones
 */

// Headers CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Auth-Token");
header("Content-Type: application/json; charset=utf-8");

// Manejar preflight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir archivos necesarios
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../helpers/AuthMiddleware.php');
require_once(__DIR__ . '/routes/routes.php');

// Ejecutar enrutamiento
Routes();

// Para Linux usar .htaccess y para Windows usar web.config
?>
