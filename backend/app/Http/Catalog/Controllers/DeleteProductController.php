<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\DeleteProductAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class DeleteProductController extends Controller
{
    public function __invoke(int $productId, DeleteProductAction $action): JsonResponse
    {
        $action->execute($productId);

        return response()->json([
            'data' => ['message' => 'Product deleted.'],
        ]);
    }
}
