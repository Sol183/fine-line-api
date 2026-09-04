<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function jsonResponse(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function jsonBody(): array
{
    $rawBody = file_get_contents('php://input');
    if ($rawBody === false || trim($rawBody) === '') {
        jsonResponse(400, ['error' => 'El cuerpo JSON es obligatorio.']);
    }

    try {
        $body = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        jsonResponse(400, ['error' => 'El cuerpo contiene JSON inválido.']);
    }

    if (!is_array($body) || array_is_list($body)) {
        jsonResponse(400, ['error' => 'El cuerpo JSON debe ser un objeto.']);
    }

    return $body;
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = '/' . trim(rawurldecode($path), '/');

if ($path === '/health') {
    if ($method !== 'GET') {
        header('Allow: GET');
        jsonResponse(405, ['error' => 'Método no permitido.']);
    }

    jsonResponse(200, ['status' => 'ok', 'message' => 'FINE LINE API está funcionando.']);
}

$isCollection = $path === '/api/productos';
$isItem = preg_match('#^/api/productos/([1-9][0-9]*)$#', $path, $matches) === 1;

if (!$isCollection && !$isItem) {
    jsonResponse(404, ['error' => 'Ruta no encontrada.']);
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Producto.php';
require_once __DIR__ . '/controllers/ProductoController.php';

try {
    $controller = new ProductoController(new Producto(Database::connect()));

    if ($isCollection) {
        if ($method === 'GET') {
            $controller->index();
        }
        if ($method === 'POST') {
            $controller->store(jsonBody());
        }

        header('Allow: GET, POST');
        jsonResponse(405, ['error' => 'Método no permitido.']);
    }

    $id = (int) $matches[1];
    match ($method) {
        'GET' => $controller->show($id),
        'PUT' => $controller->replace($id, jsonBody()),
        'PATCH' => $controller->patch($id, jsonBody()),
        'DELETE' => $controller->destroy($id),
        default => (function (): never {
            header('Allow: GET, PUT, PATCH, DELETE');
            jsonResponse(405, ['error' => 'Método no permitido.']);
        })(),
    };
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    jsonResponse(500, ['error' => 'Error interno de base de datos.']);
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    jsonResponse(500, ['error' => 'Error interno del servidor.']);
}
