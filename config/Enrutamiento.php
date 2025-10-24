<?php
//config/Enrutamiento.php

class Enrutamiento
{  
    private $ambiente = 'dev'; // dev, testing o prod
 
    public static function dominio()
    {
        $instance = new self();
        
        if ($instance->ambiente == 'dev') {
            return "http://localhost/sistema_rifas";
        } 
        elseif ($instance->ambiente == 'testing') {
            return "https://sistema_rifas/testing";
        } 
        else {
            return "https://control.cafedcallao.gob.pe"; //prod
        }
    }
 
}
?>