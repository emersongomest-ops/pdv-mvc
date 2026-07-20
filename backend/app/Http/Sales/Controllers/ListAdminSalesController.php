<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\ListAdminSalesAction;
use App\Domain\Sales\DTOs\AdminSaleFilters;
use App\Http\Controllers\Controller;
use App\Http\Sales\Requests\ListAdminSalesRequest;
use App\Models\Sale;
use App\Models\User;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;

final class ListAdminSalesController extends Controller
{
    public function __invoke(
        ListAdminSalesRequest $request,
        ListAdminSalesAction $action,
    ): JsonResponse {
        $validated = $request->validated();

        $filters = new AdminSaleFilters(
            fromDate: isset($validated['from']) ? (string) $validated['from'] : null,
            toDate: isset($validated['to']) ? (string) $validated['to'] : null,
            storeId: isset($validated['store_id']) ? (int) $validated['store_id'] : null,
            operatorId: isset($validated['operator_id']) ? (int) $validated['operator_id'] : null,
            customerId: isset($validated['customer_id']) ? (int) $validated['customer_id'] : null,
            paymentMethod: isset($validated['payment_method']) ? (string) $validated['payment_method'] : null,
            status: array_key_exists('status', $validated)
                ? ($validated['status'] !== null ? (string) $validated['status'] : null)
                : 'completed',
        );

        /** @var User $manager */
        $manager = $request->user();
        $sales = $action->execute($manager, $filters);

        return response()->json([
            'data' => [
                'sales' => $sales
                    ->map(fn (Sale $sale): array => SaleResource::summaryToArray($sale))
                    ->values()
                    ->all(),
            ],
        ]);
    }
}
