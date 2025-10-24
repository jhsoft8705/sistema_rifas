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
                $conectar = $this->dbh = new PDO(
                    "sqlsrv:server=198.72.127.152;Database=db_control_asistencia_testing",
                    "cafedasistencia2025", 
                    "cafed2025O"
                );
            } 
            elseif ($this->ambiente == 'testing') {
                // TESTING
                  $conectar = $this->dbh = new PDO(
                    "sqlsrv:server=198.72.127.152;Database=db_control_asistencia_testing",
                    "cafedasistencia2025", 
                    "cafed2025O"
                );
            } 
            else {
                // PRODUCCION
                $conectar = $this->dbh = new PDO(
                    "sqlsrv:server=198.72.127.152;Database=db_control_asistencia_prod",
                    "cafedasistencia2025", 
                    "cafed2025O"
                );
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
            return "/CONTROL_ASISTENCIA_CAFED/";
        } 
        elseif ($instance->ambiente == 'testing') {
            return "/testing/";
        } 
        else {
            return "/asistencia.cafedcallao.gob.pe/"; // prod
        }
    }
   
 
 
}
?>