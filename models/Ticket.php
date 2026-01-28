<?php
/**
 * Modelo Ticket
 * Manejo de operaciones CRUD para tickets y comprobantes utilizando procedimientos almacenados
 */
class Ticket extends Conectar
{
    /**
     * Crear ticket (compra de usuario final)
     */
    public function crear_ticket(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL register_ticket(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @ticket_id, @codigo_ticket, @mensaje)";
            $query = $conectar->prepare($sql);
            
            $query->bindValue(1, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'rifa_id'), PDO::PARAM_INT);
            $query->bindValue(3, $this->getValue($data, 'nombres'), PDO::PARAM_STR);
            $query->bindValue(4, $this->getValue($data, 'apellidos'), PDO::PARAM_STR);
            $query->bindValue(5, $this->getValue($data, 'tipo_documento'), PDO::PARAM_STR);
            $query->bindValue(6, $this->getValue($data, 'numero_documento'), PDO::PARAM_STR);
            $query->bindValue(7, $this->getValue($data, 'email'), PDO::PARAM_STR);
            $query->bindValue(8, $this->getValue($data, 'telefono'), PDO::PARAM_STR);
            $this->bindNullable($query, 9, $this->getValue($data, 'direccion'), PDO::PARAM_STR);
            $this->bindNullable($query, 10, $this->getValue($data, 'ciudad'), PDO::PARAM_STR);
            $this->bindNullable($query, 11, $this->getValue($data, 'pais'), PDO::PARAM_STR);
            $query->bindValue(12, $this->getValue($data, 'precio_pagado'), PDO::PARAM_STR);
            $query->bindValue(13, $this->getValue($data, 'cantidad_tickets', 1), PDO::PARAM_INT);
            $this->bindNullable($query, 14, isset($data['numeros_seleccionados']) ? json_encode($data['numeros_seleccionados']) : null, PDO::PARAM_STR);
            $this->bindNullable($query, 15, $this->getValue($data, 'ip_compra'), PDO::PARAM_STR);
            $this->bindNullable($query, 16, $this->getValue($data, 'canal_venta', 'WEB'), PDO::PARAM_STR);
            $this->bindNullable($query, 17, $this->getValue($data, 'estado_inicial'), PDO::PARAM_STR);
            
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @ticket_id AS ticket_id, @codigo_ticket AS codigo_ticket, @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false || stripos($mensaje, 'creado') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje,
                'ticket_id' => $result['ticket_id'] ?? null,
                'codigo_ticket' => $result['codigo_ticket'] ?? null
            ];
        } catch (PDOException $e) {
            error_log("Error en crear_ticket: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al crear el ticket',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Listar tickets por sede y estado
     */
    public function listar_tickets(int $sede_id, ?int $rifa_id = null, ?string $estado = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_tickets(?, ?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $this->bindNullable($query, 2, $rifa_id, PDO::PARAM_INT);
            if ($estado === null || $estado === '') {
                $query->bindValue(3, null, PDO::PARAM_NULL);
            } else {
                $query->bindValue(3, $estado, PDO::PARAM_STR);
            }
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Tickets obtenidos correctamente' : 'No hay tickets registrados',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_tickets: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los tickets',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener ticket por código (para usuario final)
     */
    public function obtener_ticket_por_codigo(string $codigo_ticket): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL get_ticket_by_codigo(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $codigo_ticket, PDO::PARAM_STR);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Ticket encontrado' : 'Ticket no encontrado',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en obtener_ticket_por_codigo: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener el ticket',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Listar tickets para consulta en landing (por código, documento o número)
     */
    public function list_tickets_consulta_landing(string $busqueda): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_tickets_consulta_landing(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, trim($busqueda), PDO::PARAM_STR);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();
            return [
                'ok' => true,
                'msj' => empty($data) ? 'No se encontraron tickets' : 'Consulta exitosa',
                'data' => $data ?: []
            ];
        } catch (PDOException $e) {
            error_log("Error en list_tickets_consulta_landing: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al consultar tickets',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar comprobante de pago
     */
    public function registrar_comprobante(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL register_comprobante_pago(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @comprobante_id, @mensaje)";
            $query = $conectar->prepare($sql);
            
            $query->bindValue(1, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'ticket_id'), PDO::PARAM_INT);
            $this->bindNullable($query, 3, $this->getValue($data, 'metodo_pago_id'), PDO::PARAM_INT);
            $this->bindNullable($query, 4, $this->getValue($data, 'numero_operacion'), PDO::PARAM_STR);
            $query->bindValue(5, $this->getValue($data, 'monto'), PDO::PARAM_STR);
            $this->bindNullable($query, 6, $this->getValue($data, 'fecha_pago'), PDO::PARAM_STR);
            $query->bindValue(7, $this->getValue($data, 'archivo_comprobante'), PDO::PARAM_STR);
            $this->bindNullable($query, 8, $this->getValue($data, 'tipo_archivo'), PDO::PARAM_STR);
            $this->bindNullable($query, 9, $this->getValue($data, 'tamano_archivo'), PDO::PARAM_INT);
            $this->bindNullable($query, 10, $this->getValue($data, 'banco_origen'), PDO::PARAM_STR);
            $this->bindNullable($query, 11, $this->getValue($data, 'cuenta_origen'), PDO::PARAM_STR);
            $this->bindNullable($query, 12, $this->getValue($data, 'titular_origen'), PDO::PARAM_STR);
            $this->bindNullable($query, 13, $this->getValue($data, 'observaciones'), PDO::PARAM_STR);
            $query->bindValue(14, $this->getValue($data, 'creado_por'), PDO::PARAM_STR);
            
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @comprobante_id AS comprobante_id, @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false || stripos($mensaje, 'registrado') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje,
                'comprobante_id' => $result['comprobante_id'] ?? null
            ];
        } catch (PDOException $e) {
            error_log("Error en registrar_comprobante: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar el comprobante',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Listar comprobantes pendientes de validación
     */
    public function listar_comprobantes_pendientes(int $sede_id, ?string $estado = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_comprobantes_pendientes(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            if ($estado === null || $estado === '') {
                $query->bindValue(2, null, PDO::PARAM_NULL);
            } else {
                $query->bindValue(2, $estado, PDO::PARAM_STR);
            }
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Comprobantes obtenidos correctamente' : 'No hay comprobantes pendientes',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_comprobantes_pendientes: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los comprobantes',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Validar comprobante (aprobar o rechazar)
     */
    public function validar_comprobante(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL validar_comprobante(?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            
            $query->bindValue(1, $this->getValue($data, 'comprobante_id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(3, $this->getValue($data, 'estado'), PDO::PARAM_STR);
            $query->bindValue(4, $this->getValue($data, 'validado_por'), PDO::PARAM_STR);
            $this->bindNullable($query, 5, $this->getValue($data, 'motivo_rechazo'), PDO::PARAM_STR);
            
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false || stripos($mensaje, 'aprobado') !== false || stripos($mensaje, 'rechazado') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en validar_comprobante: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al validar el comprobante',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Listar ventas/compras realizadas
     */
    public function listar_ventas(int $sede_id, ?int $rifa_id = null, ?string $estado = null, ?string $fecha_desde = null, ?string $fecha_hasta = null, ?string $busqueda = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_ventas(?, ?, ?, ?, ?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $this->bindNullable($query, 2, $rifa_id, PDO::PARAM_INT);
            if ($estado === null || $estado === '') {
                $query->bindValue(3, null, PDO::PARAM_NULL);
            } else {
                $query->bindValue(3, $estado, PDO::PARAM_STR);
            }
            $this->bindNullable($query, 4, $fecha_desde, PDO::PARAM_STR);
            $this->bindNullable($query, 5, $fecha_hasta, PDO::PARAM_STR);
            $this->bindNullable($query, 6, $busqueda, PDO::PARAM_STR);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Ventas obtenidas correctamente' : 'No hay ventas registradas',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_ventas: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener las ventas',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener datos del comprobante para impresión
     */
    public function obtener_comprobante(int $ticket_id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL get_comprobante_ticket(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $ticket_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();
            
            if ($result) {
                return [
                    'ok' => true,
                    'data' => $result
                ];
            } else {
                return [
                    'ok' => false,
                    'msj' => 'No se encontró el comprobante'
                ];
            }
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'msj' => 'Error al obtener el comprobante: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Helper para obtener valor de array con default
     */
    private function getValue(array $data, string $key, $default = null)
    {
        return isset($data[$key]) ? $data[$key] : $default;
    }

    /**
     * Helper para bindear valores nullable
     */
    private function bindNullable($query, int $param, $value, int $type): void
    {
        if ($value === null || $value === '') {
            $query->bindValue($param, null, PDO::PARAM_NULL);
        } else {
            $query->bindValue($param, $value, $type);
        }
    }
}

