<?php
/**
 * Controlador de Rifas
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Rifa.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class RifaController
{
    private $rifa;

    public function __construct()
    {
        $this->rifa = new Rifa();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'getAll':
                $this->listar_rifas();
                break;
            case 'getById':
                $this->obtener_rifa_por_id();
                break;
            case 'register':
                $this->registrar_rifa();
                break;
            case 'update':
                $this->actualizar_rifa();
                break;
            case 'delete':
                $this->eliminar_rifa();
                break;
            case 'getPremios':
                $this->listar_premios_rifa();
                break;
            case 'addPremio':
                $this->agregar_premio_rifa();
                break;
            case 'updatePremio':
                $this->actualizar_premio_rifa();
                break;
            case 'deletePremio':
                $this->eliminar_premio_rifa();
                break;
            case 'getNumeros':
                $this->listar_numeros_rifa();
                break;
            case 'updateNumero':
                $this->actualizar_numero_rifa();
                break;
            case 'getPublicas':
                $this->listar_rifas_publicas();
                break;
            case 'generarNumeros':
                $this->generar_numeros_rifa();
                break;
            case 'getNumerosDisponibles':
                $this->obtener_numeros_disponibles();
                break;
            case 'reservarNumeros':
                $this->reservar_numeros();
                break;
            case 'asignarNumeroAleatorio':
                $this->asignar_numero_aleatorio();
                break;
            case 'liberarNumeros':
                $this->liberar_numeros_reservados();
                break;
            case 'cerrar':
                $this->cerrar_rifa();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    private function listar_rifas(): void
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio']);
                return;
            }
            $sede_id = (int) $_GET['sede_id'];
            $estado = isset($_GET['estado']) ? trim($_GET['estado']) : null;
            $resultado = $this->rifa->listar_rifas($sede_id, $estado);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar_rifas: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener las rifas']);
        }
    }

    private function obtener_rifa_por_id(): void
    {
        try {
            if (!isset($_GET['id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros id y sede_id son obligatorios']);
                return;
            }

            $id = (int) $_GET['id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->rifa->obtener_rifa_por_id($id, $sede_id);

            if ($resultado['ok'] && isset($resultado['data'])) {
                $premios = $this->rifa->listar_rifa_premios($id);
                if ($premios['ok']) {
                    $resultado['data']['premios'] = $premios['data'];
                } else {
                    $resultado['data']['premios'] = [];
                    $resultado['alertas']['premios'] = $premios['msj'];
                }
            }

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en obtener_rifa_por_id: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener la rifa']);
        }
    }

    private function registrar_rifa(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'sede_id',
                'nombre',
                'precio_ticket',
                'numero_inicial',
                'numero_final',
                'fecha_inicio_venta',
                'fecha_fin_venta',
                'fecha_sorteo',
                'creado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $data = $this->buildRifaDataFromInput($input, false);
            $resultado = $this->rifa->registrar_rifa($data);

            if ($resultado['ok'] && !empty($resultado['rifa_id'])) {
                $this->procesarPremiosIniciales($resultado['rifa_id'], $data['sede_id'], $input);
            }

            http_response_code($resultado['ok'] ? 201 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en registrar_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al registrar la rifa']);
        }
    }

    private function actualizar_rifa(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'id',
                'sede_id',
                'nombre',
                'precio_ticket',
                'numero_inicial',
                'numero_final',
                'fecha_inicio_venta',
                'fecha_fin_venta',
                'fecha_sorteo',
                'estado',
                'estado_activo',
                'modificado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            // Obtener código de la rifa existente si no se envía
            if (!isset($input['codigo']) || trim($input['codigo']) === '') {
                $rifaExistente = $this->rifa->obtener_rifa_por_id((int) $input['id'], (int) $input['sede_id']);
                if ($rifaExistente['ok'] && isset($rifaExistente['data']['codigo'])) {
                    $input['codigo'] = $rifaExistente['data']['codigo'];
                }
            }

            $data = $this->buildRifaDataFromInput($input, true);
            $resultado = $this->rifa->actualizar_rifa($data);

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en actualizar_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al actualizar la rifa']);
        }
    }

    private function eliminar_rifa(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'id', 'sede_id', 'modificado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $resultado = $this->rifa->eliminar_rifa(
                (int) $input['id'],
                (int) $input['sede_id'],
                trim($input['modificado_por'])
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en eliminar_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al eliminar la rifa']);
        }
    }

    /**
     * Construir arreglo con datos de rifa desde input
     */
    private function buildRifaDataFromInput(array $input, bool $isUpdate): array
    {
        $data = [
            'sede_id' => (int) $input['sede_id'],
            'premio_id' => isset($input['premio_id']) && $input['premio_id'] !== '' ? (int) $input['premio_id'] : null,
            'ubicacion_id' => isset($input['ubicacion_id']) ? (int) $input['ubicacion_id'] : null,
            'codigo' => isset($input['codigo']) && trim($input['codigo']) !== '' ? trim($input['codigo']) : null,
            'nombre' => trim($input['nombre']),
            'descripcion' => $input['descripcion'] ?? null,
            'numero_intentos' => isset($input['numero_intentos']) ? (int) $input['numero_intentos'] : null,
            'intento_ganador' => isset($input['intento_ganador']) ? (int) $input['intento_ganador'] : null,
            'precio_ticket' => (float) $input['precio_ticket'],
            'cantidad_maxima_tickets' => isset($input['cantidad_maxima_tickets']) ? (int) $input['cantidad_maxima_tickets'] : null,
            'cantidad_maxima_por_persona' => isset($input['cantidad_maxima_por_persona']) ? (int) $input['cantidad_maxima_por_persona'] : null,
            'usa_numeracion_boletos' => isset($input['usa_numeracion_boletos']) ? (int) $input['usa_numeracion_boletos'] : null,
            'tipo_numeracion' => $input['tipo_numeracion'] ?? null,
            'numero_inicial' => (int) $input['numero_inicial'],
            'numero_final' => (int) $input['numero_final'],
            'cantidad_digitos' => isset($input['cantidad_digitos']) ? (int) $input['cantidad_digitos'] : null,
            'prefijo_numero' => $input['prefijo_numero'] ?? null,
            'sufijo_numero' => $input['sufijo_numero'] ?? null,
            'permitir_seleccion_numero' => isset($input['permitir_seleccion_numero']) ? (int) $input['permitir_seleccion_numero'] : null,
            'asignacion_automatica' => isset($input['asignacion_automatica']) ? (int) $input['asignacion_automatica'] : null,
            'mostrar_numeros_disponibles' => isset($input['mostrar_numeros_disponibles']) ? (int) $input['mostrar_numeros_disponibles'] : null,
            'generar_volantarios' => isset($input['generar_volantarios']) ? (int) $input['generar_volantarios'] : null,
            'numeros_por_volantario' => isset($input['numeros_por_volantario']) ? (int) $input['numeros_por_volantario'] : null,
            'formato_impresion' => $input['formato_impresion'] ?? null,
            'numeros_por_pagina' => isset($input['numeros_por_pagina']) ? (int) $input['numeros_por_pagina'] : null,
            'numeros_bloqueados' => $input['numeros_bloqueados'] ?? null,
            'numeros_especiales' => $input['numeros_especiales'] ?? null,
            'fecha_inicio_venta' => $input['fecha_inicio_venta'],
            'fecha_fin_venta' => $input['fecha_fin_venta'],
            'fecha_sorteo' => $input['fecha_sorteo'],
            'mostrar_contador' => isset($input['mostrar_contador']) ? (int) $input['mostrar_contador'] : null,
            'mostrar_participantes' => isset($input['mostrar_participantes']) ? (int) $input['mostrar_participantes'] : null,
            'mostrar_tickets_vendidos' => isset($input['mostrar_tickets_vendidos']) ? (int) $input['mostrar_tickets_vendidos'] : null,
            'tipo_publicidad' => $input['tipo_publicidad'] ?? null,
            'url_banner' => $input['url_banner'] ?? null,
            'texto_promocional' => $input['texto_promocional'] ?? null,
            'reglas_participacion' => $input['reglas_participacion'] ?? null,
            'terminos_condiciones' => $input['terminos_condiciones'] ?? null,
        ];

        if ($isUpdate) {
            $data['id'] = (int) $input['id'];
            $data['codigo'] = isset($input['codigo']) && trim($input['codigo']) !== '' ? trim($input['codigo']) : null;
            $data['fecha_sorteo_realizado'] = $input['fecha_sorteo_realizado'] ?? null;
            $data['estado'] = trim($input['estado']);
            $data['estado_activo'] = isset($input['estado_activo']) ? (int) $input['estado_activo'] : null;
            $data['regenerar_numeros'] = isset($input['regenerar_numeros']) ? (int) $input['regenerar_numeros'] : 0;
            $data['modificado_por'] = trim($input['modificado_por']);
        } else {
            $data['estado'] = isset($input['estado']) && trim($input['estado']) !== '' ? trim($input['estado']) : 'BORRADOR';
            $data['creado_por'] = trim($input['creado_por']);
            $data['regenerar_numeros'] = 0;
        }

        return $data;
    }

    private function procesarPremiosIniciales(int $rifa_id, int $sede_id, array $input): void
    {
        if (empty($input['premios']) || !is_array($input['premios'])) {
            return;
        }

        foreach ($input['premios'] as $premio) {
            if (!isset($premio['premio_id'])) {
                continue;
            }

            $payload = [
                'rifa_id' => $rifa_id,
                'sede_id' => $sede_id,
                'premio_id' => (int) $premio['premio_id'],
                'orden' => isset($premio['orden']) ? (int) $premio['orden'] : null,
                'es_principal' => isset($premio['es_principal']) ? (int) $premio['es_principal'] : null,
                'titulo' => $premio['titulo'] ?? null,
                'descripcion' => $premio['descripcion'] ?? null,
                'cantidad' => isset($premio['cantidad']) ? (int) $premio['cantidad'] : null,
                'valor_estimado' => isset($premio['valor_estimado']) ? (float) $premio['valor_estimado'] : null,
                'creado_por' => isset($input['creado_por']) ? trim($input['creado_por']) : 'SYSTEM'
            ];

            $this->rifa->registrar_rifa_premio($payload);
        }
    }

    private function listar_premios_rifa(): void
    {
        try {
            if (!isset($_GET['rifa_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro rifa_id es obligatorio']);
                return;
            }

            $rifa_id = (int) $_GET['rifa_id'];
            $resultado = $this->rifa->listar_rifa_premios($rifa_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar_premios_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los premios de la rifa']);
        }
    }

    private function agregar_premio_rifa(): void
    {
        $this->handlePremioRequest(function (array $data) {
            return $this->rifa->registrar_rifa_premio($data);
        }, ['rifa_id', 'sede_id', 'premio_id', 'creado_por']);
    }

    private function actualizar_premio_rifa(): void
    {
        $this->handlePremioRequest(function (array $data) {
            return $this->rifa->actualizar_rifa_premio($data);
        }, ['id', 'rifa_id', 'sede_id', 'modificado_por']);
    }

    private function eliminar_premio_rifa(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'id', 'rifa_id', 'sede_id', 'modificado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $resultado = $this->rifa->eliminar_rifa_premio(
                (int) $input['id'],
                (int) $input['rifa_id'],
                (int) $input['sede_id'],
                trim($input['modificado_por'])
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en eliminar_premio_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al eliminar el premio de la rifa']);
        }
    }

    private function handlePremioRequest(callable $callback, array $requiredFields): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, $requiredFields);
            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $resultado = $callback($input);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en manejo de premios de rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error en la operación de premios de la rifa']);
        }
    }

    private function listar_numeros_rifa(): void
    {
        try {
            if (!isset($_GET['rifa_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro rifa_id es obligatorio']);
                return;
            }

            $rifa_id = (int) $_GET['rifa_id'];
            $estado = isset($_GET['estado']) ? trim($_GET['estado']) : null;
            $resultado = $this->rifa->listar_numeros_rifa($rifa_id, $estado);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar_numeros_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los números de la rifa']);
        }
    }

    private function actualizar_numero_rifa(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'numero_id', 'rifa_id', 'estado', 'modificado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $resultado = $this->rifa->actualizar_estado_numero($input);
            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en actualizar_numero_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al actualizar el número de la rifa']);
        }
    }

    private function listar_rifas_publicas(): void
    {
        try {
            $sede_id = isset($_GET['sede_id']) ? (int) $_GET['sede_id'] : null;
            $resultado = $this->rifa->listar_rifas_publicas($sede_id);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar_rifas_publicas: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener las rifas públicas']);
        }
    }

    private function generar_numeros_rifa(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode([
                    'ok' => false, 
                    'msj' => 'JSON inválido: ' . json_last_error_msg(),
                    'input_raw' => file_get_contents("php://input")
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            // Log para depuración
            error_log("Generar números - Input recibido: " . json_encode($input));

            $validation = Validator::validarCamposRequeridos($input, [
                'rifa_id',
                'sede_id',
                'creado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode([
                    'ok' => false,
                    'msj' => $validation['msj'] ?? 'Campos requeridos faltantes',
                    'campos_faltantes' => $validation['campos_faltantes'] ?? [],
                    'input_recibido' => $input
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $this->rifa->generar_numeros_rifa(
                (int) $input['rifa_id'],
                (int) $input['sede_id'],
                trim($input['creado_por'])
            );

            // Log del resultado
            error_log("Generar números - Resultado: " . json_encode($resultado));

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en generar_numeros_rifa: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode([
                'ok' => false, 
                'msj' => 'Error al generar los números de la rifa: ' . $e->getMessage(),
                'detalle' => $e->getTraceAsString()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Obtener números disponibles de una rifa
     */
    private function obtener_numeros_disponibles(): void
    {
        try {
            if (!isset($_GET['rifa_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro rifa_id es obligatorio']);
                return;
            }

            $rifa_id = (int) $_GET['rifa_id'];
            $limite = isset($_GET['limite']) ? (int) $_GET['limite'] : null;
            $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : null;

            $resultado = $this->rifa->obtener_numeros_disponibles($rifa_id, $limite, $busqueda);
            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en obtener_numeros_disponibles: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los números disponibles']);
        }
    }

    /**
     * Reservar números específicos
     */
    private function reservar_numeros(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'rifa_id',
                'numeros'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            if (!is_array($input['numeros']) || empty($input['numeros'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Debe proporcionar al menos un número']);
                return;
            }

            $sesion_id = $input['sesion_id'] ?? session_id() . '_' . time();
            $resultado = $this->rifa->reservar_numeros(
                (int) $input['rifa_id'],
                $input['numeros'],
                $sesion_id
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en reservar_numeros: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al reservar los números']);
        }
    }

    /**
     * Asignar número aleatorio
     */
    private function asignar_numero_aleatorio(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'rifa_id',
                'cantidad'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $sesion_id = $input['sesion_id'] ?? session_id() . '_' . time();
            $cantidad = isset($input['cantidad']) ? (int) $input['cantidad'] : 1;

            $resultado = $this->rifa->asignar_numeros_aleatorios(
                (int) $input['rifa_id'],
                $cantidad,
                $sesion_id
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en asignar_numero_aleatorio: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al asignar número aleatorio']);
        }
    }

    /**
     * Liberar números reservados por sesión
     */
    private function liberar_numeros_reservados(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'JSON inválido']);
                return;
            }

            $validation = Validator::validarCamposRequeridos($input, [
                'rifa_id',
                'sesion_id'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $resultado = $this->rifa->liberar_numeros_reservados(
                (int) $input['rifa_id'],
                trim($input['sesion_id'])
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en liberar_numeros_reservados: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al liberar los números reservados']);
        }
    }

    /**
     * Cerrar rifa
     */
    private function cerrar_rifa(): void
    {
        try {
            $input = $this->obtenerDatosRequest();

            $validation = Validator::validarCamposRequeridos($input, [
                'id', 'sede_id', 'modificado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation, JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $this->rifa->cerrar_rifa(
                (int) $input['id'],
                (int) $input['sede_id'],
                trim($input['modificado_por'])
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en cerrar_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al cerrar la rifa'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function obtenerDatosRequest(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON inválido');
            }
            return $this->sanitizeArray($input ?? []);
        }
        return $this->sanitizeArray($_POST ?? []);
    }

    private function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }
        return $data;
    }
}


