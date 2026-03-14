<?php
/**
 * CONFIGURACIÓN PRODUCCIÓN - SUBDOMINIO
 * 
 * Destino: ganadoresya.jhsoftperu.com
 * Ruta en Hostinger: /home/u696088465/domains/jhsoftperu.com/public_html/ganadoresya
 * 
 * INSTRUCCIONES: Copiar este archivo a config/conexion.php reemplazando el original
 */
session_start();

class Conectar
{
    protected $dbh;
    private $ambiente = 'prod'; // PRODUCCIÓN

    protected function Conexion()
    {
        try {
            if ($this->ambiente == 'dev') {
                $conectar = $this->dbh = new PDO("mysql:host=88.223.84.166;dbname=u696088465_bd_sorteos", "u696088465_user_sorteos", "@2Zq0s>q8P");
                $conectar->exec("SET time_zone = '-05:00';");
            } elseif ($this->ambiente == 'testing') {
                $conectar = $this->dbh = new PDO("mysql:host=localhost;dbname=db_rifas", "root", "");
                $conectar->exec("SET time_zone = '-05:00';");
            } else {
                // PRODUCCIÓN - Hostinger (ganadoresya.jhsoftperu.com)
                $conectar = $this->dbh = new PDO("mysql:host=88.223.84.166;dbname=u696088465_bd_prd_sorteos","u696088465_user_prod","^O6xr0>TI");

                $conectar->exec("SET time_zone = '-05:00';");
            }
            return $conectar;
        } catch (Exception $e) {
            error_log("Error Conexion BD: " . $e->getMessage());
            throw $e;
        }
    }

    public static function obtenerBaseUrl()
    {
        $instance = new self();
        if ($instance->ambiente == 'dev') {
            return "/sistema_rifas/";
        } elseif ($instance->ambiente == 'testing') {
            return "/testing/";
        } else {
            return "/"; // Subdominio: raíz de ganadoresya.jhsoftperu.com
        }
    }
}
?>
