<?php

declare(strict_types=1);

final class ProductoController
{
    private const DEFAULTS = [
        'id_categoria' => null,
        'descripcion' => null,
        'precio' => 0,
        'stock' => 0,
        'imagen' => null,
        'estado' => 'DISPONIBLE',
    ];

    private const VALID_STATES = ['DISPONIBLE', 'AGOTADO', 'INACTIVO'];

    private const ALLOWED_FIELDS = [
        'codigo',
        'id_categoria',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'imagen',
        'estado',
    ];

    public function __construct(private readonly Producto $producto)
    {
    }

    public function index(): void
    {
        jsonResponse(200, ['data' => $this->producto->all()]);
    }

    public function show(int $id): void
    {
        $producto = $this->producto->find($id);
        if ($producto === null) {
            jsonResponse(404, ['error' => 'Producto no encontrado.']);
        }

        jsonResponse(200, ['data' => $producto]);
    }

    public function store(array $input): void
    {
        $data = array_merge(self::DEFAULTS, $this->onlyAllowed($input));
        $errors = $this->validate($data, true);
        if ($errors !== []) {
            jsonResponse(400, ['error' => 'Datos inválidos.', 'details' => $errors]);
        }

        jsonResponse(201, [
            'message' => 'Producto creado correctamente.',
            'data' => $this->producto->create($data),
        ]);
    }

    public function replace(int $id, array $input): void
    {
        if ($this->producto->find($id) === null) {
            jsonResponse(404, ['error' => 'Producto no encontrado.']);
        }

        $data = array_merge(self::DEFAULTS, $this->onlyAllowed($input));
        $errors = $this->validate($data, true);
        if ($errors !== []) {
            jsonResponse(400, ['error' => 'Datos inválidos.', 'details' => $errors]);
        }

        jsonResponse(200, [
            'message' => 'Producto actualizado correctamente.',
            'data' => $this->producto->update($id, $data),
        ]);
    }

    public function patch(int $id, array $input): void
    {
        if ($this->producto->find($id) === null) {
            jsonResponse(404, ['error' => 'Producto no encontrado.']);
        }

        $data = $this->onlyAllowed($input);
        if ($data === []) {
            jsonResponse(400, ['error' => 'Debe enviar al menos un campo válido para actualizar.']);
        }

        $errors = $this->validate($data, false);
        if ($errors !== []) {
            jsonResponse(400, ['error' => 'Datos inválidos.', 'details' => $errors]);
        }

        jsonResponse(200, [
            'message' => 'Producto actualizado parcialmente.',
            'data' => $this->producto->update($id, $data),
        ]);
    }

    public function destroy(int $id): void
    {
        if (!$this->producto->delete($id)) {
            jsonResponse(404, ['error' => 'Producto no encontrado.']);
        }

        jsonResponse(200, ['message' => 'Producto eliminado correctamente.']);
    }

    private function onlyAllowed(array $input): array
    {
        return array_intersect_key($input, array_flip(self::ALLOWED_FIELDS));
    }

    private function validate(array &$data, bool $requireIdentity): array
    {
        $errors = [];

        foreach (['codigo', 'nombre'] as $field) {
            if (($requireIdentity || array_key_exists($field, $data))
                && (!is_string($data[$field] ?? null) || trim($data[$field]) === '')) {
                $errors[$field] = 'El campo ' . $field . ' es obligatorio.';
            } elseif (array_key_exists($field, $data)) {
                $data[$field] = trim($data[$field]);
            }
        }

        if (array_key_exists('precio', $data)) {
            if (!is_int($data['precio']) && !is_float($data['precio'])) {
                $errors['precio'] = 'El campo precio debe ser numérico.';
            } elseif ($data['precio'] < 0) {
                $errors['precio'] = 'El campo precio debe ser mayor o igual a cero.';
            } else {
                $data['precio'] = (float) $data['precio'];
            }
        }

        if (array_key_exists('stock', $data)) {
            if (!is_int($data['stock']) || $data['stock'] < 0) {
                $errors['stock'] = 'El campo stock debe ser un entero mayor o igual a cero.';
            }
        }

        if ($requireIdentity || array_key_exists('id_categoria', $data)) {
            if (!is_int($data['id_categoria'] ?? null) || $data['id_categoria'] <= 0) {
                $errors['id_categoria'] = 'El campo id_categoria debe ser un entero válido.';
            }
        }

        if (array_key_exists('estado', $data)) {
            if (!is_string($data['estado']) || !in_array($data['estado'], self::VALID_STATES, true)) {
                $errors['estado'] = 'El campo estado debe ser DISPONIBLE, AGOTADO o INACTIVO.';
            }
        }

        foreach (['descripcion', 'imagen'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null && !is_string($data[$field])) {
                $errors[$field] = 'El campo ' . $field . ' debe ser texto o null.';
            }
        }

        return $errors;
    }
}
