<?php

declare(strict_types=1);

namespace App\Http\Analytics\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListCampaignCustomersRequest extends FormRequest
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
            'birth_month' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:12'],
            'region' => ['sometimes', 'nullable', 'string', 'max:120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        foreach (['birth_month', 'region'] as $key) {
            if ($this->query($key) !== null) {
                $merge[$key] = $this->query($key);
            }
        }
        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
