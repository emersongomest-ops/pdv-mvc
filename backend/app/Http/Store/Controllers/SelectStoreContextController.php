<?php

declare(strict_types=1);

namespace App\Http\Store\Controllers;

use App\Application\Store\Actions\SelectStoreContextAction;
use App\Http\Controllers\Controller;
use App\Http\Store\Requests\SelectStoreContextRequest;
use Illuminate\Http\JsonResponse;

final class SelectStoreContextController extends Controller
{
    public function __invoke(SelectStoreContextRequest $request, SelectStoreContextAction $action): JsonResponse
    {
        $validated = $request->validated();

        $store = $action->execute(
            $request->user(),
            $validated['store_id'],
            $request,
        );

        return response()->json([
            'data' => [
                'message' => 'Store context selected.',
                'store' => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'code' => $store->code,
                ],
            ],
        ]);
    }
}
