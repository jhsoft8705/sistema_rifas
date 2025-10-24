<?php
//config/conexion.php
session_start();

class Conectar
{
    protected $dbh;

    //Solo cambia esto según tu ambiente:
    // 'dev', 'testing' o 'prod'
    private $ambiente = 'dev'; // <-- CAMBIA

    protected function Conexion()
    {
        try {
            if ($this->ambiente == 'dev') {
                // DESARROLLO
                $conectar = $this->dbh = new PDO("mysql:host=localhost;dbname=db_rifas", "root", "");
                // Establecer zona horaria de Perú
                $conectar->exec("SET time_zone = '-05:00';");
            } elseif ($this->ambiente == 'testing') {
                // TESTING
                $conectar = $this->dbh = new PDO("mysql:host=localhost;dbname=db_rifas", "root", "");
                // Establecer zona horaria de Perú
                $conectar->exec("SET time_zone = '-05:00';");
            } else {
                // PRODUCCION
                $conectar = $this->dbh = new PDO("mysql:host=localhost;dbname=db_rifas", "root", "");
                // Establecer zona horaria de Perú
                $conectar->exec("SET time_zone = '-05:00';");
            }

            return $conectar;
        } catch (Exception $e) {
            print "Error Conexion BD" . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     * Summary of obtenerBaseUrl
     * @return string
     * Ruta importante para el funcionamiento de la API
     */
    public static function obtenerBaseUrl()
    {
        $instance = new self();

        if ($instance->ambiente == 'dev') {
            return "/sistema_rifas/";
        } elseif ($instance->ambiente == 'testing') {
            return "/testing/";
        } else {
            return "/frm.db_rifas.gob.pe/"; // prod
        }
    }
 
}
?>
 