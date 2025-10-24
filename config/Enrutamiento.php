<?php
//config/Enrutamiento.php

class Enrutamiento
{  
    private $ambiente = 'dev'; // dev, testing o prod
 
    public static function dominio()
    {
        $instance = new self();
        
        if ($instance->ambiente == 'dev') {
            return "http://localhost/CONTROL_ASISTENCIA_CAFED";
        } 
        elseif ($instance->ambiente == 'testing') {
            return "https://CONTROL_ASISTENCIA_CAFED/testing";
        } 
        else {
            return "https://control.cafedcallao.gob.pe"; //prod
        }
    }
 
}
?>