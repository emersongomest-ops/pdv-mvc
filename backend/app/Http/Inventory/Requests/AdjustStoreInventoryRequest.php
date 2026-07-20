<?php

declare(strict_types=1);

namespace App\Http\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AdjustStoreInventoryRequest extends FormRequest
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
            'store_id' => ['required', 'integer', Rule::exists('stores', 'id')],
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }
}
