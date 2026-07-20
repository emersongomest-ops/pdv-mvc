<?php

declare(strict_types=1);

namespace App\Http\Catalog\Controllers;

use App\Application\Catalog\Actions\ListOperationalProductsAction;
use App\Http\Catalog\Requests\ListOperationalProductsRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class ListOperationalProductsController extends Controller
{
    public function __invoke(
        ListOperationalProductsRequest $request,
        ListOperationalProductsAction $action,
    ): JsonResponse {
        $validated = $request->validated();

        try {
            $result = $action->execute(
                (int) $request->attributes->get('store_id'),
                isset($validated['category_id']) ? (int) $validated['category_id'] : null,
                isset($validated['search']) && is_string($validated['search'])
                    ? $validated['search']
                    : null,
                isset($validated['cursor']) && is_string($validated['cursor'])
                    ? $validated['cursor']
                    : null,
                isset($validated['per_page']) ? (int) $validated['per_page'] : 50,
            );
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'cursor' => ['The cursor is invalid.'],
            ]);
        }

        return response()->json([
            'data' => [
                'products' => $result['products'],
            ],
            'meta' => [
                'next_cursor' => $result['next_cursor'],
            ],
        ]);
    }
}
