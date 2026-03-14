<?php
/**
 * CONFIGURACIÓN PRODUCCIÓN - SUBDOMINIO
 * 
 * Destino: ganadoresya.jhsoftperu.com
 * Ruta: /home/u696088465/domains/jhsoftperu.com/public_html/ganadoresya
 * 
 * INSTRUCCIONES: Copiar este archivo a config/Enrutamiento.php reemplazando el original
 */
class Enrutamiento
{
    private $ambiente = 'prod'; // PRODUCCIÓN

    public static function dominio()
    {
        $instance = new self();
        if ($instance->ambiente == 'dev') {
            return "http://localhost/sistema_rifas";
        } elseif ($instance->ambiente == 'testing') {
            return "https://sistema_rifas/testing";
        } else {
            return "https://ganadoresya.jhsoftperu.com"; // PRODUCCIÓN subdominio
        }
    }
}
?>
