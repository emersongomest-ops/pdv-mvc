<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\StoreInventoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreInventory extends Model
{
    /** @use HasFactory<StoreInventoryFactory> */
    use HasFactory;
    /**
     * @var list<string>
     */
    protected $fillable = [
        'store_id',
        'product_id',
        'quantity',
        'track_stock',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'track_stock' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Store, $this>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
