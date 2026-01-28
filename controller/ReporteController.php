<?php
/**
 * Controlador de Reportes
 * Reporte de recaudación por rifa y ganadores
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Reporte.php');

class ReporteController
{
    private $reporte;

    public function __construct()
    {
        $this->reporte = new Reporte();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'getReporteRecaudacion':
                $this->get_reporte_recaudacion();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    /**
     * Devuelve recaudación de la rifa en el rango de fechas y listado de ganadores
     */
    private function get_reporte_recaudacion(): void
    {
        try {
            if (!isset($_GET['sede_id']) || !isset($_GET['rifa_id']) || !isset($_GET['fecha_desde']) || !isset($_GET['fecha_hasta'])) {
                http_response_code(400);
                echo json_encode([
                    'ok' => false,
                    'msj' => 'Parámetros obligatorios: sede_id, rifa_id, fecha_desde, fecha_hasta'
                ]);
                return;
            }

            $sede_id = (int) $_GET['sede_id'];
            $rifa_id = (int) $_GET['rifa_id'];
            $fecha_desde = trim($_GET['fecha_desde']);
            $fecha_hasta = trim($_GET['fecha_hasta']);

            if ($fecha_desde === '' || $fecha_hasta === '') {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Las fechas son obligatorias']);
                return;
            }

            $recaudacion = $this->reporte->get_recaudacion_rifa($sede_id, $rifa_id, $fecha_desde, $fecha_hasta);
            $ganadores = $this->reporte->get_ganadores_rifa($sede_id, $rifa_id);

            $resultado = [
                'ok' => $recaudacion['ok'],
                'msj' => $recaudacion['msj'],
                'recaudacion' => $recaudacion['data'],
                'ganadores' => $ganadores['ok'] ? $ganadores['data'] : []
            ];

            http_response_code(200);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en get_reporte_recaudacion: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al generar el reporte']);
        }
    }
}
