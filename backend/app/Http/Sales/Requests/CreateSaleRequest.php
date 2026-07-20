<?php

declare(strict_types=1);

namespace App\Http\Sales\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateSaleRequest extends FormRequest
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
            'product_id' => ['sometimes', 'integer', 'exists:products,id'],
            'quantity' => ['required_with:product_id', 'integer', 'min:1'],
        ];
    }
}
