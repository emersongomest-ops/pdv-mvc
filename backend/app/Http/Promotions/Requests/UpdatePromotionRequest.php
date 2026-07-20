<?php

declare(strict_types=1);

namespace App\Http\Promotions\Requests;

use App\Domain\Promotions\ValueObjects\DiscountType;
use App\Domain\Promotions\ValueObjects\StackingMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdatePromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['sometimes', 'string', 'max:64'],
            'name' => ['sometimes', 'string', 'max:255'],
            'discount_type' => ['sometimes', Rule::enum(DiscountType::class)],
            'discount_value' => ['sometimes', 'numeric', 'min:0'],
            'stacking_mode' => ['sometimes', Rule::enum(StackingMode::class)],
            'applies_to_all_customers' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'customer_ids' => ['sometimes', 'array'],
            'customer_ids.*' => ['integer', 'min:1'],
        ];
    }
}
