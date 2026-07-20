<?php

declare(strict_types=1);

namespace App\Http\Promotions\Controllers;

use App\Application\Promotions\Actions\ShowPromotionAction;
use App\Http\Controllers\Controller;
use App\Support\Http\PromotionResource;
use Illuminate\Http\JsonResponse;

final class ShowPromotionController extends Controller
{
    public function __invoke(ShowPromotionAction $action, int $promotionId): JsonResponse
    {
        $promotion = $action->execute($promotionId);

        return response()->json([
            'data' => [
                'promotion' => PromotionResource::toArray($promotion),
            ],
        ]);
    }
}
