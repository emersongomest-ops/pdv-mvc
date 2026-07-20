<?php

declare(strict_types=1);

namespace App\Http\Inventory\Controllers;

use App\Application\Inventory\Actions\ListStoreInventoryAction;
use App\Http\Controllers\Controller;
use App\Http\Inventory\Requests\ListStoreInventoryRequest;
use App\Models\User;
use App\Support\Http\InventoryResource;
use Illuminate\Http\JsonResponse;

final class ListStoreInventoryController extends Controller
{
    public function __invoke(ListStoreInventoryRequest $request, ListStoreInventoryAction $action): JsonResponse
    {
        /** @var User $manager */
        $manager = $request->user();
        $storeId = (int) $request->validated('store_id');
        $items = $action->execute($manager, $storeId);

        return response()->json([
            'data' => [
                'inventory' => $items
                    ->map(fn ($item): array => InventoryResource::toArray($item))
                    ->values()
                    ->all(),
            ],
        ]);
    }
}
