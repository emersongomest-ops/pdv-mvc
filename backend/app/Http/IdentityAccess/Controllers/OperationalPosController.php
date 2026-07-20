<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Store\StoreContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class OperationalPosController extends Controller
{
    public function __invoke(Request $request, StoreContext $storeContext): JsonResponse
    {
        return response()->json([
            'data' => [
                'area' => 'operational',
                'message' => 'POS operational access granted.',
                'user_id' => $request->user()?->id,
                'store_id' => $storeContext->current($request),
                'shift_id' => $request->attributes->get('cash_shift_id'),
            ],
        ]);
    }
}
