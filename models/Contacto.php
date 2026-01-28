<?php
/**
 * Modelo Contacto
 * Mensajes de contacto desde la landing (Contáctanos)
 */
class Contacto extends Conectar
{
    /**
     * Registrar mensaje de contacto
     */
    public function registrar(array $data): array
    {
        try {
            $conectar = parent::Conexion();
            $sql = "CALL register_contacto(?, ?, ?, ?, ?, ?, ?, @mensaje)";
            $query = $conectar->prepare($sql);

            $this->bindNullable($query, 1, $this->getValue($data, 'sede_id'), PDO::PARAM_INT);
            $query->bindValue(2, $this->getValue($data, 'nombre'), PDO::PARAM_STR);
            $query->bindValue(3, $this->getValue($data, 'email'), PDO::PARAM_STR);
            $this->bindNullable($query, 4, $this->getValue($data, 'telefono'), PDO::PARAM_STR);
            $query->bindValue(5, $this->getValue($data, 'asunto'), PDO::PARAM_STR);
            $query->bindValue(6, $this->getValue($data, 'mensaje'), PDO::PARAM_STR);
            $this->bindNullable($query, 7, $this->getValue($data, 'ip_origen'), PDO::PARAM_STR);

            $query->execute();
            $query->closeCursor();

            $mensajeStmt = $conectar->query("SELECT @mensaje AS mensaje");
            $result = $mensajeStmt->fetch(PDO::FETCH_ASSOC);
            $mensajeStmt->closeCursor();

            $mensaje = $result['mensaje'] ?? 'Error desconocido';
            $ok = stripos($mensaje, 'correctamente') !== false || stripos($mensaje, 'enviado') !== false;

            return [
                'ok' => $ok,
                'msj' => $mensaje
            ];
        } catch (PDOException $e) {
            error_log("Error en Contacto::registrar: " . $e->getMessage());
            return [
                'ok' => false,
                'msj' => 'Error al enviar el mensaje. Intenta de nuevo más tarde.',
                'detalle' => $e->getMessage()
            ];
        }
    }

    private function getValue(array $data, string $key, $default = null)
    {
        return isset($data[$key]) ? $data[$key] : $default;
    }

    private function bindNullable($query, int $param, $value, int $type): void
    {
        if ($value === null || $value === '') {
            $query->bindValue($param, null, PDO::PARAM_NULL);
        } else {
            $query->bindValue($param, $value, $type);
        }
    }
}
