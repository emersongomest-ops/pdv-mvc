<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\ConfirmManagerMfaSetupAction;
use App\Http\Controllers\Controller;
use App\Http\IdentityAccess\Requests\MfaCodeRequest;
use Illuminate\Http\JsonResponse;

final class ConfirmManagerMfaSetupController extends Controller
{
    public function __invoke(MfaCodeRequest $request, ConfirmManagerMfaSetupAction $action): JsonResponse
    {
        $result = $action->execute($request->session(), $request->validated('code'));

        return response()->json([
            'data' => [
                'mfa_required' => false,
                'mfa_setup_required' => false,
                'recovery_codes' => $result['recovery_codes'],
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'role' => $result['user']->role->value,
                ],
            ],
        ]);
    }
}
