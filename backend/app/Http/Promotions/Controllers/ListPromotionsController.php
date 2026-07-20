<?php

declare(strict_types=1);

namespace App\Http\Promotions\Controllers;

use App\Application\Promotions\Actions\ListPromotionsAction;
use App\Http\Controllers\Controller;
use App\Http\Promotions\Requests\ListPromotionsRequest;
use App\Models\Promotion;
use App\Support\Http\PromotionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class ListPromotionsController extends Controller
{
    public function __invoke(ListPromotionsRequest $request, ListPromotionsAction $action): JsonResponse
    {
        $validated = $request->validated();
        $cursor = $validated['cursor'] ?? null;
        $perPage = $validated['per_page'] ?? null;

        try {
            $result = $action->execute(
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
                'promotions' => $result['promotions']
                    ->map(fn (Promotion $promotion): array => PromotionResource::toArray($promotion))
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
