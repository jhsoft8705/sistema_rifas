<?php
/**
 * Modelo Cargo
 * Manejo de operaciones CRUD para cargos con soporte multi-sede
 */
class Cargo extends Conectar
{
    /**
     * Listar todos los cargos activos de una sede
     * @param int $sede_id ID de la sede
     * @return array Respuesta con listado de cargos
     */
    public function listar_cargos($sede_id)
    {
        try {
            $conectar = parent::Conexion();
            $sql = "EXEC list_cargo @sede_id = ?";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'ok' => true,
                'msj' => !empty($data) ? 'Cargos obtenidos correctamente' : 'No hay cargos registrados en esta sede',
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en listar_cargos: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener los cargos',
                'data' => []

            ];
        }
    }

    /**
     * Obtener un cargo específico por ID
     * @param int $id ID del cargo
     * @param int $sede_id ID de la sede
     * @return array Respuesta con datos del cargo
     */
    public function obtener_cargo_por_id($id, $sede_id)
    {
        try {
            $conectar = parent::Conexion();
            $sql = "EXEC list_cargos_by_id @id = ?, @sede_id = ?";
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch(PDO::FETCH_ASSOC);

            return [
                'ok' => !empty($data),
                'msj' => !empty($data) ? 'Cargo encontrado' : 'Cargo no existe en esta sede',
                'data' => $data ?: null
            ];
        } catch (PDOException $e) {
            error_log("Error en obtener_cargo_por_id: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al obtener el cargo',
                'data' => null
            ];
        }
    }

    /**
     * Registrar un nuevo cargo
     * @param int $sede_id ID de la sede
     * @param string $nombre_cargo Nombre del cargo
     * @param string|null $descripcion Descripción del cargo
     * @param float|null $salario_base Salario base (opcional)
     * @param string $creado_por Usuario que crea el registro
     * @return array Respuesta de la operación
     */
    public function registrar_cargo($sede_id, $nombre_cargo, $descripcion, $salario_base, $creado_por)
    {
        try {
            $conectar = parent::Conexion();
            $sql = "
                DECLARE @mensaje NVARCHAR(255);
                EXEC register_cargo 
                    @sede_id = ?, 
                    @nombre_cargo = ?, 
                    @descripcion = ?, 
                    @salario_base = ?, 
                    @creado_por = ?, 
                    @mensaje = @mensaje OUTPUT;
                SELECT @mensaje AS mensaje;
            ";
            
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $sede_id, PDO::PARAM_INT);
            $query->bindValue(2, $nombre_cargo, PDO::PARAM_STR);
            $query->bindValue(3, $descripcion, PDO::PARAM_STR);
            $query->bindValue(4, $salario_base, PDO::PARAM_STR);
            $query->bindValue(5, $creado_por, PDO::PARAM_STR);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            $mensaje = $result['mensaje'] ?? 'Error desconocido';

            // Determinar si fue exitoso
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en registrar_cargo: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al registrar el cargo',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar un cargo existente
     * @param int $id ID del cargo
     * @param int $sede_id ID de la sede
     * @param string $nombre_cargo Nombre del cargo
     * @param string|null $descripcion Descripción del cargo
     * @param float|null $salario_base Salario base (opcional)
     * @param int $estado Estado del cargo (1=Activo, 0=Inactivo)
     * @param string $modificado_por Usuario que modifica el registro
     * @return array Respuesta de la operación
     */
    public function actualizar_cargo($id, $sede_id, $nombre_cargo, $descripcion, $salario_base, $estado, $modificado_por)
    {
        try {
            $conectar = parent::Conexion();
            $sql = "
                DECLARE @mensaje NVARCHAR(255);
                EXEC update_cargo 
                    @id = ?, 
                    @sede_id = ?, 
                    @nombre_cargo = ?, 
                    @descripcion = ?, 
                    @salario_base = ?, 
                    @estado = ?, 
                    @modificado_por = ?, 
                    @mensaje = @mensaje OUTPUT;
                SELECT @mensaje AS mensaje;
            ";
            
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, $nombre_cargo, PDO::PARAM_STR);
            $query->bindValue(4, $descripcion, PDO::PARAM_STR);
            $query->bindValue(5, $salario_base, PDO::PARAM_STR);
            $query->bindValue(6, $estado, PDO::PARAM_INT);
            $query->bindValue(7, $modificado_por, PDO::PARAM_STR);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            $mensaje = $result['mensaje'] ?? 'Error desconocido';

            // Determinar si fue exitoso
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en actualizar_cargo: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al actualizar el cargo',
                'detalle' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar lógicamente un cargo
     * @param int $id ID del cargo
     * @param int $sede_id ID de la sede
     * @param string $modificado_por Usuario que elimina el registro
     * @return array Respuesta de la operación
     */
    public function eliminar_cargo($id, $sede_id, $modificado_por)
    {
        try {
            $conectar = parent::Conexion();
            $sql = "
                DECLARE @mensaje NVARCHAR(255);
                EXEC delete_cargo 
                    @id = ?, 
                    @sede_id = ?, 
                    @modificado_por = ?, 
                    @mensaje = @mensaje OUTPUT;
                SELECT @mensaje AS mensaje;
            ";
            
            $query = $conectar->prepare($sql);
            $query->bindValue(1, $id, PDO::PARAM_INT);
            $query->bindValue(2, $sede_id, PDO::PARAM_INT);
            $query->bindValue(3, $modificado_por, PDO::PARAM_STR);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            $mensaje = $result['mensaje'] ?? 'Error desconocido';

            // Determinar si fue exitoso
            $ok = stripos($mensaje, 'correctamente') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en eliminar_cargo: " . $e->getMessage());
            return [
                'ok' => false, 
                'msj' => 'Error al eliminar el cargo',
                'detalle' => $e->getMessage()
            ];
        }
    }
}
