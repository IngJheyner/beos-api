<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Beos Products API',
    description: 'API REST de productos, su divisa base y precios en otras divisas. Las respuestas son JSON. Cada IP tiene un límite de 60 peticiones por minuto.'
)]
#[OA\Server(url: 'http://localhost:8080', description: 'Docker Compose local')]
#[OA\Tag(name: 'Products', description: 'Alta, consulta, actualización y baja de productos')]
#[OA\Tag(name: 'Prices', description: 'Precios de un producto en divisas distintas a la base')]
#[OA\Schema(
    schema: 'Currency',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'US Dollar'),
        new OA\Property(property: 'symbol', type: 'string', example: 'USD'),
        new OA\Property(property: 'exchange_rate', type: 'string', example: '1.000000'),
    ]
)]
#[OA\Schema(
    schema: 'Product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Notebook'),
        new OA\Property(property: 'description', type: 'string', example: 'Portátil de 14 pulgadas'),
        new OA\Property(property: 'price', type: 'string', example: '1200.50'),
        new OA\Property(property: 'currency_id', type: 'integer', example: 1),
        new OA\Property(property: 'tax_cost', type: 'string', example: '228.10'),
        new OA\Property(property: 'manufacturing_cost', type: 'string', example: '640.00'),
        new OA\Property(property: 'currency', ref: '#/components/schemas/Currency'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'ProductPrice',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 10),
        new OA\Property(property: 'product_id', type: 'integer', example: 1),
        new OA\Property(property: 'currency_id', type: 'integer', example: 2),
        new OA\Property(property: 'price', type: 'string', example: '1100.00'),
        new OA\Property(property: 'currency', ref: '#/components/schemas/Currency'),
    ]
)]
#[OA\Schema(
    schema: 'ProductBody',
    required: ['name', 'description', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Notebook'),
        new OA\Property(property: 'description', type: 'string', example: 'Portátil de 14 pulgadas'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 1200.5),
        new OA\Property(property: 'currency_id', type: 'integer', example: 1),
        new OA\Property(property: 'tax_cost', type: 'number', format: 'float', example: 228.1),
        new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float', example: 640),
    ]
)]
#[OA\Schema(
    schema: 'ProductPriceBody',
    required: ['currency_id', 'price'],
    properties: [
        new OA\Property(property: 'currency_id', type: 'integer', example: 2),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 1100),
    ]
)]
class OpenApiSpec
{
}
