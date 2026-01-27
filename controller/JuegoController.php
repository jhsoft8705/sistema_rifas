<?php
/**
 * Controlador de Juegos
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Juego.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class JuegoController
{
    private $juego;

    public function __construct()
    {
        $this->juego = new Juego();
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'getRifasParaJugar':
                $this->listar_rifas_para_jugar();
                break;
            case 'getPremiosRifa':
                $this->listar_premios_rifa();
                break;
            case 'getParticipantes':
                $this->listar_participantes();
                break;
            case 'jugar':
                $this->jugar_premio_rifa();
                break;
            case 'getInfoJuego':
                $this->obtener_info_juego_premio();
                break;
            case 'registrarGanador':
                $this->registrar_ganador();
                break;
            case 'verificarRifaCompleta':
                $this->verificar_rifa_completa();
                break;
            case 'getNumerosGanador':
                $this->obtener_numeros_ganador();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida'], JSON_UNESCAPED_UNICODE);
                break;
        }
    }

    private function listar_rifas_para_jugar(): void
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $sede_id = (int) $_GET['sede_id'];
            $resultado = $this->juego->listar_rifas_para_jugar($sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en listar_rifas_para_jugar: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener las rifas'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function listar_participantes(): void
    {
        try {
            if (!isset($_GET['rifa_id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros rifa_id y sede_id son obligatorios'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $rifa_id = (int) $_GET['rifa_id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->juego->listar_participantes_rifa($rifa_id, $sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en listar_participantes: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los participantes'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function listar_premios_rifa(): void
    {
        try {
            if (!isset($_GET['rifa_id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros rifa_id y sede_id son obligatorios'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $rifa_id = (int) $_GET['rifa_id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->juego->listar_premios_rifa_para_jugar($rifa_id, $sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en listar_premios_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los premios'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function jugar_premio_rifa(): void
    {
        try {
            $input = $this->obtenerDatosRequest();

            $validation = Validator::validarCamposRequeridos($input, [
                'rifa_id', 'rifa_premio_id', 'sede_id', 'jugado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation, JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $this->juego->jugar_premio_rifa(
                (int) $input['rifa_id'],
                (int) $input['rifa_premio_id'],
                (int) $input['sede_id'],
                trim($input['jugado_por'])
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en jugar_premio_rifa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al ejecutar el juego'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function obtener_info_juego_premio(): void
    {
        try {
            if (!isset($_GET['rifa_id']) || !isset($_GET['rifa_premio_id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros rifa_id, rifa_premio_id y sede_id son obligatorios'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $rifa_id = (int) $_GET['rifa_id'];
            $rifa_premio_id = (int) $_GET['rifa_premio_id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->juego->obtener_info_juego_premio($rifa_id, $rifa_premio_id, $sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en obtener_info_juego_premio: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener la información del juego'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function registrar_ganador(): void
    {
        try {
            $input = $this->obtenerDatosRequest();

            $validation = Validator::validarCamposRequeridos($input, [
                'sede_id', 'rifa_id', 'rifa_premio_id', 'premio_id', 'persona_id', 'intento_ganador', 'jugado_por', 'creado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation, JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $this->juego->registrar_ganador(
                (int) $input['sede_id'],
                (int) $input['rifa_id'],
                (int) $input['rifa_premio_id'],
                (int) $input['premio_id'],
                (int) $input['persona_id'],
                isset($input['ticket_id']) ? (int) $input['ticket_id'] : null,
                isset($input['numero_id']) ? (int) $input['numero_id'] : null,
                $this->nullIfEmpty($input['direccion_envio'] ?? null),
                $this->nullIfEmpty($input['ciudad_envio'] ?? null),
                $this->nullIfEmpty($input['pais_envio'] ?? null),
                isset($input['publicar_web']) ? (bool) $input['publicar_web'] : false,
                (int) $input['intento_ganador'],
                trim($input['jugado_por']),
                trim($input['creado_por'])
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en registrar_ganador: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al registrar el ganador'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function verificar_rifa_completa(): void
    {
        try {
            if (!isset($_GET['rifa_id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros rifa_id y sede_id son obligatorios'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $rifa_id = (int) $_GET['rifa_id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->juego->verificar_rifa_completa($rifa_id, $sede_id);

            http_response_code($resultado['ok'] ? 200 : 500);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en verificar_rifa_completa: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al verificar el estado de la rifa'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function obtener_numeros_ganador(): void
    {
        try {
            if (!isset($_GET['ganador_id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros ganador_id y sede_id son obligatorios'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $ganador_id = (int) $_GET['ganador_id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->juego->obtener_numeros_ganador($ganador_id, $sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en obtener_numeros_ganador: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los números ganadores'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function nullIfEmpty($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }
        return $value;
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
