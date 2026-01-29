<?php
/**
 * Modelo Dashboard
 * Manejo de operaciones para el dashboard utilizando procedimientos almacenados
 */
class Dashboard extends Conectar
{
    /**
     * Obtener KPIs de Ventas y Tickets
     */
    public function get_kpis_ventas_tickets(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_kpis_ventas_tickets(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'KPIs obtenidos correctamente',
                'data' => $data ?: [
                    'tickets_vendidos_hoy' => 0,
                    'ingresos_hoy' => 0,
                    'ingresos_mes' => 0,
                    'ticket_promedio' => 0
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error en get_kpis_ventas_tickets: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener KPIs de ventas',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener KPIs de Estado Operativo
     */
    public function get_kpis_estado_operativo(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_kpis_estado_operativo(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'KPIs obtenidos correctamente',
                'data' => $data ?: [
                    'tickets_pendientes_validacion' => 0,
                    'pagos_rechazados_hoy' => 0,
                    'tickets_por_expirar' => 0,
                    'personas_unicas_participantes' => 0
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error en get_kpis_estado_operativo: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener KPIs de estado operativo',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener KPIs de Rifas
     */
    public function get_kpis_rifas(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_kpis_rifas(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'KPIs obtenidos correctamente',
                'data' => $data ?: [
                    'rifas_activas' => 0,
                    'rifa_mas_vendida' => null,
                    'rifa_menor_avance' => null
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error en get_kpis_rifas: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener KPIs de rifas',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener ventas en el tiempo
     */
    public function get_ventas_tiempo(int $sede_id, int $dias = 30): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_ventas_tiempo(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->bindValue(2, $dias, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'Datos obtenidos correctamente',
                'data' => $data ?: []
            ];
        } catch (PDOException $e) {
            error_log("Error en get_ventas_tiempo: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener ventas en el tiempo',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener estado de tickets
     */
    public function get_estado_tickets(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_estado_tickets(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'Datos obtenidos correctamente',
                'data' => $data ?: []
            ];
        } catch (PDOException $e) {
            error_log("Error en get_estado_tickets: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener estado de tickets',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener avance de rifas
     */
    public function get_avance_rifas(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_avance_rifas(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'Datos obtenidos correctamente',
                'data' => $data ?: []
            ];
        } catch (PDOException $e) {
            error_log("Error en get_avance_rifas: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener avance de rifas',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener canales de venta
     */
    public function get_canales_venta(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_canales_venta(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'Datos obtenidos correctamente',
                'data' => $data ?: []
            ];
        } catch (PDOException $e) {
            error_log("Error en get_canales_venta: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener canales de venta',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener últimos movimientos
     */
    public function get_ultimos_movimientos(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_ultimos_movimientos(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'Datos obtenidos correctamente',
                'data' => $data ?: []
            ];
        } catch (PDOException $e) {
            error_log("Error en get_ultimos_movimientos: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener últimos movimientos',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener últimos ganadores
     */
    public function get_ultimos_ganadores(int $sede_id, int $limite = 10): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_ultimos_ganadores(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->bindValue(2, $limite, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'Datos obtenidos correctamente',
                'data' => $data ?: []
            ];
        } catch (PDOException $e) {
            error_log("Error en get_ultimos_ganadores: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener últimos ganadores',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener tickets aprobados
     */
    public function get_tickets_aprobados(int $sede_id, int $limite = 50): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL dashboard_tickets_aprobados(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->bindValue(2, $limite, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => 'Datos obtenidos correctamente',
                'data' => $data ?: []
            ];
        } catch (PDOException $e) {
            error_log("Error en get_tickets_aprobados: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener tickets aprobados',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener todos los datos del dashboard
     */
    public function get_dashboard_completo(int $sede_id, int $dias_ventas = 30): array
    {
        return [
            'kpis_ventas_tickets' => $this->get_kpis_ventas_tickets($sede_id),
            'kpis_estado_operativo' => $this->get_kpis_estado_operativo($sede_id),
            'kpis_rifas' => $this->get_kpis_rifas($sede_id),
            'ventas_tiempo' => $this->get_ventas_tiempo($sede_id, $dias_ventas),
            'estado_tickets' => $this->get_estado_tickets($sede_id),
            'avance_rifas' => $this->get_avance_rifas($sede_id),
            'canales_venta' => $this->get_canales_venta($sede_id),
            'ultimos_movimientos' => $this->get_ultimos_movimientos($sede_id),
            'ultimos_ganadores' => $this->get_ultimos_ganadores($sede_id, 10),
            'tickets_aprobados' => $this->get_tickets_aprobados($sede_id, 50)
        ];
    }
}
