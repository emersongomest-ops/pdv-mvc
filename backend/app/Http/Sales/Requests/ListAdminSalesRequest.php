<?php

declare(strict_types=1);

namespace App\Http\Sales\Requests;

use App\Domain\Payments\ValueObjects\PaymentMethod;
use App\Domain\Sales\ValueObjects\SaleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ListAdminSalesRequest extends FormRequest
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
            'from' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'store_id' => ['sometimes', 'nullable', 'integer', Rule::exists('stores', 'id')],
            'operator_id' => ['sometimes', 'nullable', 'integer', Rule::exists('users', 'id')],
            'customer_id' => ['sometimes', 'nullable', 'integer', Rule::exists('customers', 'id')],
            'payment_method' => ['sometimes', 'nullable', 'string', Rule::in(PaymentMethod::values())],
            'status' => ['sometimes', 'nullable', 'string', Rule::in(array_column(SaleStatus::cases(), 'value'))],
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['from', 'to', 'store_id', 'operator_id', 'customer_id', 'payment_method', 'status'] as $key) {
            if ($this->query($key) !== null) {
                $merge[$key] = $this->query($key);
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
