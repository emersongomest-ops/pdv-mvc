<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\LogoutUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LogoutController extends Controller
{
    public function __invoke(Request $request, LogoutUserAction $logout): JsonResponse
    {
        $logout->execute($request);

        return response()->json([
            'data' => [
                'logged_out' => true,
            ],
        ]);
    }
}
