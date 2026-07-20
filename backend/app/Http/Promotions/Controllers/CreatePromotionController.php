<?php

declare(strict_types=1);

namespace App\Http\Promotions\Controllers;

use App\Application\Promotions\Actions\CreatePromotionAction;
use App\Domain\Shared\Money;
use App\Http\Controllers\Controller;
use App\Http\Promotions\Requests\StorePromotionRequest;
use App\Models\User;
use App\Support\Http\PromotionResource;
use Illuminate\Http\JsonResponse;

final class CreatePromotionController extends Controller
{
    public function __invoke(StorePromotionRequest $request, CreatePromotionAction $action): JsonResponse
    {
        $validated = $request->validated();
        $validated['discount_value'] = Money::fromDecimalInput($validated['discount_value']);

        /** @var list<int> $customerIds */
        $customerIds = array_map('intval', $validated['customer_ids'] ?? []);

        /** @var User $actor */
        $actor = $request->user();
        $promotion = $action->execute($actor, $validated, $customerIds);

        return response()->json([
            'data' => [
                'message' => 'Promotion created.',
                'promotion' => PromotionResource::toArray($promotion),
            ],
        ], 201);
    }
}
