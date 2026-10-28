<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    /**
     * Lista productos. La divisa base siempre viene incluida.
     */
    #[OA\Get(
        path: '/products',
        summary: 'Listar productos',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'included', in: 'query', description: 'Relaciones: currency, prices, prices.currency', schema: new OA\Schema(type: 'string', example: 'prices')),
            new OA\Parameter(name: 'filter[name]', in: 'query', description: 'Filtra el nombre con coincidencia parcial', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[description]', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort', in: 'query', description: 'Campos: id, name, price, tax_cost, manufacturing_cost. El prefijo - invierte el orden.', schema: new OA\Schema(type: 'string', example: '-price')),
            new OA\Parameter(name: 'perPage', in: 'query', description: 'Si se envía, la respuesta se pagina. Máximo 100.', schema: new OA\Schema(type: 'integer', example: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de productos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Product')),
                    ]
                )
            ),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with('currency')
            ->included()
            ->filter()
            ->sort()
            ->getOrPaginate();

        return ProductResource::collection($products);
    }

    #[OA\Post(
        path: '/products',
        summary: 'Crear un producto',
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductBody')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Producto creado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Datos inválidos'),
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::query()->create($request->validated());
        $product->load('currency');

        return ProductResource::make($product)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/products/{id}',
        summary: 'Obtener un producto',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'included', in: 'query', schema: new OA\Schema(type: 'string', example: 'prices')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function show(Product $product): ProductResource
    {
        $product = Product::query()
            ->with('currency')
            ->included()
            ->findOrFail($product->getKey());

        return ProductResource::make($product);
    }

    #[OA\Put(
        path: '/products/{id}',
        summary: 'Actualizar un producto',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductBody')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto actualizado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
            new OA\Response(response: 422, description: 'Datos inválidos'),
        ]
    )]
    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());
        $product->load('currency');

        return ProductResource::make($product);
    }

    #[OA\Delete(
        path: '/products/{id}',
        summary: 'Eliminar un producto',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Producto eliminado'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function destroy(Product $product): Response
    {
        $product->delete();

        return response()->noContent();
    }
}
