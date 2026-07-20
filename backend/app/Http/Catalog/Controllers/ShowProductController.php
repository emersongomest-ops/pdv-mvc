<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\ShowProductAction;
use App\Http\Controllers\Controller;
use App\Support\Http\CatalogResource;
use Illuminate\Http\JsonResponse;

final class ShowProductController extends Controller
{
    public function __invoke(int $productId, ShowProductAction $action): JsonResponse
    {
        return response()->json([
            'data' => [
                'product' => CatalogResource::productToArray($action->execute($productId)),
            ],
        ]);
    }
}
