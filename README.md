# FINE LINE API

API REST universitaria para administrar los productos de la base de datos MySQL `fine_line_api`. Está desarrollada con PHP 8.2, PDO y prepared statements, sin frameworks.

## Requisitos

- XAMPP con PHP 8.2 y la extensión `pdo_mysql` activa.
- MySQL ejecutándose en `127.0.0.1:3306`.
- Base de datos `fine_line_api` con la tabla `productos`.
- Usuario MySQL `root` sin contraseña (configuración local indicada).

## Iniciar la API

Desde `C:\xampp\htdocs\fine-line-api`:

```powershell
C:\xampp\php\php.exe -S localhost:8000 router.php
```

La API queda disponible en `http://localhost:8000`.

## Endpoints

| Método | Ruta | Acción | Respuesta exitosa |
|---|---|---|---|
| GET | `/health` | Comprobar el servicio | 200 |
| GET | `/api/productos` | Listar productos | 200 |
| GET | `/api/productos/{id}` | Consultar un producto | 200 |
| POST | `/api/productos` | Crear un producto | 201 |
| PUT | `/api/productos/{id}` | Reemplazar los datos editables | 200 |
| PATCH | `/api/productos/{id}` | Actualizar campos enviados | 200 |
| DELETE | `/api/productos/{id}` | Eliminar un producto | 200 |

Todas las respuestas usan JSON. Para `POST`, `PUT` y `PATCH`, envíe el encabezado `Content-Type: application/json`.

## Ejemplos

Crear un producto:

```powershell
curl.exe -X POST http://localhost:8000/api/productos `
  -H "Content-Type: application/json" `
  -d '{"codigo":"PRD-028","id_categoria":1,"nombre":"Producto de prueba","precio":25.50,"stock":10,"estado":"DISPONIBLE"}'
```

Actualizar parcialmente:

```powershell
curl.exe -X PATCH http://localhost:8000/api/productos/1 `
  -H "Content-Type: application/json" `
  -d '{"stock":15,"precio":30}'
```

El `PUT` exige `codigo` y `nombre`; los campos editables opcionales que se omitan se reemplazan con sus valores predeterminados. El `PATCH` conserva todos los campos no enviados.

## Validaciones

- `codigo`, `id_categoria` y `nombre` son obligatorios al crear o reemplazar un producto.
- `id_categoria` debe ser un entero mayor que cero.
- `precio` debe ser numérico y mayor o igual a cero.
- `stock` debe ser un entero mayor o igual a cero.
- `estado` debe ser `DISPONIBLE`, `AGOTADO` o `INACTIVO`.
- `fecha_registro` no se recibe: la genera la base de datos.
- Los identificadores inexistentes devuelven HTTP 404.
- JSON vacío, mal formado o inválido devuelve HTTP 400.
- Los errores internos se registran en el log de PHP sin exponer detalles sensibles al cliente.

> Las peticiones `POST`, `PUT`, `PATCH` y `DELETE` sí modifican la base de datos. Los ejemplos se incluyen como documentación y no se ejecutan automáticamente.
