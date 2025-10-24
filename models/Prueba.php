<?php
class Prueba extends Conectar
{
    public function listar_prueba()
    {
        try {
            $conectar = parent::Conexion();
            $sql = "sp_prueba_listar";
            $query = $conectar->prepare($sql);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            return [
                'ok' => true,
                'msj' => !empty($data) ? 'data obtenida' : 'No existe data',
                'prueba' => $data ?: null
            ];
        } catch (PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
            return ["error" => "Ocurrió un error al ejecutar consulta"];
        }
    }

}
