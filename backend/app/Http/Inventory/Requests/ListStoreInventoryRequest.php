<?php

declare(strict_types=1);

namespace App\Http\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ListStoreInventoryRequest extends FormRequest
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
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->query('store_id') !== null) {
            $this->merge([
                'store_id' => $this->query('store_id'),
            ]);
        }
    }
}
