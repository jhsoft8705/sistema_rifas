<?php
require_once __DIR__ . "/../config/conexion.php";

// Detectar automáticamente la ruta base del proyecto
$base_project = basename(dirname(__DIR__));
$base_path_url = '/' . $base_project;

// Obtener la URL solicitada
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Eliminar la base del proyecto si existe en la URL solicitada
if (strpos($url, $base_path_url) === 0) {
    $url = substr($url, strlen($base_path_url));
}
$url = trim($url, '/');

// Definir la ruta base del sistema de archivos
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $base_project;

// Definir rutas disponibles
$routes = [
    '' => $base_path . '/index.php',
    'admin-login' => $base_path . '/views/login/index.php',
    'admin-dashboard' => $base_path . '/views/dashboard/index.php',
    'admin-premios' => $base_path . '/views/premios/index.php',
    'admin-rifas' => $base_path . '/views/rifas/index.php',
    'rifas-ventas' => $base_path . '/views/rifas/ventas/index.php',
    'admin-categorias' => $base_path . '/views/categorias/index.php',
    'admin-tickets' => $base_path . '/views/tickets/index.php',
    'admin-personas' => $base_path . '/views/personas/index.php',
    'admin-juegos' => $base_path . '/views/juegos/index.php',

    'cargos' => $base_path . '/views/cargos/index.php',
    'empleados' => $base_path . '/views/empleados/index.php',
    'marcaciones' => $base_path . '/views/marcaciones/index.php',
    'empleadosregistro' => $base_path . '/views/empleados/register/index.php',
    'terminos' => $base_path . '/views/web/terminos/index.php',
    'rifa-numeros' => $base_path . '/views/rifas/numeros/index.php',
   ];

// Hacer disponible la ruta base para las vistas
$GLOBALS['BASE_URL'] = $base_path_url;

// Manejar rutas con parámetros dinámicos
$urlParts = explode('/', $url);
$routeMatched = false;

// Verificar ruta de números de rifa con ID encryptado
if (count($urlParts) === 2 && $urlParts[0] === 'rifa-numeros') {
    $encryptedId = $urlParts[1];
    $file = $base_path . '/views/rifas/numeros/index.php';
    if (file_exists($file)) {
        $_GET['id'] = $encryptedId;
        include $file;
        $routeMatched = true;
    }
}

// Verificar si la ruta existe en el array estático
if (!$routeMatched && array_key_exists($url, $routes)) {
    $file = $routes[$url];
    if (file_exists($file)) {
        include $file;
        $routeMatched = true;
    }
}

// Si no se encontró ninguna ruta
if (!$routeMatched) {
    http_response_code(404);
    $error_file = $base_path . '/views/404.php';
    if (file_exists($error_file)) {
        include $error_file;
    } else {
        echo "<h1>404 - Página no encontrada</h1>
              <p>La página que buscas no existe.</p>
              <a href='{$base_path_url}/home'>Volver al inicio</a>";
    }
}


?>