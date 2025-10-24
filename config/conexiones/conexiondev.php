<?php
session_start();

$dev = true; //  false si es producción, true si es desarrollo

if ($dev) {
    $server = '198.72.127.152';
    $dbname = 'bd_instituciones_testing';
    $username = 'cafed2025';
    $password = 'yUnpnXb2Aj3~6?mr';
} else {
    $server = '198.72.127.152';
    $dbname = 'cafedc4lla0_BD_Instituciones';
    $username = 'cafed2025';
    $password = 'yUnpnXb2Aj3~6?mr';
}

class Conectar
{
    protected $dbh;

    public function Conexion()
    {
        global $server, $dbname, $username, $password;

        try {
            $this->dbh = new PDO("sqlsrv:Server=$server;Database=$dbname", $username, $password);
            return $this->dbh;
        } catch (Exception $e) {
            print "Error Conexión BD: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public static function obtenerBaseUrl()
    {
        global $dev;
        if ($dev) {
            return "/CONTROL_ASISTENCIA_CAFED/";
        } else {
            return "/CONTROL_ASISTENCIA_CAFED/";
        }
    }
}

 
?>
