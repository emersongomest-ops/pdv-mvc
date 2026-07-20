<?php

declare(strict_types=1);

namespace App\Http\Catalog\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListProductsRequest extends FormRequest
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
            'category_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
            'search' => ['sometimes', 'nullable', 'string', 'max:120'],
            'cursor' => ['sometimes', 'nullable', 'string', 'max:512'],
            'per_page' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        foreach (['category_id', 'is_active', 'search', 'cursor', 'per_page'] as $key) {
            if ($this->query($key) !== null) {
                $merge[$key] = $this->query($key);
            }
        }
        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
