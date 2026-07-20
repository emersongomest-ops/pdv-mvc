<?php

declare(strict_types=1);

namespace App\Http\Customers\Controllers;

use App\Application\Customers\Actions\CreateCustomerAction;
use App\Http\Controllers\Controller;
use App\Http\Customers\Requests\StoreCustomerRequest;
use App\Support\Http\CustomerResource;
use Illuminate\Http\JsonResponse;

final class CreateCustomerController extends Controller
{
    public function __invoke(StoreCustomerRequest $request, CreateCustomerAction $action): JsonResponse
    {
        $customer = $action->execute($request->validated());

        return response()->json([
            'data' => [
                'message' => 'Customer created.',
                'customer' => CustomerResource::toArray($customer),
            ],
        ], 201);
    }
}
