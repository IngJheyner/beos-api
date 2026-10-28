<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPrice\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ProductPriceController extends Controller
{
    #[OA\Get(
        path: '/products/{id}/prices',
        summary: 'Listar precios de un producto',
        tags: ['Prices'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Precios del producto en otras divisas',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ProductPrice')),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function index(Product $product): AnonymousResourceCollection
    {
        $prices = $product->prices()->with('currency')->orderBy('id')->get();

        return ProductPriceResource::collection($prices);
    }

    #[OA\Post(
        path: '/products/{id}/prices',
        summary: 'Registrar un precio en otra divisa',
        tags: ['Prices'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductPriceBody')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Precio creado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductPrice'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
            new OA\Response(response: 422, description: 'Datos inválidos o divisa repetida'),
        ]
    )]
    public function store(StoreProductPriceRequest $request, Product $product): JsonResponse
    {
        $price = $product->prices()->create($request->validated());
        $price->load('currency');

        return ProductPriceResource::make($price)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
