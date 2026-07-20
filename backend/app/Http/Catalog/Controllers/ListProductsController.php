<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\ListProductsAction;
use App\Http\Catalog\Requests\ListProductsRequest;
use App\Http\Controllers\Controller;
use App\Support\Http\CatalogResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class ListProductsController extends Controller
{
    public function __invoke(ListProductsRequest $request, ListProductsAction $action): JsonResponse
    {
        $validated = $request->validated();
        $categoryId = $validated['category_id'] ?? null;
        $isActive = array_key_exists('is_active', $validated) ? (bool) $validated['is_active'] : null;
        $search = $validated['search'] ?? null;
        $cursor = $validated['cursor'] ?? null;
        $perPage = $validated['per_page'] ?? null;

        try {
            $result = $action->execute(
                $categoryId !== null ? (int) $categoryId : null,
                $isActive,
                is_string($search) && $search !== '' ? $search : null,
                is_string($cursor) && $cursor !== '' ? $cursor : null,
                $perPage !== null ? (int) $perPage : null,
            );
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'cursor' => ['The cursor is invalid.'],
            ]);
        }

        $payload = [
            'data' => [
                'products' => $result['products']
                    ->map(fn ($product): array => CatalogResource::productToArray($product))
                    ->values()
                    ->all(),
            ],
        ];

        if ($perPage !== null) {
            $payload['meta'] = [
                'next_cursor' => $result['next_cursor'],
            ];
        }

        return response()->json($payload);
    }
}
