<?php

declare(strict_types=1);

namespace App\Http\Inventory\Controllers;

use App\Application\Inventory\Actions\AdjustStoreInventoryAction;
use App\Http\Controllers\Controller;
use App\Http\Inventory\Requests\AdjustStoreInventoryRequest;
use App\Models\User;
use App\Support\Http\InventoryResource;
use Illuminate\Http\JsonResponse;

final class AdjustStoreInventoryController extends Controller
{
    public function __invoke(
        AdjustStoreInventoryRequest $request,
        AdjustStoreInventoryAction $action,
    ): JsonResponse {
        $validated = $request->validated();

        /** @var User $manager */
        $manager = $request->user();

        $inventory = $action->execute(
            $manager,
            (int) $validated['store_id'],
            (int) $validated['product_id'],
            (int) $validated['quantity'],
            (string) $validated['reason'],
        );

        return response()->json([
            'data' => [
                'message' => 'Stock adjusted.',
                'inventory' => InventoryResource::toArray($inventory),
            ],
        ]);
    }
}
