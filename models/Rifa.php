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
            $sql = "CALL register_rifa(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @rifa_id, @mensaje)";
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
            $sql = "CALL update_rifa(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
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
     * Cerrar rifa
     */
    public function cerrar_rifa(int $id, int $sede_id, string $modificado_por): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL cerrar_rifa(?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, trim($modificado_por), PDO::PARAM_STR);
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
            error_log("Error en cerrar_rifa: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al cerrar la rifa',
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
     * Liberar números reservados vencidos (helper interno)
     */
    private function liberar_numeros_vencidos(): void
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL liberar_numeros_vencidos()";
            $query = $conectar->prepare($sql);
            $query->execute();
            $query->closeCursor();
        } catch (PDOException $e) {
            // Log pero no fallar si hay error
            error_log("Error al liberar números vencidos: " . $e->getMessage());
        }
    }

    /**
     * Obtener números disponibles de una rifa
     */
    public function obtener_numeros_disponibles(int $rifa_id, ?int $limite = null, ?string $busqueda = null): array
    {
        try {
            $conectar = parent::Conexion();
            
            // Primero liberar números vencidos
            $this->liberar_numeros_vencidos();
            
            $sql = "SELECT 
                        id,
                        numero_entero,
                        numero_formateado,
                        estado
                    FROM numeros_rifa
                    WHERE rifa_id = ? 
                      AND estado = 'DISPONIBLE'";
            
            $params = [$rifa_id];
            
            // Agregar búsqueda si existe
            if ($busqueda !== null && $busqueda !== '') {
                $sql .= " AND (numero_formateado LIKE ? OR numero_entero = ?)";
                $params[] = "%{$busqueda}%";
                $params[] = (int) $busqueda;
            }
            
            $sql .= " ORDER BY numero_entero ASC";
            
            // Agregar límite si existe
            if ($limite !== null && $limite > 0) {
                $sql .= " LIMIT ?";
                $params[] = $limite;
            }
            
            $query = $conectar->prepare($sql);
            foreach ($params as $index => $param) {
                $query->bindValue($index + 1, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Números disponibles obtenidos correctamente' : 'No hay números disponibles',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en obtener_numeros_disponibles: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los números disponibles',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Reservar números específicos
     */
    public function reservar_numeros(int $rifa_id, array $numeros, string $sesion_id): array
    {
        try {
            $conectar = parent::Conexion();
            $conectar->beginTransaction();

            // Primero liberar números vencidos
            $this->liberar_numeros_vencidos();

            $numerosReservados = [];
            $numerosNoDisponibles = [];
            $reservadoHasta = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            foreach ($numeros as $numero) {
                $numeroEntero = is_numeric($numero) ? (int) $numero : null;
                
                if ($numeroEntero === null) {
                    $numerosNoDisponibles[] = $numero;
                    continue;
                }

                // Verificar si el número está disponible (incluyendo reservados vencidos)
                $checkSql = "SELECT id, numero_formateado, estado 
                            FROM numeros_rifa 
                            WHERE rifa_id = ? 
                              AND numero_entero = ? 
                              AND (
                                  estado = 'DISPONIBLE' 
                                  OR (estado = 'RESERVADO' AND ticket_id IS NULL AND (reservado_hasta IS NULL OR reservado_hasta < NOW()))
                              )
                            LIMIT 1";
                $checkQuery = $conectar->prepare($checkSql);
                $checkQuery->bindValue(1, $rifa_id, PDO::PARAM_INT);
                $checkQuery->bindValue(2, $numeroEntero, PDO::PARAM_INT);
                $checkQuery->execute();
                $numeroData = $checkQuery->fetch(PDO::FETCH_ASSOC);
                $checkQuery->closeCursor();

                if ($numeroData) {
                    // Si el número estaba reservado pero vencido, primero liberarlo
                    if ($numeroData['estado'] === 'RESERVADO') {
                        $liberarSql = "UPDATE numeros_rifa 
                                      SET estado = 'DISPONIBLE',
                                          reservado_hasta = NULL,
                                          reservado_por_sesion = NULL,
                                          fecha_reserva = NULL,
                                          fecha_modificacion = NOW()
                                      WHERE id = ?";
                        $liberarQuery = $conectar->prepare($liberarSql);
                        $liberarQuery->bindValue(1, $numeroData['id'], PDO::PARAM_INT);
                        $liberarQuery->execute();
                        $liberarQuery->closeCursor();
                    }
                    
                    // Reservar el número
                    $updateSql = "UPDATE numeros_rifa 
                                  SET estado = 'RESERVADO',
                                      reservado_hasta = ?,
                                      reservado_por_sesion = ?,
                                      fecha_reserva = NOW(),
                                      fecha_modificacion = NOW()
                                  WHERE id = ?";
                    $updateQuery = $conectar->prepare($updateSql);
                    $updateQuery->bindValue(1, $reservadoHasta, PDO::PARAM_STR);
                    $updateQuery->bindValue(2, $sesion_id, PDO::PARAM_STR);
                    $updateQuery->bindValue(3, $numeroData['id'], PDO::PARAM_INT);
                    $updateQuery->execute();
                    $updateQuery->closeCursor();

                    $numerosReservados[] = [
                        'numero_entero' => $numeroEntero,
                        'numero_formateado' => $numeroData['numero_formateado'],
                        'reservado_hasta' => $reservadoHasta
                    ];
                } else {
                    $numerosNoDisponibles[] = $numeroEntero;
                }
            }

            if (empty($numerosReservados)) {
                $conectar->rollBack();
                return [
                    'ok' => false,
                    'msj' => 'Ninguno de los números solicitados está disponible',
                    'numeros_no_disponibles' => $numerosNoDisponibles
                ];
            }

            $conectar->commit();

            return [
                'ok' => true,
                'msj' => count($numerosReservados) . ' número(s) reservado(s) correctamente',
                'numeros_reservados' => $numerosReservados,
                'numeros_no_disponibles' => $numerosNoDisponibles,
                'reservado_hasta' => $reservadoHasta
            ];
        } catch (PDOException $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }
            error_log("Error en reservar_numeros: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al reservar los números',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Liberar números reservados por sesión
     */
    public function liberar_numeros_reservados(int $rifa_id, string $sesion_id): array
    {
        try {
            $conectar = parent::Conexion();
            $conectar->beginTransaction();

            // Liberar números reservados por esta sesión que no tienen ticket_id asignado
            $sql = "UPDATE numeros_rifa 
                    SET estado = 'DISPONIBLE',
                        reservado_hasta = NULL,
                        reservado_por_sesion = NULL,
                        fecha_reserva = NULL,
                        fecha_modificacion = NOW()
                    WHERE rifa_id = ?
                      AND reservado_por_sesion = ?
                      AND estado = 'RESERVADO'
                      AND ticket_id IS NULL";
            
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(2, $sesion_id, PDO::PARAM_STR);
            $query->execute();
            $filasAfectadas = $query->rowCount();
            $query->closeCursor();

            $conectar->commit();

            return [
                'ok' => true,
                'msj' => $filasAfectadas > 0 
                    ? "$filasAfectadas número(s) liberado(s) correctamente" 
                    : 'No había números reservados para liberar',
                'numeros_liberados' => $filasAfectadas
            ];
        } catch (PDOException $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }
            error_log("Error en liberar_numeros_reservados: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al liberar los números reservados',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Asignar números aleatorios
     */
    public function asignar_numeros_aleatorios(int $rifa_id, int $cantidad, string $sesion_id): array
    {
        try {
            $conectar = parent::Conexion();
            $conectar->beginTransaction();

            // Primero liberar números vencidos
            $this->liberar_numeros_vencidos();

            // Obtener números disponibles aleatorios (incluyendo reservados vencidos)
            $sql = "SELECT id, numero_entero, numero_formateado 
                    FROM numeros_rifa 
                    WHERE rifa_id = ? 
                      AND (
                          estado = 'DISPONIBLE' 
                          OR (estado = 'RESERVADO' AND ticket_id IS NULL AND (reservado_hasta IS NULL OR reservado_hasta < NOW()))
                      )
                    ORDER BY RAND() 
                    LIMIT ?";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(2, $cantidad, PDO::PARAM_INT);
            $query->execute();
            $numerosDisponibles = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            if (count($numerosDisponibles) < $cantidad) {
                $conectar->rollBack();
                return [
                    'ok' => false,
                    'msj' => 'No hay suficientes números disponibles. Disponibles: ' . count($numerosDisponibles),
                    'disponibles' => count($numerosDisponibles),
                    'solicitados' => $cantidad
                ];
            }

            $numerosAsignados = [];
            $reservadoHasta = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            foreach ($numerosDisponibles as $numero) {
                // Si el número estaba reservado pero vencido, primero liberarlo
                if (isset($numero['estado']) && $numero['estado'] === 'RESERVADO') {
                    $liberarSql = "UPDATE numeros_rifa 
                                  SET estado = 'DISPONIBLE',
                                      reservado_hasta = NULL,
                                      reservado_por_sesion = NULL,
                                      fecha_reserva = NULL,
                                      fecha_modificacion = NOW()
                                  WHERE id = ?";
                    $liberarQuery = $conectar->prepare($liberarSql);
                    $liberarQuery->bindValue(1, $numero['id'], PDO::PARAM_INT);
                    $liberarQuery->execute();
                    $liberarQuery->closeCursor();
                }
                
                // Reservar el número
                $updateSql = "UPDATE numeros_rifa 
                              SET estado = 'RESERVADO',
                                  reservado_hasta = ?,
                                  reservado_por_sesion = ?,
                                  fecha_reserva = NOW(),
                                  fecha_modificacion = NOW()
                              WHERE id = ?";
                $updateQuery = $conectar->prepare($updateSql);
                $updateQuery->bindValue(1, $reservadoHasta, PDO::PARAM_STR);
                $updateQuery->bindValue(2, $sesion_id, PDO::PARAM_STR);
                $updateQuery->bindValue(3, $numero['id'], PDO::PARAM_INT);
                $updateQuery->execute();
                $updateQuery->closeCursor();

                $numerosAsignados[] = [
                    'numero_entero' => $numero['numero_entero'],
                    'numero_formateado' => $numero['numero_formateado'],
                    'reservado_hasta' => $reservadoHasta
                ];
            }

            $conectar->commit();

            return [
                'ok' => true,
                'msj' => count($numerosAsignados) . ' número(s) asignado(s) correctamente',
                'numeros' => $numerosAsignados,
                'reservado_hasta' => $reservadoHasta
            ];
        } catch (PDOException $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }
            error_log("Error en asignar_numeros_aleatorios: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al asignar números aleatorios',
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
        $this->bindNullable($stmt, 3, $this->nullIfEmpty($this->getValue($data, 'codigo')), PDO::PARAM_STR);
        $stmt->bindValue(4, $this->getValue($data, 'nombre'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 5, $this->getValue($data, 'descripcion'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 6, $this->getValue($data, 'numero_intentos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 7, $this->getValue($data, 'intento_ganador'), PDO::PARAM_INT);
        $stmt->bindValue(8, $this->getValue($data, 'precio_ticket'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 9, $this->getValue($data, 'cantidad_maxima_tickets'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 10, $this->getValue($data, 'cantidad_maxima_por_persona'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 11, $this->getValue($data, 'usa_numeracion_boletos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 12, $this->getValue($data, 'tipo_numeracion'), PDO::PARAM_STR);
        $stmt->bindValue(13, $this->getValue($data, 'numero_inicial'), PDO::PARAM_INT);
        $stmt->bindValue(14, $this->getValue($data, 'numero_final'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 15, $this->getValue($data, 'cantidad_digitos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 16, $this->getValue($data, 'prefijo_numero'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 17, $this->getValue($data, 'sufijo_numero'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 18, $this->getValue($data, 'permitir_seleccion_numero'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 19, $this->getValue($data, 'asignacion_automatica'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 20, $this->getValue($data, 'mostrar_numeros_disponibles'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 21, $this->getValue($data, 'numeros_bloqueados'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 22, $this->getValue($data, 'numeros_especiales'), PDO::PARAM_STR);
        $stmt->bindValue(23, $this->getValue($data, 'fecha_inicio_venta'), PDO::PARAM_STR);
        $stmt->bindValue(24, $this->getValue($data, 'fecha_fin_venta'), PDO::PARAM_STR);
        $stmt->bindValue(25, $this->getValue($data, 'fecha_sorteo'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 26, $this->getValue($data, 'mostrar_contador'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 27, $this->getValue($data, 'mostrar_participantes'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 28, $this->getValue($data, 'mostrar_tickets_vendidos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 29, $this->getValue($data, 'texto_promocional'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 30, $this->getValue($data, 'reglas_participacion'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 31, $this->getValue($data, 'terminos_condiciones'), PDO::PARAM_STR);
        $stmt->bindValue(32, $this->getValue($data, 'estado', 'BORRADOR'), PDO::PARAM_STR);
        $stmt->bindValue(33, $this->getValue($data, 'creado_por'), PDO::PARAM_STR);
    }

    /**
     * Helper para bindear parámetros de actualización
     */
    private function bindUpdateParams(PDOStatement $stmt, array $data): void
    {
        $stmt->bindValue(1, $this->getValue($data, 'id'), PDO::PARAM_INT);
        $stmt->bindValue(2, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 3, $this->getValue($data, 'premio_id'), PDO::PARAM_INT);
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
        $this->bindNullable($stmt, 22, $this->getValue($data, 'numeros_bloqueados'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 23, $this->getValue($data, 'numeros_especiales'), PDO::PARAM_STR);
        $stmt->bindValue(24, $this->getValue($data, 'fecha_inicio_venta'), PDO::PARAM_STR);
        $stmt->bindValue(25, $this->getValue($data, 'fecha_fin_venta'), PDO::PARAM_STR);
        $stmt->bindValue(26, $this->getValue($data, 'fecha_sorteo'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 27, $this->getValue($data, 'fecha_sorteo_realizado'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 28, $this->getValue($data, 'mostrar_contador'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 29, $this->getValue($data, 'mostrar_participantes'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 30, $this->getValue($data, 'mostrar_tickets_vendidos'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 31, $this->getValue($data, 'texto_promocional'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 32, $this->getValue($data, 'reglas_participacion'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 33, $this->getValue($data, 'terminos_condiciones'), PDO::PARAM_STR);
        $stmt->bindValue(34, $this->getValue($data, 'estado'), PDO::PARAM_STR);
        $this->bindNullable($stmt, 35, $this->getValue($data, 'estado_activo'), PDO::PARAM_INT);
        $this->bindNullable($stmt, 36, $this->getValue($data, 'regenerar_numeros'), PDO::PARAM_INT);
        $stmt->bindValue(37, $this->getValue($data, 'modificado_por'), PDO::PARAM_STR);
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
     * Helper para convertir valores vacíos a null
     */
    private function nullIfEmpty($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }
        return $value;
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

    /**
     * Obtener próximo sorteo (una sola rifa, ligero - para hero landing)
     */
    public function get_proximo_sorteo(?int $sede_id = null): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL get_proximo_sorteo(?)";
            $query = $conectar->prepare($sql);
            if ($sede_id === null) {
                $query->bindValue(1, null, PDO::PARAM_NULL);
            } else {
                $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            }
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => $data ? 'Próximo sorteo obtenido' : 'No hay próximo sorteo',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en get_proximo_sorteo: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener próximo sorteo',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Generar números de boletos para una rifa
     */
    public function generar_numeros_rifa(int $rifa_id, int $sede_id, string $creado_por): array
    {
        try {
            // Primero obtener la información de la rifa
            $rifa = $this->obtener_rifa_por_id($rifa_id, $sede_id);
            
            if (!$rifa['ok'] || !isset($rifa['data'])) {
                return [
                    'ok' => false,
                    'msj' => 'No se pudo obtener la información de la rifa'
                ];
            }

            $rifaData = $rifa['data'];

            // Verificar que la rifa use numeración de boletos
            if (empty($rifaData['usa_numeracion_boletos']) || $rifaData['usa_numeracion_boletos'] != 1) {
                return [
                    'ok' => false,
                    'msj' => 'Esta rifa no está configurada para usar numeración de boletos'
                ];
            }

            // Verificar que tenga rango configurado
            if (empty($rifaData['numero_inicial']) || empty($rifaData['numero_final'])) {
                return [
                    'ok' => false,
                    'msj' => 'La rifa no tiene configurado el rango de números (inicial/final)'
                ];
            }

            $conectar = parent::Conexion();
            
            // Log de información de la rifa
            error_log("Generando números para rifa_id: $rifa_id, sede_id: $sede_id");
            error_log("Rango: {$rifaData['numero_inicial']} - {$rifaData['numero_final']}");
            error_log("Prefijo: " . ($rifaData['prefijo_numero'] ?? 'NULL') . ", Sufijo: " . ($rifaData['sufijo_numero'] ?? 'NULL'));
            
            // Verificar si el procedimiento existe
            try {
                $checkProc = $conectar->query("SHOW PROCEDURE STATUS WHERE Db = DATABASE() AND Name = 'generate_rifa_numeros'");
                $procedimientoExiste = $checkProc->rowCount() > 0;
                error_log("Procedimiento existe: " . ($procedimientoExiste ? 'SÍ' : 'NO'));
            } catch (PDOException $e) {
                error_log("Error al verificar procedimiento: " . $e->getMessage());
                $procedimientoExiste = false;
            }
            
            if ($procedimientoExiste) {
                // Usar procedimiento almacenado si existe
                try {
                    $sql = "CALL generate_rifa_numeros(?, ?, ?, ?, ?, ?, ?, ?, @mensaje)";
                    $query = $conectar->prepare($sql);
                    $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
                    $query->bindValue(2, $sede_id, PDO::PARAM_INT);
                    $query->bindValue(3, (int) $rifaData['numero_inicial'], PDO::PARAM_INT);
                    $query->bindValue(4, (int) $rifaData['numero_final'], PDO::PARAM_INT);
                    $query->bindValue(5, isset($rifaData['cantidad_digitos']) ? (int) $rifaData['cantidad_digitos'] : 4, PDO::PARAM_INT);
                    $query->bindValue(6, $rifaData['prefijo_numero'] ?? null, PDO::PARAM_STR);
                    $query->bindValue(7, $rifaData['sufijo_numero'] ?? null, PDO::PARAM_STR);
                    $query->bindValue(8, $creado_por, PDO::PARAM_STR);
                    
                    error_log("Ejecutando procedimiento almacenado con parámetros: rifa_id=$rifa_id, sede_id=$sede_id, inicial={$rifaData['numero_inicial']}, final={$rifaData['numero_final']}");
                    
                    $query->execute();
                    $query->closeCursor();

                    $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
                    $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
                    $mensajeStmt->closeCursor();

                    $mensaje = $result['mensaje'] ?? 'Error desconocido';
                    error_log("Mensaje del procedimiento: " . $mensaje);
                    
                    $ok = stripos($mensaje, 'correctamente') !== false;

                    if (!$ok) {
                        // Si hay error, obtener más información
                        $errorInfo = $conectar->errorInfo();
                        error_log("Error Info: " . print_r($errorInfo, true));
                    }

                    return [
                        'ok' => $ok,
                        'msj' => $mensaje,
                        'detalle' => $ok ? null : ($conectar->errorInfo()[2] ?? null)
                    ];
                } catch (PDOException $e) {
                    error_log("PDOException al ejecutar procedimiento: " . $e->getMessage());
                    error_log("Error Info: " . print_r($conectar->errorInfo(), true));
                    throw $e;
                }
            } else {
                // Generar números directamente sin procedimiento almacenado
                $numero_inicial = (int) $rifaData['numero_inicial'];
                $numero_final = (int) $rifaData['numero_final'];
                $cantidad_digitos = isset($rifaData['cantidad_digitos']) ? (int) $rifaData['cantidad_digitos'] : 4;
                $prefijo = $rifaData['prefijo_numero'] ?? '';
                $sufijo = $rifaData['sufijo_numero'] ?? '';
                
                $conectar->beginTransaction();
                
                try {
                    // Verificar que la tabla existe
                    $checkTable = $conectar->query("SHOW TABLES LIKE 'numeros_rifa'");
                    if ($checkTable->rowCount() == 0) {
                        throw new Exception("La tabla 'numeros_rifa' no existe en la base de datos. Ejecuta el script docs/sql/bd_rifas_mysql.sql");
                    }
                    
                    // Preparar valores para inserción múltiple (más eficiente)
                    $valores = [];
                    $contador = 0;
                    
                    for ($numero = $numero_inicial; $numero <= $numero_final; $numero++) {
                        $numero_formateado = $prefijo . str_pad($numero, $cantidad_digitos, '0', STR_PAD_LEFT) . $sufijo;
                        // La tabla tiene fecha_modificacion con ON UPDATE CURRENT_TIMESTAMP, no necesita especificarse
                        $valores[] = "($sede_id, $rifa_id, $numero, " . $conectar->quote($numero_formateado) . ", 'DISPONIBLE')";
                        $contador++;
                        
                        // Insertar en lotes de 500 para mejor rendimiento
                        if (count($valores) >= 500) {
                            $sqlBatch = "INSERT IGNORE INTO numeros_rifa (
                                sede_id, rifa_id, numero_entero, numero_formateado, estado
                            ) VALUES " . implode(', ', $valores);
                            
                            error_log("Ejecutando SQL batch (primeros 200 chars): " . substr($sqlBatch, 0, 200) . "...");
                            $resultado = $conectar->exec($sqlBatch);
                            error_log("Filas afectadas en batch: $resultado");
                            $valores = [];
                        }
                    }
                    
                    // Insertar los valores restantes
                    if (!empty($valores)) {
                        $sqlBatch = "INSERT IGNORE INTO numeros_rifa (
                            sede_id, rifa_id, numero_entero, numero_formateado, estado
                        ) VALUES " . implode(', ', $valores);
                        
                        error_log("Ejecutando SQL final (primeros 200 chars): " . substr($sqlBatch, 0, 200) . "...");
                        $resultado = $conectar->exec($sqlBatch);
                        error_log("Filas afectadas en final: $resultado");
                    }
                    
                    $conectar->commit();
                    
                    return [
                        'ok' => true,
                        'msj' => "Números generados correctamente. Se procesaron $contador números."
                    ];
                } catch (PDOException $e) {
                    $conectar->rollBack();
                    error_log("PDOException en generar_numeros_rifa: " . $e->getMessage());
                    error_log("SQL Error Info: " . print_r($conectar->errorInfo(), true));
                    throw $e;
                } catch (Exception $e) {
                    $conectar->rollBack();
                    error_log("Exception en generar_numeros_rifa: " . $e->getMessage());
                    throw $e;
                }
            }
        } catch (PDOException $e) {
            error_log("Error en generar_numeros_rifa: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $errorMsg = $e->getMessage();
            
            // Mensaje más amigable si el procedimiento no existe
            if (stripos($errorMsg, 'does not exist') !== false || stripos($errorMsg, 'no existe') !== false) {
                return [
                    'ok' => false,
                    'msj' => 'El procedimiento almacenado generate_rifa_numeros no existe. Ejecuta el archivo docs/sql/rifas.sql en tu base de datos.',
                    'detalle' => $errorMsg
                ];
            }
            
            return [
                'ok' => false,
                'msj' => 'Error al generar los números de la rifa: ' . $errorMsg,
                'detalle' => $errorMsg,
                'code' => $e->getCode()
            ];
        } catch (Exception $e) {
            error_log("Error general en generar_numeros_rifa: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'ok' => false,
                'msj' => 'Error inesperado al generar los números: ' . $e->getMessage(),
                'detalle' => $e->getMessage(),
                'tipo' => get_class($e)
            ];
        }
    }
}


