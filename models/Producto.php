<?php

declare(strict_types=1);

final class Producto
{
    private const TABLE = 'productos';

    private const WRITABLE_FIELDS = [
        'codigo',
        'id_categoria',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'imagen',
        'estado',
    ];

    public function __construct(private readonly PDO $connection)
    {
    }

    public function all(): array
    {
        $statement = $this->connection->query(
            'SELECT id_producto, codigo, id_categoria, nombre, descripcion, precio, stock, imagen, estado, fecha_registro
             FROM ' . self::TABLE . '
             ORDER BY id_producto ASC'
        );

        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->connection->prepare(
            'SELECT id_producto, codigo, id_categoria, nombre, descripcion, precio, stock, imagen, estado, fecha_registro
             FROM ' . self::TABLE . '
             WHERE id_producto = :id'
        );
        $statement->execute(['id' => $id]);
        $producto = $statement->fetch();

        return $producto === false ? null : $producto;
    }

    public function create(array $data): array
    {
        $fields = array_values(array_intersect(self::WRITABLE_FIELDS, array_keys($data)));
        $columns = implode(', ', $fields);
        $placeholders = implode(', ', array_map(static fn (string $field): string => ':' . $field, $fields));

        $statement = $this->connection->prepare(
            sprintf('INSERT INTO %s (%s) VALUES (%s)', self::TABLE, $columns, $placeholders)
        );
        $statement->execute($this->parameters($data, $fields));

        return $this->find((int) $this->connection->lastInsertId());
    }

    public function update(int $id, array $data): ?array
    {
        $fields = array_values(array_intersect(self::WRITABLE_FIELDS, array_keys($data)));
        $assignments = implode(', ', array_map(
            static fn (string $field): string => $field . ' = :' . $field,
            $fields
        ));

        $statement = $this->connection->prepare(
            sprintf('UPDATE %s SET %s WHERE id_producto = :id', self::TABLE, $assignments)
        );
        $parameters = $this->parameters($data, $fields);
        $parameters['id'] = $id;
        $statement->execute($parameters);

        return $this->find($id);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare(
            'DELETE FROM ' . self::TABLE . ' WHERE id_producto = :id'
        );
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    private function parameters(array $data, array $fields): array
    {
        $parameters = [];
        foreach ($fields as $field) {
            $parameters[$field] = $data[$field];
        }

        return $parameters;
    }
}
