<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\ListCategoriesAction;
use App\Http\Controllers\Controller;
use App\Support\Http\CatalogResource;
use Illuminate\Http\JsonResponse;

final class ListCategoriesController extends Controller
{
    public function __invoke(ListCategoriesAction $action): JsonResponse
    {
        $categories = $action->execute();

        return response()->json([
            'data' => [
                'categories' => $categories
                    ->map(fn ($category): array => CatalogResource::categoryToArray($category))
                    ->values()
                    ->all(),
            ],
        ]);
    }
}
