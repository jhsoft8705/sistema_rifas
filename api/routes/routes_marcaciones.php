<?php
/**
 * Rutas API para Gestión de Marcaciones Biométricas
 * 
 * Endpoints disponibles:
 * - POST /api/marcaciones/procesar-logs
 * - GET /api/marcaciones/obtener
 * - GET /api/marcaciones/resumen
 * - GET /api/marcaciones/logs-pendientes
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/Conexion.php';

class MarcacionesAPI {
    
    private $conexion;
    
    public function __construct() {
        $this->conexion = Conexion::getConexion();
    }
    
    /**
     * Procesa logs pendientes del biométrico
     * 
     * POST /api/marcaciones/procesar-logs
     * Body: {
     *   "sede_id": 1,
     *   "limite": 100
     * }
     */
    public function procesarLogs() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $sedeId = $input['sede_id'] ?? null;
            $limite = $input['limite'] ?? 100;
            
            $sql = "EXEC sp_procesar_logs_biometrico @sede_id = ?, @limite = ?";
            $params = [$sedeId, $limite];
            
            $stmt = sqlsrv_prepare($this->conexion, $sql, $params);
            
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . print_r(sqlsrv_errors(), true));
            }
            
            if (!sqlsrv_execute($stmt)) {
                throw new Exception("Error al ejecutar consulta: " . print_r(sqlsrv_errors(), true));
            }
            
            $resultado = [];
            if (sqlsrv_fetch($stmt)) {
                $resultado = [
                    'logs_procesados' => sqlsrv_get_field($stmt, 0),
                    'empleados_afectados' => sqlsrv_get_field($stmt, 1)
                ];
            }
            
            sqlsrv_free_stmt($stmt);
            
            $this->sendResponse(200, [
                'success' => true,
                'mensaje' => 'Logs procesados exitosamente',
                'data' => $resultado
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'success' => false,
                'mensaje' => 'Error al procesar logs',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtiene marcaciones filtradas
     * 
     * GET /api/marcaciones/obtener?sede_id=1&fecha_inicio=2025-01-01&fecha_fin=2025-01-31&empleado_id=1
     */
    public function obtenerMarcaciones() {
        try {
            $sedeId = $_GET['sede_id'] ?? null;
            $fechaInicio = $_GET['fecha_inicio'] ?? null;
            $fechaFin = $_GET['fecha_fin'] ?? null;
            $empleadoId = $_GET['empleado_id'] ?? null;
            
            if (!$sedeId) {
                throw new Exception("El parámetro sede_id es obligatorio");
            }
            
            $sql = "EXEC sp_obtener_marcaciones @sede_id = ?, @fecha_inicio = ?, @fecha_fin = ?, @empleado_id = ?";
            $params = [$sedeId, $fechaInicio, $fechaFin, $empleadoId];
            
            $stmt = sqlsrv_query($this->conexion, $sql, $params);
            
            if (!$stmt) {
                throw new Exception("Error en la consulta: " . print_r(sqlsrv_errors(), true));
            }
            
            $marcaciones = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // Convertir DateTime a string
                foreach ($row as $key => $value) {
                    if ($value instanceof DateTime) {
                        $row[$key] = $value->format('Y-m-d H:i:s');
                    }
                }
                $marcaciones[] = $row;
            }
            
            sqlsrv_free_stmt($stmt);
            
            $this->sendResponse(200, [
                'success' => true,
                'mensaje' => 'Marcaciones obtenidas correctamente',
                'data' => $marcaciones,
                'total' => count($marcaciones)
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'success' => false,
                'mensaje' => 'Error al obtener marcaciones',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtiene resumen de asistencia
     * 
     * GET /api/marcaciones/resumen?sede_id=1&fecha_inicio=2025-01-01&fecha_fin=2025-01-31
     */
    public function obtenerResumen() {
        try {
            $sedeId = $_GET['sede_id'] ?? null;
            $fechaInicio = $_GET['fecha_inicio'] ?? null;
            $fechaFin = $_GET['fecha_fin'] ?? null;
            $empleadoId = $_GET['empleado_id'] ?? null;
            
            if (!$sedeId || !$fechaInicio || !$fechaFin) {
                throw new Exception("Los parámetros sede_id, fecha_inicio y fecha_fin son obligatorios");
            }
            
            $sql = "EXEC sp_resumen_asistencia_empleado @sede_id = ?, @fecha_inicio = ?, @fecha_fin = ?, @empleado_id = ?";
            $params = [$sedeId, $fechaInicio, $fechaFin, $empleadoId];
            
            $stmt = sqlsrv_query($this->conexion, $sql, $params);
            
            if (!$stmt) {
                throw new Exception("Error en la consulta: " . print_r(sqlsrv_errors(), true));
            }
            
            $resumen = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $resumen[] = $row;
            }
            
            sqlsrv_free_stmt($stmt);
            
            $this->sendResponse(200, [
                'success' => true,
                'mensaje' => 'Resumen obtenido correctamente',
                'data' => $resumen,
                'total' => count($resumen)
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'success' => false,
                'mensaje' => 'Error al obtener resumen',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtiene logs pendientes de procesar
     * 
     * GET /api/marcaciones/logs-pendientes?sede_id=1&limite=100
     */
    public function obtenerLogsPendientes() {
        try {
            $sedeId = $_GET['sede_id'] ?? null;
            $limite = $_GET['limite'] ?? 100;
            
            if (!$sedeId) {
                throw new Exception("El parámetro sede_id es obligatorio");
            }
            
            $sql = "EXEC sp_obtener_logs_pendientes @sede_id = ?, @limite = ?";
            $params = [$sedeId, $limite];
            
            $stmt = sqlsrv_query($this->conexion, $sql, $params);
            
            if (!$stmt) {
                throw new Exception("Error en la consulta: " . print_r(sqlsrv_errors(), true));
            }
            
            $logs = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // Convertir DateTime a string
                foreach ($row as $key => $value) {
                    if ($value instanceof DateTime) {
                        $row[$key] = $value->format('Y-m-d H:i:s');
                    }
                }
                $logs[] = $row;
            }
            
            sqlsrv_free_stmt($stmt);
            
            $this->sendResponse(200, [
                'success' => true,
                'mensaje' => 'Logs pendientes obtenidos correctamente',
                'data' => $logs,
                'total' => count($logs)
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'success' => false,
                'mensaje' => 'Error al obtener logs pendientes',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtiene marcaciones pendientes de reconciliación
     * 
     * GET /api/marcaciones/pendientes-reconciliacion?sede_id=1
     */
    public function obtenerPendientesReconciliacion() {
        try {
            $sedeId = $_GET['sede_id'] ?? null;
            
            if (!$sedeId) {
                throw new Exception("El parámetro sede_id es obligatorio");
            }
            
            $sql = "EXEC sp_marcaciones_pendientes_reconciliacion @sede_id = ?";
            $params = [$sedeId];
            
            $stmt = sqlsrv_query($this->conexion, $sql, $params);
            
            if (!$stmt) {
                throw new Exception("Error en la consulta: " . print_r(sqlsrv_errors(), true));
            }
            
            $marcaciones = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // Convertir DateTime a string
                foreach ($row as $key => $value) {
                    if ($value instanceof DateTime) {
                        $row[$key] = $value->format('Y-m-d H:i:s');
                    }
                }
                $marcaciones[] = $row;
            }
            
            sqlsrv_free_stmt($stmt);
            
            $this->sendResponse(200, [
                'success' => true,
                'mensaje' => 'Marcaciones pendientes obtenidas correctamente',
                'data' => $marcaciones,
                'total' => count($marcaciones)
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'success' => false,
                'mensaje' => 'Error al obtener marcaciones pendientes',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Envía respuesta JSON
     */
    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
}

// =====================================================
// ENRUTAMIENTO
// =====================================================

$api = new MarcacionesAPI();
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Extraer la acción de la URI
$uriParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
$action = end($uriParts);

try {
    switch ($action) {
        case 'procesar-logs':
            if ($requestMethod === 'POST') {
                $api->procesarLogs();
            } else {
                throw new Exception("Método no permitido");
            }
            break;
            
        case 'obtener':
            if ($requestMethod === 'GET') {
                $api->obtenerMarcaciones();
            } else {
                throw new Exception("Método no permitido");
            }
            break;
            
        case 'resumen':
            if ($requestMethod === 'GET') {
                $api->obtenerResumen();
            } else {
                throw new Exception("Método no permitido");
            }
            break;
            
        case 'logs-pendientes':
            if ($requestMethod === 'GET') {
                $api->obtenerLogsPendientes();
            } else {
                throw new Exception("Método no permitido");
            }
            break;
            
        case 'pendientes-reconciliacion':
            if ($requestMethod === 'GET') {
                $api->obtenerPendientesReconciliacion();
            } else {
                throw new Exception("Método no permitido");
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'mensaje' => 'Endpoint no encontrado',
                'endpoints_disponibles' => [
                    'POST /api/marcaciones/procesar-logs',
                    'GET /api/marcaciones/obtener',
                    'GET /api/marcaciones/resumen',
                    'GET /api/marcaciones/logs-pendientes',
                    'GET /api/marcaciones/pendientes-reconciliacion'
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}


