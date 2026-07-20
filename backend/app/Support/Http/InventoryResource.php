<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Models\StoreInventory;

final class InventoryResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(StoreInventory $inventory): array
    {
        $inventory->loadMissing('product');

        return [
            'store_id' => $inventory->store_id,
            'product_id' => $inventory->product_id,
            'product_name' => $inventory->product?->name,
            'product_sku' => $inventory->product?->sku,
            'quantity' => $inventory->quantity,
            'track_stock' => $inventory->track_stock,
        ];
    }
}
