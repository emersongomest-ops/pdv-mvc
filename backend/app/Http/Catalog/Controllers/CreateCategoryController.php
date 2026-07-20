<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\CreateCategoryAction;
use App\Http\Catalog\Requests\StoreCategoryRequest;
use App\Http\Controllers\Controller;
use App\Support\Http\CatalogResource;
use Illuminate\Http\JsonResponse;

final class CreateCategoryController extends Controller
{
    public function __invoke(StoreCategoryRequest $request, CreateCategoryAction $action): JsonResponse
    {
        $validated = $request->validated();
        $category = $action->execute(
            $validated['name'],
            $validated['is_active'] ?? true,
        );

        return response()->json([
            'data' => [
                'message' => 'Category created.',
                'category' => CatalogResource::categoryToArray($category),
            ],
        ], 201);
    }
}
