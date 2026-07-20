<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\ShowCategoryAction;
use App\Http\Controllers\Controller;
use App\Support\Http\CatalogResource;
use Illuminate\Http\JsonResponse;

final class ShowCategoryController extends Controller
{
    public function __invoke(int $categoryId, ShowCategoryAction $action): JsonResponse
    {
        return response()->json([
            'data' => [
                'category' => CatalogResource::categoryToArray($action->execute($categoryId)),
            ],
        ]);
    }
}
