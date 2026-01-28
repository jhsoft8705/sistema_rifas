<?php
/**
 * Enrutador Principal
 * Determina qué módulo debe manejar cada petición
 */
require_once(__DIR__ . "/../../config/conexion.php");
require_once(__DIR__ . "/../../helpers/AuthMiddleware.php");
require_once(__DIR__ . "/routes_auth.php");
require_once(__DIR__ . "/routes_cargos.php");
require_once(__DIR__ . "/routes_premios.php");
require_once(__DIR__ . "/routes_categorias_premios.php");
require_once(__DIR__ . "/routes_rifas.php");
require_once(__DIR__ . "/routes_tickets.php");
require_once(__DIR__ . "/routes_personas.php");
require_once(__DIR__ . "/routes_juegos.php");
require_once(__DIR__ . "/routes_contactos.php");
require_once(__DIR__ . "/routes_organizacion.php");
require_once(__DIR__ . "/routes_usuarios.php");
require_once(__DIR__ . "/routes_reportes.php");

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
        'api/auth/verificar',
        'api/rifas/publicas',
        'api/rifas/getById',
        'api/rifas/numeros/get',
        'api/rifas/numeros/disponibles',
        'api/rifas/numeros/reservar',
        'api/rifas/numeros/aleatorio',
        'api/tickets/create',
        'api/tickets/getByCodigo',
        'api/tickets/consultar',
        'api/tickets/uploadComprobante',
        'api/juegos/ganadoresPublicos',
        'api/contactos/register'
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

        case strpos($url, 'api/premios') === 0:
            RoutesPremios($url, $method);
            break;

        case strpos($url, 'api/categorias') === 0:
            RoutesCategoriasPremios($url, $method);
            break;

        case strpos($url, 'api/rifas') === 0:
            RoutesRifas($url, $method);
            break;

        case strpos($url, 'api/tickets') === 0:
            RoutesTickets($url, $method);
            break;

        case strpos($url, 'api/personas') === 0:
            RoutesPersonas($url, $method);
            break;

        case strpos($url, 'api/juegos') === 0:
            RoutesJuegos($url, $method);
            break;

        case strpos($url, 'api/contactos') === 0:
            RoutesContactos($url, $method);
            break;

        case strpos($url, 'api/organizacion') === 0:
            RoutesOrganizacion($url, $method);
            break;

        case strpos($url, 'api/usuarios') === 0:
            RoutesUsuarios($url, $method);
            break;

        case strpos($url, 'api/reporte') === 0:
            RoutesReportes($url, $method);
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
