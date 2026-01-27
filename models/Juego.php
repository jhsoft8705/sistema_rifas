<?php
/**
 * Modelo Juego
 * Maneja operaciones del proceso de juego de rifas
 */
class Juego extends Conectar
{
    /**
     * Listar rifas listas para jugar
     */
    public function listar_rifas_para_jugar(int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_rifas_para_jugar(?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Rifas listas para jugar obtenidas correctamente' : 'No hay rifas listas para jugar',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_rifas_para_jugar: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener las rifas listas para jugar',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener participantes de una rifa
     */
    public function listar_participantes_rifa(int $rifa_id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_participantes_rifa(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Participantes obtenidos correctamente' : 'No hay participantes en esta rifa',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_participantes_rifa: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los participantes',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Jugar premio de rifa (seleccionar número aleatorio)
     */
    public function jugar_premio_rifa(int $rifa_id, int $rifa_premio_id, int $sede_id, string $jugado_por): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL jugar_premio_rifa(?, ?, ?, ?, @numero_seleccionado_id, @numero_formateado, @persona_seleccionada_id, @nombre_completo, @ticket_id, @intento_actual, @es_ganador, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(2, $rifa_premio_id, PDO::PARAM_INT);
            $query->bindValue(3, $sede_id, PDO::PARAM_INT);
            $query->bindValue(4, trim($jugado_por), PDO::PARAM_STR);
            $query->execute();
            $query->closeCursor();

            // Obtener los valores de salida
            $resultStmt = $conectar->query("SELECT @numero_seleccionado_id AS numero_id, @numero_formateado AS numero_formateado, @persona_seleccionada_id AS persona_id, @nombre_completo AS nombre_completo, @ticket_id AS ticket_id, @intento_actual AS intento_actual, @es_ganador AS es_ganador, @mensaje AS mensaje");
            $result = $resultStmt->fetch(PDO::FETCH_ASSOC);
            $resultStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'Error') === false;

            return [
                'ok' => $ok,
                'msj' => $mensaje,
                'numero_id' => $result['numero_id'] ?? null,
                'numero_formateado' => $result['numero_formateado'] ?? null,
                'persona_id' => $result['persona_id'] ?? null,
                'nombre_completo' => $result['nombre_completo'] ?? null,
                'ticket_id' => $result['ticket_id'] ?? null,
                'intento_actual' => (int) ($result['intento_actual'] ?? 0),
                'es_ganador' => (int) ($result['es_ganador'] ?? 0) === 1
            ];
        } catch (PDOException $e) {
            error_log("Error en jugar_premio_rifa: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al ejecutar el juego',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Listar premios de una rifa para jugar
     */
    public function listar_premios_rifa_para_jugar(int $rifa_id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL list_premios_rifa_para_jugar(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Premios obtenidos correctamente' : 'No hay premios para esta rifa',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_premios_rifa_para_jugar: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los premios',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener información del juego de un premio
     */
    public function obtener_info_juego_premio(int $rifa_id, int $rifa_premio_id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL get_info_juego_premio(?, ?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(2, $rifa_premio_id, PDO::PARAM_INT);
            $query->bindValue(3, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Información del juego obtenida correctamente' : 'No se encontró información del juego',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en obtener_info_juego_premio: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener la información del juego',
                'data' => null,
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar ganador
     */
    public function registrar_ganador(
        int $sede_id,
        int $rifa_id,
        int $rifa_premio_id,
        int $premio_id,
        int $persona_id,
        ?int $ticket_id,
        ?int $numero_id,
        ?string $direccion_envio,
        ?string $ciudad_envio,
        ?string $pais_envio,
        bool $publicar_web,
        int $intento_ganador,
        string $jugado_por,
        string $creado_por
    ): array {
        try {
            $conectar = parent::Conexion();
            $placeholders = implode(', ', array_fill(0, 14, '?'));
            $sql = "CALL register_ganador($placeholders, @mensaje)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->bindValue(2, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(3, $rifa_premio_id, PDO::PARAM_INT);
            $query->bindValue(4, $premio_id, PDO::PARAM_INT);
            $query->bindValue(5, $persona_id, PDO::PARAM_INT);
            $this->bindNullable($query, 6, $ticket_id, PDO::PARAM_INT);
            $this->bindNullable($query, 7, $numero_id, PDO::PARAM_INT);
            $this->bindNullable($query, 8, $direccion_envio, PDO::PARAM_STR);
            $this->bindNullable($query, 9, $ciudad_envio, PDO::PARAM_STR);
            $this->bindNullable($query, 10, $pais_envio, PDO::PARAM_STR);
            $query->bindValue(11, $publicar_web ? 1 : 0, PDO::PARAM_INT);
            $query->bindValue(12, $intento_ganador, PDO::PARAM_INT);
            $query->bindValue(13, trim($jugado_por), PDO::PARAM_STR);
            $query->bindValue(14, trim($creado_por), PDO::PARAM_STR);
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
            error_log("Error en registrar_ganador: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar el ganador',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener números ganadores de un ganador
     */
    public function obtener_numeros_ganador(int $ganador_id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL get_numeros_ganador(?, ?)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $ganador_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();

            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Números ganadores obtenidos correctamente' : 'No se encontraron números ganadores',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en obtener_numeros_ganador: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los números ganadores',
                'data' => [],
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar si rifa está completa (todos los premios tienen ganador)
     */
    public function verificar_rifa_completa(int $rifa_id, int $sede_id): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL check_rifa_completa(?, ?, @todos_premios_ganados, @total_premios, @premios_ganados)";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $rifa_id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $query->closeCursor();

            $resultStmt = $conectar->query("SELECT @todos_premios_ganados AS todos_ganados, @total_premios AS total, @premios_ganados AS ganados");
            $result = $resultStmt->fetch(PDO::FETCH_ASSOC);
            $resultStmt->closeCursor();

            return [
                'ok' => true,
                'todos_premios_ganados' => (int) ($result['todos_ganados'] ?? 0) === 1,
                'total_premios' => (int) ($result['total'] ?? 0),
                'premios_ganados' => (int) ($result['ganados'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("Error en verificar_rifa_completa: " . $e->getMessage());
            return [
                'ok' => false,
                'todos_premios_ganados' => false,
                'total_premios' => 0,
                'premios_ganados' => 0
            ];
        }
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

}
