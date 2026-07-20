<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\UpdateProductAction;
use App\Domain\Shared\Money;
use App\Http\Catalog\Requests\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Http\CatalogResource;
use Illuminate\Http\JsonResponse;

final class UpdateProductController extends Controller
{
    public function __invoke(
        UpdateProductRequest $request,
        int $productId,
        UpdateProductAction $action,
    ): JsonResponse {
        $validated = $request->validated();

        if (isset($validated['base_price'])) {
            $validated['base_price'] = Money::fromDecimalInput($validated['base_price']);
        }

        /** @var User $actor */
        $actor = $request->user();
        $product = $action->execute($actor, $productId, $validated);

        return response()->json([
            'data' => [
                'message' => 'Product updated.',
                'product' => CatalogResource::productToArray($product),
            ],
        ]);
    }
}
