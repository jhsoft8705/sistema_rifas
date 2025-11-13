<?php
/**
 * Modelo Rifa
 * Manejo de operaciones CRUD para rifas utilizando procedimientos almacenados
 */
class Rifa extends Conectar
{
    /**
     * Listar rifas por sede (opcionalmente por estado)
     */
    public function listar_rifas(int $sede_id, ?string $estado = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_rifas(?, ?)";
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
                'msj' => !empty($data) ? 'Rifas obtenidas correctamente' : 'No hay rifas registradas',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_rifas: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener las rifas',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener rifa por ID
     */
    public function obtener_rifa_por_id(int $id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_rifa_by_id(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Rifa encontrada' : 'Rifa no existe en esta sede',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en obtener_rifa_por_id: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener la rifa',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar una nueva rifa
     * @param array $data Datos de la rifa
     */
    public function registrar_rifa(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL register_rifa(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @rifa_id, @mensaje)";
            $query = $conectar->prepare($sql);

            $this->bindRegisterParams($query, $data);

            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @rifa_id AS rifa_id, @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje,
                'rifa_id' => $result['rifa_id'] ?? null
            ];
        } catch (PDOException $e) {
            error_log("Error en registrar_rifa: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar la rifa',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar rifa
     * @param array $data Datos de la rifa (debe incluir id y sede_id)
     */
    public function actualizar_rifa(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL update_rifa(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);

            $this->bindUpdateParams($query, $data);

            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en actualizar_rifa: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar la rifa',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar (lógico) una rifa
     */
    public function eliminar_rifa(int $id, int $sede_id, string $modificado_por): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL delete_rifa(?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, $modificado_por, PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en eliminar_rifa: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al eliminar la rifa',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Listar premios asociados a una rifa
     */
    public function listar_rifa_premios(int $rifa_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_rifa_premios(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Premios asociados obtenidos correctamente' : 'No hay premios asociados',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_rifa_premios: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los premios de la rifa',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar premio para una rifa
     */
    public function registrar_rifa_premio(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $placeholders = implode(', ', array_fill(0, 10, '?'));
            $sql = "CALL register_rifa_premio($placeholders, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $this->getValue($data, 'rifa_id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(3, $this->getValue($data, 'premio_id'), PDO::PARAM_INT);
            $this->bindNullable($query, 4, $this->getValue($data, 'orden'), PDO::PARAM_INT);
            $this->bindNullable($query, 5, $this->getValue($data, 'es_principal'), PDO::PARAM_INT);
            $this->bindNullable($query, 6, $this->getValue($data, 'titulo'), PDO::PARAM_STR);
            $this->bindNullable($query, 7, $this->getValue($data, 'descripcion'), PDO::PARAM_STR);
            $this->bindNullable($query, 8, $this->getValue($data, 'cantidad'), PDO::PARAM_INT);
            $this->bindNullable($query, 9, $this->getValue($data, 'valor_estimado'), PDO::PARAM_STR);
            $query->bindValue(10, $this->getValue($data, 'creado_por'), PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en registrar_rifa_premio: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar el premio de la rifa',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar premio asociado
     */
    public function actualizar_rifa_premio(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $placeholders = implode(', ', array_fill(0, 11, '?'));
            $sql = "CALL update_rifa_premio($placeholders, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $this->getValue($data, 'id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'rifa_id'), PDO::PARAM_INT);
            $query->bindValue(3, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
            $this->bindNullable($query, 4, $this->getValue($data, 'orden'), PDO::PARAM_INT);
            $this->bindNullable($query, 5, $this->getValue($data, 'es_principal'), PDO::PARAM_INT);
            $this->bindNullable($query, 6, $this->getValue($data, 'titulo'), PDO::PARAM_STR);
            $this->bindNullable($query, 7, $this->getValue($data, 'descripcion'), PDO::PARAM_STR);
            $this->bindNullable($query, 8, $this->getValue($data, 'cantidad'), PDO::PARAM_INT);
            $this->bindNullable($query, 9, $this->getValue($data, 'valor_estimado'), PDO::PARAM_STR);
            $this->bindNullable($query, 10, $this->getValue($data, 'estado'), PDO::PARAM_INT);
            $query->bindValue(11, $this->getValue($data, 'modificado_por'), PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en actualizar_rifa_premio: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar el premio de la rifa',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar (inactivar) premio asociado
     */
    public function eliminar_rifa_premio(int $id, int $rifa_id, int $sede_id, string $modificado_por): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL delete_rifa_premio(?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(3, $sede_id, PDO::PARAM_INT);
            $query->bindValue(4, $modificado_por, PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en eliminar_rifa_premio: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al eliminar el premio de la rifa',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Listar números/cartillas de una rifa
     */
    public function listar_numeros_rifa(int $rifa_id, ?string $estado = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_rifa_numeros(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
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
                'msj' => !empty($data) ? 'Números obtenidos correctamente' : 'No hay números registrados',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_numeros_rifa: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los números de la rifa',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar estado de un número/cartilla
     */
    public function actualizar_estado_numero(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL update_estado_numero_rifa(?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $this->getValue($data, 'numero_id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'rifa_id'), PDO::PARAM_INT);
            $query->bindValue(3, $this->getValue($data, 'estado'), PDO::PARAM_STR);
            $this->bindNullable($query, 4, $this->getValue($data, 'ticket_id'), PDO::PARAM_INT);
            $this->bindNullable($query, 5, $this->getValue($data, 'reservado_hasta'), PDO::PARAM_STR);
            $this->bindNullable($query, 6, $this->getValue($data, 'reservado_por_sesion'), PDO::PARAM_STR);
            $query->bindValue(7, $this->getValue($data, 'modificado_por'), PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en actualizar_estado_numero: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar el estado del número',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Helper para bindear parámetros de registro
     */
    private function bindRegisterParams(PDOStatement $stmt, array $data): void
    {
        $stmt->bindValue(1, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 2, $this->getValue($data, 'premio_id'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 3, $this->getValue($data, 'ubicacion_id'), PDO::PARAM_INT);
        $stmt->bindValue(4, $this->getValue($data, 'codigo'), PDO::PARAM_STR);
        $stmt->bindValue(5, $this->getValue($data, 'nombre'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 6, $this->getValue($data, 'descripcion'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 7, $this->getValue($data, 'numero_intentos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 8, $this->getValue($data, 'intento_ganador'), PDO::PARAM_INT);
        $stmt->bindValue(9, $this->getValue($data, 'precio_ticket'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 10, $this->getValue($data, 'cantidad_maxima_tickets'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 11, $this->getValue($data, 'cantidad_maxima_por_persona'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 12, $this->getValue($data, 'usa_numeracion_boletos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 13, $this->getValue($data, 'tipo_numeracion'), PDO::PARAM_STR);
        $stmt->bindValue(14, $this->getValue($data, 'numero_inicial'), PDO::PARAM_INT);
        $stmt->bindValue(15, $this->getValue($data, 'numero_final'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 16, $this->getValue($data, 'cantidad_digitos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 17, $this->getValue($data, 'prefijo_numero'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 18, $this->getValue($data, 'sufijo_numero'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 19, $this->getValue($data, 'permitir_seleccion_numero'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 20, $this->getValue($data, 'asignacion_automatica'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 21, $this->getValue($data, 'mostrar_numeros_disponibles'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 22, $this->getValue($data, 'generar_volantarios'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 23, $this->getValue($data, 'numeros_por_volantario'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 24, $this->getValue($data, 'formato_impresion'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 25, $this->getValue($data, 'numeros_por_pagina'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 26, $this->getValue($data, 'numeros_bloqueados'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 27, $this->getValue($data, 'numeros_especiales'), PDO::PARAM_STR);
        $stmt->bindValue(28, $this->getValue($data, 'fecha_inicio_venta'), PDO::PARAM_STR);
        $stmt->bindValue(29, $this->getValue($data, 'fecha_fin_venta'), PDO::PARAM_STR);
        $stmt->bindValue(30, $this->getValue($data, 'fecha_sorteo'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 31, $this->getValue($data, 'mostrar_contador'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 32, $this->getValue($data, 'mostrar_participantes'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 33, $this->getValue($data, 'mostrar_tickets_vendidos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 34, $this->getValue($data, 'tipo_publicidad'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 35, $this->getValue($data, 'url_banner'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 36, $this->getValue($data, 'texto_promocional'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 37, $this->getValue($data, 'reglas_participacion'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 38, $this->getValue($data, 'terminos_condiciones'), PDO::PARAM_STR);
        $stmt->bindValue(39, $this->getValue($data, 'creado_por'), PDO::PARAM_STR);
    }

    /**
     * Helper para bindear parámetros de actualización
     */
    private function bindUpdateParams(PDOStatement $stmt, array $data): void
    {
        $stmt->bindValue(1, $this->getValue($data, 'id'), PDO::PARAM_INT);
        $stmt->bindValue(2, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 3, $this->getValue($data, 'premio_id'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 4, $this->getValue($data, 'ubicacion_id'), PDO::PARAM_INT);
        $stmt->bindValue(5, $this->getValue($data, 'codigo'), PDO::PARAM_STR);
        $stmt->bindValue(6, $this->getValue($data, 'nombre'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 7, $this->getValue($data, 'descripcion'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 8, $this->getValue($data, 'numero_intentos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 9, $this->getValue($data, 'intento_ganador'), PDO::PARAM_INT);
        $stmt->bindValue(10, $this->getValue($data, 'precio_ticket'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 11, $this->getValue($data, 'cantidad_maxima_tickets'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 12, $this->getValue($data, 'cantidad_maxima_por_persona'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 13, $this->getValue($data, 'usa_numeracion_boletos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 14, $this->getValue($data, 'tipo_numeracion'), PDO::PARAM_STR);
        $stmt->bindValue(15, $this->getValue($data, 'numero_inicial'), PDO::PARAM_INT);
        $stmt->bindValue(16, $this->getValue($data, 'numero_final'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 17, $this->getValue($data, 'cantidad_digitos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 18, $this->getValue($data, 'prefijo_numero'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 19, $this->getValue($data, 'sufijo_numero'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 20, $this->getValue($data, 'permitir_seleccion_numero'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 21, $this->getValue($data, 'asignacion_automatica'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 22, $this->getValue($data, 'mostrar_numeros_disponibles'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 23, $this->getValue($data, 'generar_volantarios'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 24, $this->getValue($data, 'numeros_por_volantario'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 25, $this->getValue($data, 'formato_impresion'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 26, $this->getValue($data, 'numeros_por_pagina'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 27, $this->getValue($data, 'numeros_bloqueados'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 28, $this->getValue($data, 'numeros_especiales'), PDO::PARAM_STR);
        $stmt->bindValue(29, $this->getValue($data, 'fecha_inicio_venta'), PDO::PARAM_STR);
        $stmt->bindValue(30, $this->getValue($data, 'fecha_fin_venta'), PDO::PARAM_STR);
        $stmt->bindValue(31, $this->getValue($data, 'fecha_sorteo'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 32, $this->getValue($data, 'fecha_sorteo_realizado'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 33, $this->getValue($data, 'mostrar_contador'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 34, $this->getValue($data, 'mostrar_participantes'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 35, $this->getValue($data, 'mostrar_tickets_vendidos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 36, $this->getValue($data, 'tipo_publicidad'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 37, $this->getValue($data, 'url_banner'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 38, $this->getValue($data, 'texto_promocional'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 39, $this->getValue($data, 'reglas_participacion'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 40, $this->getValue($data, 'terminos_condiciones'), PDO::PARAM_STR);
        $stmt->bindValue(41, $this->getValue($data, 'estado'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 42, $this->getValue($data, 'estado_activo'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 43, $this->getValue($data, 'regenerar_numeros'), PDO::PARAM_INT);
        $stmt->bindValue(44, $this->getValue($data, 'modificado_por'), PDO::PARAM_STR);
    }

    /**
     * Helper bind nullable
     */
    private function bindNullable(PDOStatement $statement, int $position, $value, int $type): void
    {
        if ($value === null || $value === '') {
            $statement->bindValue($position, null, PDO::PARAM_NULL);
            return;
        }
        $statement->bindValue($position, $value, $type);
    }

    /**
     * Helper para obtener valores del array con default
     */
    private function getValue(array $data, string $key, $default = null)
    {
        return array_key_exists($key, $data) ? $data[$key] : $default;
    }

    /**
     * Listar rifas públicas (estado PUBLICADA) para landing page
     */
    public function listar_rifas_publicas(?int $sede_id = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_rifas_publicas(?)";
            $query = $conectar->prepare($sql);
            if ($sede_id === null) {
                $query->bindValue(1, null, PDO::PARAM_NULL);
            } else {
                $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            }
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            // Para cada rifa, obtener sus premios asociados
            foreach ($data as &$rifa) {
                $premios = $this->listar_rifa_premios((int) $rifa['id']);
                if ($premios['ok']) {
                    $rifa['premios'] = $premios['data'];
                } else {
                    $rifa['premios'] = [];
                }
            }

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Rifas públicas obtenidas correctamente' : 'No hay rifas públicas disponibles',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_rifas_publicas: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener las rifas públicas',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }
}


