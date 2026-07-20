<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\DeleteCategoryAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class DeleteCategoryController extends Controller
{
    public function __invoke(int $categoryId, DeleteCategoryAction $action): JsonResponse
    {
        $action->execute($categoryId);

        return response()->json([
            'data' => ['message' => 'Category deleted.'],
        ]);
    }
}
