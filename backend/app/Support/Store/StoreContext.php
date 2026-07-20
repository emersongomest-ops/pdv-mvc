<?php

declare(strict_types=1);

namespace App\Support\Store;

use Illuminate\Http\Request;

final class StoreContext
{
    public const SESSION_KEY = 'store_context_id';

    public function current(Request $request): ?int
    {
        if (! $request->hasSession()) {
            return null;
        }

        $value = $request->session()->get(self::SESSION_KEY);

        if (! is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    public function set(Request $request, int $storeId): void
    {
        if (! $request->hasSession()) {
            throw new \RuntimeException('Session is required to set store context.');
        }

        $request->session()->put(self::SESSION_KEY, $storeId);
    }
}
