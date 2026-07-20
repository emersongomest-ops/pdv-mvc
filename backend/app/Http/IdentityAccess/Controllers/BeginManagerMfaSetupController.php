<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\BeginManagerMfaSetupAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class BeginManagerMfaSetupController extends Controller
{
    public function __invoke(Request $request, BeginManagerMfaSetupAction $action): JsonResponse
    {
        $result = $action->execute($request->session());

        return response()->json([
            'data' => $result,
        ]);
    }
}
