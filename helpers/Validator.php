<?php
/**
 * Validator - Helper  para validaciones comunes
 */
class Validator
{
    /**
     * Validar que campos obligatorios no estén vacíos
     * @param array $data Datos a validar
     * @param array $required Campos requeridos
     * @return array ['ok' => bool, 'msj' => string, 'campo' => string]
     */
    public static function validarCamposRequeridos($data, $required)
    {
        foreach ($required as $campo) {
            if (!isset($data[$campo]) || (is_string($data[$campo]) && trim($data[$campo]) === '')) {
                return [
                    'ok' => false,
                    'msj' => "El campo '$campo' es obligatorio",
                    'campo' => $campo

                ];
            }
        }
        
        return ['ok' => true];
    }
}


