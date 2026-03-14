<?php
/**
 * Prueba de conexión a la base de datos
 * 
 * Acceso: /sistema_rifas/config/test_conexion.php (dev)
 *         /config/test_conexion.php (prod subdominio)
 * 
 * IMPORTANTE: Eliminar o restringir acceso en producción por seguridad.
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/conexion.php';

class TestConectar extends Conectar
{
    public function probarConexion()
    {
        return $this->Conexion();
    }
}

$resultado = [
    'ok' => false,
    'mensaje' => '',
    'detalle' => [],
    'sp_login' => null,
    'debug' => null
];

// ?debug=1 para ver info de enrutamiento (útil en producción)
if (!empty($_GET['debug'])) {
    $basePath = Conectar::obtenerBaseUrl();
    $resultado['debug'] = [
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '-',
        'PATH_INFO' => $_SERVER['PATH_INFO'] ?? '-',
        'basePath' => $basePath,
        'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? '-',
    ];
}

try {
    $test = new TestConectar();
    $pdo = $test->probarConexion();
    
    $resultado['ok'] = true;
    $resultado['mensaje'] = 'Conexión exitosa';
    $resultado['detalle'] = [
        'driver' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
        'server' => @$pdo->getAttribute(PDO::ATTR_SERVER_VERSION) ?: 'OK',
    ];

    // Probar sp_Login si existe
    $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Name = 'sp_Login'");
    if ($stmt && $stmt->rowCount() > 0) {
        $resultado['sp_login'] = 'sp_Login existe en la BD';
    } else {
        $resultado['sp_login'] = 'sp_Login NO encontrado - ejecutar docs/sql/auth.sql';
    }

} catch (PDOException $e) {
    $resultado['mensaje'] = 'Error de conexión PDO';
    $resultado['detalle'] = [
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
    ];
} catch (Throwable $e) {
    $resultado['mensaje'] = 'Error: ' . $e->getMessage();
    $resultado['detalle'] = [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ];
}

echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
