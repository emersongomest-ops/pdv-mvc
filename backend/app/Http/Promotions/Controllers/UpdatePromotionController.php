<?php

declare(strict_types=1);

namespace App\Http\Promotions\Controllers;

use App\Application\Promotions\Actions\UpdatePromotionAction;
use App\Domain\Shared\Money;
use App\Http\Controllers\Controller;
use App\Http\Promotions\Requests\UpdatePromotionRequest;
use App\Models\User;
use App\Support\Http\PromotionResource;
use Illuminate\Http\JsonResponse;

final class UpdatePromotionController extends Controller
{
    public function __invoke(
        UpdatePromotionRequest $request,
        UpdatePromotionAction $action,
        int $promotionId,
    ): JsonResponse {
        $validated = $request->validated();

        if (array_key_exists('discount_value', $validated)) {
            $validated['discount_value'] = Money::fromDecimalInput($validated['discount_value']);
        }

        $customerIds = array_key_exists('customer_ids', $validated)
            ? array_map('intval', $validated['customer_ids'])
            : null;

        /** @var User $actor */
        $actor = $request->user();
        $promotion = $action->execute($actor, $promotionId, $validated, $customerIds);

        return response()->json([
            'data' => [
                'message' => 'Promotion updated.',
                'promotion' => PromotionResource::toArray($promotion),
            ],
        ]);
    }
}
