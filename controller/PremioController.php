<?php
/**
 * Controlador de Premios
 */
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Premio.php');
require_once(__DIR__ . '/../helpers/Validator.php');

class PremioController
{
    private $premio;
    private $uploadDir;
    private $uploadPublicPath;

    public function __construct()
    {
        $this->premio = new Premio();
        $this->uploadDir = __DIR__ . '/../uploads/premios';
        $this->uploadPublicPath = 'uploads/premios';
    }

    public function handleRequest(string $action): void
    {
        header('Content-Type: application/json; charset=utf-8');

        switch ($action) {
            case 'getAll':
                $this->listar_premios();
                break;
            case 'getById':
                $this->obtener_premio_por_id();
                break;
            case 'destacados':
                $this->listar_premios_destacados();
                break;
            case 'register':
                $this->registrar_premio();
                break;
            case 'update':
                $this->actualizar_premio();
                break;
            case 'delete':
                $this->eliminar_premio();
                break;
            default:
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Acción no válida']);
                break;
        }
    }

    private function listar_premios(): void
    {
        try {
            if (!isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'El parámetro sede_id es obligatorio']);
                return;
            }

            $sede_id = (int) $_GET['sede_id'];
            $estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? (int) $_GET['estado'] : null;

            $resultado = $this->premio->listar_premios($sede_id, $estado);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en listar_premios: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los premios']);
        }
    }

    private function obtener_premio_por_id(): void
    {
        try {
            if (!isset($_GET['id']) || !isset($_GET['sede_id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'msj' => 'Los parámetros id y sede_id son obligatorios']);
                return;
            }

            $id = (int) $_GET['id'];
            $sede_id = (int) $_GET['sede_id'];

            $resultado = $this->premio->obtener_premio_por_id($id, $sede_id);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en obtener_premio_por_id: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener el premio']);
        }
    }

    /**
     * Listar premios destacados (público, no requiere autenticación)
     */
    private function listar_premios_destacados(): void
    {
        try {
            // sede_id es opcional, por defecto 1
            $sede_id = isset($_GET['sede_id']) ? (int) $_GET['sede_id'] : 1;
            $limite = isset($_GET['limite']) ? (int) $_GET['limite'] : 6;

            $resultado = $this->premio->listar_premios_destacados($sede_id, $limite);

            http_response_code($resultado['ok'] ? 200 : 404);
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error en listar_premios_destacados: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al obtener los premios destacados']);
        }
    }

    private function registrar_premio(): void
    {
        try {
            $input = $this->obtenerDatosRequest();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $validation = Validator::validarCamposRequeridos($input, [
            'sede_id', 'nombre', 'creado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation);
            return;
        }

        $sedeId = (int) $input['sede_id'];
        $categoriaId = $this->parseNullableInt($input['categoria_id'] ?? null);
        $valorEstimado = $this->parseNullableFloat($input['valor_estimado'] ?? null);
        $galeriaActual = $this->decodeGaleriaActual($input['galeria_imagenes_actual'] ?? '[]');

        try {
            $imagenPrincipal = $this->procesarImagen('imagen_principal', $this->nullIfEmpty($input['imagen_principal'] ?? null));
            $imagenSecundaria = $this->procesarImagen('imagen_secundaria', $this->nullIfEmpty($input['imagen_secundaria'] ?? null));
            $galeria = $this->procesarGaleria($galeriaActual);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $galeriaJson = !empty($galeria) ? json_encode($galeria) : null;

        $resultado = $this->premio->registrar_premio(
            $sedeId,
            $categoriaId,
            $this->nullIfEmpty($input['codigo'] ?? null),
            trim($input['nombre']),
            $this->nullIfEmpty($input['descripcion'] ?? null),
            $valorEstimado,
            $imagenPrincipal,
            $imagenSecundaria,
            $galeriaJson,
            $this->nullIfEmpty($input['video_url'] ?? null),
            $this->nullIfEmpty($input['marca'] ?? null),
            $this->nullIfEmpty($input['modelo'] ?? null),
            $this->nullIfEmpty($input['color'] ?? null),
            $this->nullIfEmpty($input['especificaciones'] ?? null),
            $this->nullIfEmpty($input['terminos_condiciones'] ?? null),
            $this->nullIfEmpty($input['restricciones'] ?? null),
            $this->parseNullableInt($input['es_destacado'] ?? null),
            $this->parseNullableInt($input['orden_visualizacion'] ?? null),
            trim($input['creado_por'])
        );

        http_response_code($resultado['ok'] ? 201 : 400);
        echo json_encode($resultado);
    }

    private function actualizar_premio(): void
    {
        try {
            $input = $this->obtenerDatosRequest();
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $validation = Validator::validarCamposRequeridos($input, [
            'id', 'sede_id', 'codigo', 'nombre', 'estado', 'modificado_por'
        ]);

        if (!$validation['ok']) {
            http_response_code(400);
            echo json_encode($validation);
            return;
        }

        $id = (int) $input['id'];
        $sedeId = (int) $input['sede_id'];
        $categoriaId = $this->parseNullableInt($input['categoria_id'] ?? null);
        $valorEstimado = $this->parseNullableFloat($input['valor_estimado'] ?? null);
        $estado = $this->parseNullableInt($input['estado'] ?? null);

        $imagenPrincipalActual = $this->nullIfEmpty($input['imagen_principal_actual'] ?? ($input['imagen_principal'] ?? null));
        $imagenSecundariaActual = $this->nullIfEmpty($input['imagen_secundaria_actual'] ?? ($input['imagen_secundaria'] ?? null));
        $galeriaActual = $this->decodeGaleriaActual($input['galeria_imagenes_actual'] ?? '[]');

        try {
            $imagenPrincipal = $this->procesarImagen('imagen_principal', $imagenPrincipalActual);
            $imagenSecundaria = $this->procesarImagen('imagen_secundaria', $imagenSecundariaActual);
            $galeria = $this->procesarGaleria($galeriaActual);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msj' => $e->getMessage()]);
            return;
        }

        $galeriaJson = !empty($galeria) ? json_encode($galeria) : null;

        $resultado = $this->premio->actualizar_premio(
            $id,
            $sedeId,
            $categoriaId,
            trim($input['codigo']),
            trim($input['nombre']),
            $this->nullIfEmpty($input['descripcion'] ?? null),
            $valorEstimado,
            $imagenPrincipal,
            $imagenSecundaria,
            $galeriaJson,
            $this->nullIfEmpty($input['video_url'] ?? null),
            $this->nullIfEmpty($input['marca'] ?? null),
            $this->nullIfEmpty($input['modelo'] ?? null),
            $this->nullIfEmpty($input['color'] ?? null),
            $this->nullIfEmpty($input['especificaciones'] ?? null),
            $this->nullIfEmpty($input['terminos_condiciones'] ?? null),
            $this->nullIfEmpty($input['restricciones'] ?? null),
            $this->parseNullableInt($input['es_destacado'] ?? null),
            $this->parseNullableInt($input['orden_visualizacion'] ?? null),
            $estado,
            trim($input['modificado_por'])
        );

        http_response_code($resultado['ok'] ? 200 : 400);
        echo json_encode($resultado);
    }

    private function eliminar_premio(): void
    {
        try {
            $input = $this->obtenerDatosRequest();

            $validation = Validator::validarCamposRequeridos($input, [
                'id', 'sede_id', 'modificado_por'
            ]);

            if (!$validation['ok']) {
                http_response_code(400);
                echo json_encode($validation);
                return;
            }

            $resultado = $this->premio->eliminar_premio(
                (int) $input['id'],
                (int) $input['sede_id'],
                trim($input['modificado_por'])
            );

            http_response_code($resultado['ok'] ? 200 : 400);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en eliminar_premio: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'msj' => 'Error al eliminar el premio']);
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

    private function sanitizeDate($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        return $value;
    }

    private function nullIfEmpty($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }
        return $value;
    }

    private function parseNullableInt($value): ?int
    {
        $value = $this->nullIfEmpty($value);
        return $value !== null ? (int) $value : null;
    }

    private function parseNullableFloat($value): ?float
    {
        $value = $this->nullIfEmpty($value);
        return $value !== null ? (float) $value : null;
    }

    private function decodeGaleriaActual($value): array
    {
        if (!$value) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function procesarImagen(string $key, ?string $actual = null): ?string
    {
        if (!isset($_FILES[$key])) {
            return $actual;
        }

        $file = $_FILES[$key];
        if (is_array($file['name'])) {
            $file = [
                'name' => $file['name'][0] ?? '',
                'type' => $file['type'][0] ?? '',
                'tmp_name' => $file['tmp_name'][0] ?? '',
                'error' => $file['error'][0] ?? UPLOAD_ERR_NO_FILE,
                'size' => $file['size'][0] ?? 0,
            ];
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE || empty($file['name'])) {
            return $actual;
        }

        $path = $this->saveUploadedFile($file);
        return $path ?? $actual;
    }

    private function procesarGaleria(array $existentes = []): array
    {
        if (!isset($_FILES['galeria_imagenes'])) {
            return $existentes;
        }

        $files = $_FILES['galeria_imagenes'];
        $nuevas = $this->guardarArchivosMultiples($files);

        if (empty($nuevas)) {
            return $existentes;
        }

        return array_merge($existentes, $nuevas);
    }

    private function guardarArchivosMultiples(array $files): array
    {
        $paths = [];

        if (is_array($files['name'])) {
            foreach ($files['name'] as $index => $name) {
                if (empty($name)) {
                    continue;
                }

                $file = [
                    'name' => $name,
                    'type' => $files['type'][$index],
                    'tmp_name' => $files['tmp_name'][$index],
                    'error' => $files['error'][$index],
                    'size' => $files['size'][$index]
                ];

                if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                $path = $this->saveUploadedFile($file);
                if ($path) {
                    $paths[] = $path;
                }
            }
        } else {
            if (($files['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE && !empty($files['name'])) {
                $path = $this->saveUploadedFile($files);
                if ($path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    private function saveUploadedFile(array $file): ?string
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo.');
        }

        if (($file['size'] ?? 0) <= 0) {
            throw new Exception('El archivo está vacío.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new Exception('Archivo subido no válido.');
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed, true)) {
            throw new Exception('Formato de imagen no permitido. Utiliza JPG, PNG o WEBP.');
        }

        $maxSize = 5 * 1024 * 1024; // 5 MB
        if (($file['size'] ?? 0) > $maxSize) {
            throw new Exception('El archivo excede el tamaño máximo permitido (5MB).');
        }

        if (!is_dir($this->uploadDir)) {
            if (!mkdir($concurrentDirectory = $this->uploadDir, 0777, true) && !is_dir($concurrentDirectory)) {
                throw new Exception('No se pudo crear el directorio de carga.');
            }
        }

        $filename = uniqid('premio_', true) . '.' . $extension;
        $destination = rtrim($this->uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('No se pudo guardar el archivo subido.');
        }

        return $this->uploadPublicPath . '/' . $filename;
    }
}


