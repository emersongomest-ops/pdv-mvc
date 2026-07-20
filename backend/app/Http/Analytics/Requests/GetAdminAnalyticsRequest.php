<?php

declare(strict_types=1);

namespace App\Http\Analytics\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class GetAdminAnalyticsRequest extends FormRequest
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
            'registration_days' => ['sometimes', 'integer', 'min:1', 'max:90'],
            'top_customers' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        foreach (['registration_days', 'top_customers'] as $key) {
            if ($this->query($key) !== null) {
                $merge[$key] = $this->query($key);
            }
        }
        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
