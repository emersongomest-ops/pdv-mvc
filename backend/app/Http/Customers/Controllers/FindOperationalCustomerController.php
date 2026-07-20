<?php

declare(strict_types=1);

namespace App\Http\Customers\Controllers;

use App\Application\Customers\Actions\FindCustomerByCpfAction;
use App\Http\Controllers\Controller;
use App\Support\Http\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class FindOperationalCustomerController extends Controller
{
    public function __invoke(Request $request, FindCustomerByCpfAction $action): JsonResponse
    {
        $cpf = $request->query('cpf');

        $customer = $action->execute(is_string($cpf) ? $cpf : '');

        return response()->json([
            'data' => [
                'customer' => CustomerResource::toOperationalArray($customer),
            ],
        ]);
    }
}
