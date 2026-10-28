# Beos Products API

API REST en Laravel 13 para productos, su divisa base y precios en otras divisas. Corre con Docker Compose. La documentación interactiva está en Swagger.

## Arranque

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

- Productos: `http://localhost:8080/products`
- Swagger: `http://localhost:8080/api/documentation`
- MySQL desde el host: puerto `3307`, base `beos`, usuario `beos`, clave `secret`

## Pruebas

```bash
docker compose run --rm --no-deps app php artisan test
```

## Listado

La colección de productos acepta los mismos modificadores que el proyecto de referencia:

- `included=prices` carga relaciones permitidas (`currency`, `prices`, `prices.currency`)
- `filter[name]=notebook` filtra por coincidencia parcial
- `sort=-price` ordena. El prefijo `-` invierte el orden
- `perPage=15` pagina. Sin ese parámetro devuelve el listado completo. El máximo es 100

El precio base vive en el producto. `POST /products/{id}/prices` agrega un precio en otra divisa y no admite dos precios para la misma divisa.
