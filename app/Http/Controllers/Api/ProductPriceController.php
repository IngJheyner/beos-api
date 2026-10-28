<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPrice\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductPriceController extends Controller
{
    public function index(Product $product): AnonymousResourceCollection
    {
        $prices = $product->prices()->with('currency')->orderBy('id')->get();

        return ProductPriceResource::collection($prices);
    }

    public function store(StoreProductPriceRequest $request, Product $product): JsonResponse
    {
        $price = $product->prices()->create($request->validated());
        $price->load('currency');

        return ProductPriceResource::make($price)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
