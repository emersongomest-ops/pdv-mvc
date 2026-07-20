<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\UpdateCategoryAction;
use App\Http\Catalog\Requests\UpdateCategoryRequest;
use App\Http\Controllers\Controller;
use App\Support\Http\CatalogResource;
use Illuminate\Http\JsonResponse;

final class UpdateCategoryController extends Controller
{
    public function __invoke(
        UpdateCategoryRequest $request,
        int $categoryId,
        UpdateCategoryAction $action,
    ): JsonResponse {
        $category = $action->execute($categoryId, $request->validated());

        return response()->json([
            'data' => [
                'message' => 'Category updated.',
                'category' => CatalogResource::categoryToArray($category),
            ],
        ]);
    }
}
