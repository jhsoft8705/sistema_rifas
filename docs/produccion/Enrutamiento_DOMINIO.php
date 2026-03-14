<?php
/**
 * CONFIGURACIÓN PRODUCCIÓN - DOMINIO
 * 
 * Destino: jhsoftperu.com
 * Ruta: /home/u696088465/domains/jhsoftperu.com/public_html
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
            return "https://jhsoftperu.com"; // PRODUCCIÓN dominio
        }
    }
}
?>
