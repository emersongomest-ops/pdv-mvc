<?php

declare(strict_types=1);

namespace App\Http\Customers\Controllers;

use App\Application\Customers\Actions\ShowCustomerAction;
use App\Http\Controllers\Controller;
use App\Support\Http\CustomerResource;
use Illuminate\Http\JsonResponse;

final class ShowCustomerController extends Controller
{
    public function __invoke(ShowCustomerAction $action, int $customerId): JsonResponse
    {
        $customer = $action->execute($customerId);

        return response()->json([
            'data' => [
                'customer' => CustomerResource::toArray($customer),
            ],
        ]);
    }
}
