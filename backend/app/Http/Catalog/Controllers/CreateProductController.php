<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\CreateProductAction;
use App\Domain\Shared\Money;
use App\Http\Catalog\Requests\StoreProductRequest;
use App\Http\Controllers\Controller;
use App\Support\Http\CatalogResource;
use Illuminate\Http\JsonResponse;

final class CreateProductController extends Controller
{
    public function __invoke(StoreProductRequest $request, CreateProductAction $action): JsonResponse
    {
        $validated = $request->validated();

        $product = $action->execute([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'base_price' => Money::fromDecimalInput($validated['base_price']),
            'is_active' => $validated['is_active'] ?? true,
            'category_id' => $validated['category_id'] ?? null,
        ]);

        return response()->json([
            'data' => [
                'message' => 'Product created.',
                'product' => CatalogResource::productToArray($product),
            ],
        ], 201);
    }
}
