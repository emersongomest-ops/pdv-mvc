<?php

declare(strict_types=1);

namespace App\Http\Customers\Controllers;

use App\Application\Customers\Actions\ListCustomersAction;
use App\Http\Controllers\Controller;
use App\Http\Customers\Requests\ListCustomersRequest;
use App\Models\Customer;
use App\Support\Http\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class ListCustomersController extends Controller
{
    public function __invoke(ListCustomersRequest $request, ListCustomersAction $action): JsonResponse
    {
        $validated = $request->validated();
        $search = $validated['search'] ?? null;
        $cursor = $validated['cursor'] ?? null;
        $perPage = $validated['per_page'] ?? null;

        try {
            $result = $action->execute(
                is_string($search) && $search !== '' ? $search : null,
                is_string($cursor) && $cursor !== '' ? $cursor : null,
                $perPage !== null ? (int) $perPage : null,
            );
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'cursor' => ['The cursor is invalid.'],
            ]);
        }

        $payload = [
            'data' => [
                'customers' => $result['customers']
                    ->map(fn (Customer $customer): array => CustomerResource::toArray($customer))
                    ->values()
                    ->all(),
            ],
        ];

        if ($perPage !== null) {
            $payload['meta'] = [
                'next_cursor' => $result['next_cursor'],
            ];
        }

        return response()->json($payload);
    }
}
