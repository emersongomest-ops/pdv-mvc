<?php

declare(strict_types=1);

namespace App\Http\Analytics\Controllers;

use App\Application\Analytics\Actions\ListCampaignCustomersAction;
use App\Http\Analytics\Requests\ListCampaignCustomersRequest;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Support\Http\CustomerResource;
use Illuminate\Http\JsonResponse;

final class ListCampaignCustomersController extends Controller
{
    public function __invoke(
        ListCampaignCustomersRequest $request,
        ListCampaignCustomersAction $action,
    ): JsonResponse {
        $validated = $request->validated();
        $birthMonth = isset($validated['birth_month']) ? (int) $validated['birth_month'] : null;
        $region = isset($validated['region']) && is_string($validated['region'])
            ? trim($validated['region'])
            : null;
        if ($region === '') {
            $region = null;
        }

        $customers = $action->execute($birthMonth, $region);

        return response()->json([
            'data' => [
                'customers' => $customers
                    ->map(fn (Customer $customer): array => CustomerResource::toArray($customer))
                    ->values()
                    ->all(),
            ],
        ]);
    }
}
