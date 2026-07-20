<?php

declare(strict_types=1);

namespace App\Http\Customers\Controllers;

use App\Application\Customers\Actions\UpdateCustomerAction;
use App\Http\Controllers\Controller;
use App\Http\Customers\Requests\UpdateCustomerRequest;
use App\Support\Http\CustomerResource;
use Illuminate\Http\JsonResponse;

final class UpdateCustomerController extends Controller
{
    public function __invoke(
        UpdateCustomerRequest $request,
        UpdateCustomerAction $action,
        int $customerId,
    ): JsonResponse {
        $customer = $action->execute($customerId, $request->validated());

        return response()->json([
            'data' => [
                'message' => 'Customer updated.',
                'customer' => CustomerResource::toArray($customer),
            ],
        ]);
    }
}
