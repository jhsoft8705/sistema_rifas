<?php
/**
 * Controlador de Dashboard
 * Manejo de operaciones para el dashboard
 */
require_once(__DIR__ . '/../config/Conexion.php');
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Dashboard.php');
require_once(__DIR__ . '/../helpers/AuthMiddleware.php');

class DashboardController
{
    private $dashboard;

    public function __construct()
    {
        $this->dashboard = new Dashboard();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Verificar autenticación
        $authData = AuthMiddleware::verificarAutenticacion(true);
        if (!$authData['ok']) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'msj' => 'Acceso denegado: ' . $authData['msj']]);
            return;
        }

        $usuario = $authData['data'];
        $sede_id = (int) $usuario['sede_id'];

        switch ($action) {
            case 'getDashboardCompleto':
                $this->get_dashboard_completo($sede_id);
                break;
            case 'getKPIsCompletos':
                $this->get_kpis_completos($sede_id);
                break;
            case 'getGraficosVentasEstado':
                $this->get_graficos_ventas_estado($sede_id);
                break;
            case 'getKPIsVentasTickets':
                $this->get_kpis_ventas_tickets($sede_id);
                break;
            case 'getKPIsEstadoOperativo':
                $this->get_kpis_estado_operativo($sede_id);
                break;
            case 'getKPIsRifas':
                $this->get_kpis_rifas($sede_id);
                break;
            case 'getVentasTiempo':
                $this->get_ventas_tiempo($sede_id);
                break;
            case 'getEstadoTickets':
                $this->get_estado_tickets($sede_id);
                break;
            case 'getAvanceRifas':
                $this->get_avance_rifas($sede_id);
                break;
            case 'getCanalesVenta':
                $this->get_canales_venta($sede_id);
                break;
            case 'getUltimosMovimientos':
                $this->get_ultimos_movimientos($sede_id);
                break;
            case 'getUltimosGanadores':
                $this->get_ultimos_ganadores($sede_id);
                break;
            case 'getTicketsAprobados':
                $this->get_tickets_aprobados($sede_id);
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    /**
     * Obtener dashboard completo
     */
    private function get_dashboard_completo(int $sede_id): void
    {
        try {
            $dias = isset($_GET['dias']) ? (int) $_GET['dias'] : 30;
            $resultado = $this->dashboard->get_dashboard_completo($sede_id, $dias);

            http_response_code(200);
            echo json_encode([
                'ok' => true,
                'msj' => 'Dashboard obtenido correctamente',
                'data' => $resultado
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_dashboard_completo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener el dashboard']);
        }
    }

    /**
     * Obtener KPIs completos (Ventas+Tickets + Estado Operativo + Rifas)
     */
    private function get_kpis_completos(int $sede_id): void
    {
        try {
            $resultado = $this->dashboard->get_kpis_completos($sede_id);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_kpis_completos: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener KPIs']);
        }
    }

    /**
     * Obtener gráficos Ventas en Tiempo + Estado de Tickets
     */
    private function get_graficos_ventas_estado(int $sede_id): void
    {
        try {
            $dias = isset($_GET['dias']) ? (int) $_GET['dias'] : 30;
            $resultado = $this->dashboard->get_graficos_ventas_estado($sede_id, $dias);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_graficos_ventas_estado: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener gráficos']);
        }
    }

    /**
     * Obtener KPIs de Ventas y Tickets
     */
    private function get_kpis_ventas_tickets(int $sede_id): void
    {
        try {
            $resultado = $this->dashboard->get_kpis_ventas_tickets($sede_id);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_kpis_ventas_tickets: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener KPIs']);
        }
    }

    /**
     * Obtener KPIs de Estado Operativo
     */
    private function get_kpis_estado_operativo(int $sede_id): void
    {
        try {
            $resultado = $this->dashboard->get_kpis_estado_operativo($sede_id);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_kpis_estado_operativo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener KPIs']);
        }
    }

    /**
     * Obtener KPIs de Rifas
     */
    private function get_kpis_rifas(int $sede_id): void
    {
        try {
            $resultado = $this->dashboard->get_kpis_rifas($sede_id);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_kpis_rifas: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener KPIs']);
        }
    }

    /**
     * Obtener ventas en el tiempo
     */
    private function get_ventas_tiempo(int $sede_id): void
    {
        try {
            $dias = isset($_GET['dias']) ? (int) $_GET['dias'] : 30;
            $resultado = $this->dashboard->get_ventas_tiempo($sede_id, $dias);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_ventas_tiempo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener ventas']);
        }
    }

    /**
     * Obtener estado de tickets
     */
    private function get_estado_tickets(int $sede_id): void
    {
        try {
            $resultado = $this->dashboard->get_estado_tickets($sede_id);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_estado_tickets: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener estado']);
        }
    }

    /**
     * Obtener avance de rifas
     */
    private function get_avance_rifas(int $sede_id): void
    {
        try {
            $resultado = $this->dashboard->get_avance_rifas($sede_id);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_avance_rifas: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener avance']);
        }
    }

    /**
     * Obtener canales de venta
     */
    private function get_canales_venta(int $sede_id): void
    {
        try {
            $resultado = $this->dashboard->get_canales_venta($sede_id);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_canales_venta: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener canales']);
        }
    }

    /**
     * Obtener últimos movimientos
     */
    private function get_ultimos_movimientos(int $sede_id): void
    {
        try {
            $resultado = $this->dashboard->get_ultimos_movimientos($sede_id);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_ultimos_movimientos: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener movimientos']);
        }
    }

    /**
     * Obtener últimos ganadores
     */
    private function get_ultimos_ganadores(int $sede_id): void
    {
        try {
            $limite = isset($_GET['limite']) ? (int) $_GET['limite'] : 10;
            $resultado = $this->dashboard->get_ultimos_ganadores($sede_id, $limite);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_ultimos_ganadores: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener ganadores']);
        }
    }

    /**
     * Obtener tickets aprobados
     */
    private function get_tickets_aprobados(int $sede_id): void
    {
        try {
            $limite = isset($_GET['limite']) ? (int) $_GET['limite'] : 50;
            $resultado = $this->dashboard->get_tickets_aprobados($sede_id, $limite);
            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en get_tickets_aprobados: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener tickets']);
        }
    }
}
