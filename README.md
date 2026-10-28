# Beos Products API

API REST en Laravel 13. Corre con Docker Compose: PHP, Nginx y MySQL.

## Arranque

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

- Productos: `http://localhost:8080/products`
- MySQL desde el host: puerto `3307`, base `beos`, usuario `beos`, clave `secret`
