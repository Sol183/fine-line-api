# FINE LINE API REST

API REST desarrollada para el sistema **FINE LINE**, una tienda de productos de arte y pintura.

La API permite administrar el catálogo de productos mediante operaciones CRUD utilizando los métodos HTTP GET, POST, PUT, PATCH y DELETE.

El servicio fue desarrollado con **PHP 8.2, MySQL y PDO**, y se encuentra desplegado públicamente en Railway.

---

## URL de Producción

**URL Base:**

https://fine-line-api-production.up.railway.app

**Verificar estado de la API:**

https://fine-line-api-production.up.railway.app/health

---

## Tecnologías utilizadas

- PHP 8.2
- MySQL
- PDO
- API REST
- JSON
- Git / GitHub
- Railway
- Postman

---

## Endpoints

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/health` | Verifica que la API esté funcionando |
| GET | `/api/productos` | Obtiene todos los productos |
| GET | `/api/productos/{id}` | Obtiene un producto por su ID |
| POST | `/api/productos` | Crea un nuevo producto |
| PUT | `/api/productos/{id}` | Actualiza completamente un producto |
| PATCH | `/api/productos/{id}` | Actualiza parcialmente un producto |
| DELETE | `/api/productos/{id}` | Elimina un producto |

---

## Estructura de un producto

Los productos manejan los siguientes campos:

| Campo | Tipo | Descripción |
|---|---|---|
| `id_producto` | Integer | Identificador único generado automáticamente |
| `codigo` | String | Código único del producto |
| `id_categoria` | Integer | Identificador de la categoría |
| `nombre` | String | Nombre del producto |
| `descripcion` | String | Descripción del producto |
| `precio` | Decimal | Precio del producto |
| `stock` | Integer | Cantidad disponible |
| `imagen` | String | Nombre o ruta de la imagen |
| `estado` | String | DISPONIBLE, AGOTADO o INACTIVO |
| `fecha_registro` | DateTime | Fecha de registro generada por la base de datos |

---

# Ejemplos de uso

## 1. Obtener todos los productos

### Petición

```http
GET /api/productos
```

### Ejemplo de respuesta

```json
{
  "data": [
    {
      "id_producto": 1,
      "codigo": "PROD-001",
      "id_categoria": 2,
      "nombre": "Juego de Pinceles",
      "descripcion": "Juego básico de pinceles para pintura y manualidades.",
      "precio": "25.00",
      "stock": 50,
      "imagen": "juego-pinceles.jpg",
      "estado": "DISPONIBLE",
      "fecha_registro": "2026-07-29 01:24:25"
    }
  ]
}
```

**Código HTTP:** `200 OK`

---

## 2. Obtener un producto por ID

### Petición

```http
GET /api/productos/1
```

### Ejemplo de respuesta

```json
{
  "data": {
    "id_producto": 1,
    "codigo": "PROD-001",
    "id_categoria": 2,
    "nombre": "Juego de Pinceles",
    "descripcion": "Juego básico de pinceles para pintura y manualidades.",
    "precio": "25.00",
    "stock": 50,
    "imagen": "juego-pinceles.jpg",
    "estado": "DISPONIBLE"
  }
}
```

**Código HTTP:** `200 OK`

---

## 3. Crear un producto

### Petición

```http
POST /api/productos
```

### Body JSON

```json
{
  "codigo": "PROD-028",
  "id_categoria": 1,
  "nombre": "Set de Pintura Gouache",
  "descripcion": "Set de pinturas gouache para técnicas artísticas.",
  "precio": 135.00,
  "stock": 20,
  "imagen": "set-gouache.jpg",
  "estado": "DISPONIBLE"
}
```

### Ejemplo de respuesta

```json
{
  "message": "Producto creado correctamente.",
  "data": {
    "id_producto": 28,
    "codigo": "PROD-028",
    "nombre": "Set de Pintura Gouache"
  }
}
```

**Código HTTP:** `201 Created`

---

## 4. Actualizar completamente un producto

### Petición

```http
PUT /api/productos/28
```

### Body JSON

```json
{
  "codigo": "PROD-028",
  "id_categoria": 1,
  "nombre": "Set de Pintura Gouache Profesional",
  "descripcion": "Set profesional de pinturas gouache para artistas.",
  "precio": 150.00,
  "stock": 25,
  "imagen": "set-gouache.jpg",
  "estado": "DISPONIBLE"
}
```

### Ejemplo de respuesta

```json
{
  "message": "Producto actualizado correctamente."
}
```

**Código HTTP:** `200 OK`

---

## 5. Actualizar parcialmente un producto

### Petición

```http
PATCH /api/productos/28
```

### Body JSON

```json
{
  "precio": 145.00,
  "stock": 30
}
```

### Ejemplo de respuesta

```json
{
  "message": "Producto actualizado correctamente."
}
```

**Código HTTP:** `200 OK`

---

## 6. Eliminar un producto

### Petición

```http
DELETE /api/productos/28
```

### Ejemplo de respuesta

```json
{
  "message": "Producto eliminado correctamente."
}
```

**Código HTTP:** `200 OK`

---

# Manejo de errores

La API utiliza códigos HTTP adecuados según el resultado de cada solicitud.

| Código | Significado |
|---|---|
| `200 OK` | Operación realizada correctamente |
| `201 Created` | Producto creado correctamente |
| `400 Bad Request` | Datos enviados incorrectos o inválidos |
| `404 Not Found` | Producto o ruta no encontrada |
| `405 Method Not Allowed` | Método HTTP no permitido |
| `500 Internal Server Error` | Error interno del servidor |

### Ejemplo: producto inexistente

```http
GET /api/productos/9999
```

Respuesta:

```json
{
  "error": "Producto no encontrado."
}
```

Código:

```text
404 Not Found
```

---

# Validaciones

La API realiza las siguientes validaciones:

- `codigo` es obligatorio.
- `id_categoria` debe ser un número entero positivo.
- `nombre` es obligatorio.
- `precio` debe ser numérico y no puede ser negativo.
- `stock` debe ser un número entero y no puede ser negativo.
- `estado` solamente acepta:
  - `DISPONIBLE`
  - `AGOTADO`
  - `INACTIVO`
- Si el producto no existe, se devuelve `404 Not Found`.

---

# Pruebas

Los endpoints fueron probados con **Postman** utilizando directamente la URL pública de Railway.

Se verificaron correctamente:

- GET → `200 OK`
- GET por ID → `200 OK`
- POST → `201 Created`
- PUT → `200 OK`
- DELETE → `200 OK`
- Producto inexistente → `404 Not Found`

---

# Ejecución local

Para ejecutar el proyecto localmente con XAMPP:

```bash
C:\xampp\php\php.exe -S localhost:8000 router.php
```

Después puede accederse a:

```text
http://localhost:8000/health
```

y:

```text
http://localhost:8000/api/productos
```

---

# Repositorio

Proyecto desarrollado para la actividad de **Desarrollo, Despliegue y Validación de una API RESTful**.

Repositorio:

https://github.com/Sol183/fine-line-api
