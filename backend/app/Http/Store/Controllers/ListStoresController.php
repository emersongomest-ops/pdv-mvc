<?php

declare(strict_types=1);

namespace App\Http\Store\Controllers;

use App\Application\Store\Actions\ListAccessibleStoresAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListStoresController extends Controller
{
    public function __invoke(Request $request, ListAccessibleStoresAction $action): JsonResponse
    {
        $stores = $action->execute($request->user());

        return response()->json([
            'data' => [
                'stores' => array_map(
                    static fn ($store): array => [
                        'id' => $store->id,
                        'name' => $store->name,
                        'code' => $store->code,
                    ],
                    $stores
                ),
            ],
        ]);
    }
}
