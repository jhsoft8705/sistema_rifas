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
        if (!is_array($data)) {
            return ['ok' => false, 'msj' => 'Datos requeridos', 'campo' => $required[0] ?? null];
        }
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


