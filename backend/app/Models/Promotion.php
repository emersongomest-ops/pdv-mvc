<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Promotions\ValueObjects\DiscountType;
use App\Domain\Promotions\ValueObjects\StackingMode;
use Database\Factories\PromotionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    /** @use HasFactory<PromotionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'stacking_mode',
        'applies_to_all_customers',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'discount_value' => 'integer',
            'stacking_mode' => StackingMode::class,
            'applies_to_all_customers' => 'boolean',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsToMany<Customer, $this>
     */
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class)->withTimestamps();
    }

    /**
     * @return HasMany<SalePromotion, $this>
     */
    public function saleApplications(): HasMany
    {
        return $this->hasMany(SalePromotion::class);
    }

    public function isExpired(?\DateTimeInterface $at = null): bool
    {
        if ($this->ends_at === null) {
            return false;
        }

        $at ??= now();

        return $this->ends_at->lt($at);
    }

    public function isStarted(?\DateTimeInterface $at = null): bool
    {
        if ($this->starts_at === null) {
            return true;
        }

        $at ??= now();

        return $this->starts_at->lte($at);
    }
}
