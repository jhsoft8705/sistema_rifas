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
    '' => $base_path . '/views/web/index.php',
    'dashboard' => $base_path . '/views/dashboard/index.php',
    'cargos' => $base_path . '/views/cargos/index.php',
    'empleados' => $base_path . '/views/empleados/index.php',
    'empleadosregistro' => $base_path . '/views/empleados/register/index.php',
     
   ];

// Hacer disponible la ruta base para las vistas
$GLOBALS['BASE_URL'] = $base_path_url;

// Verificar si la ruta existe
if (array_key_exists($url, $routes)) {
    $file = $routes[$url];

    // Verificar si el archivo existe
    if (file_exists($file)) {
        include $file;
    } else {
        // Archivo no encontrado
        http_response_code(404);
        echo "Archivo no encontrado: " . htmlspecialchars($file);
    }
} else {
    // Ruta no encontrada
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