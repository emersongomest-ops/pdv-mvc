<?php

declare(strict_types=1);

namespace App\Http\Sales\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AttachCustomerToSaleRequest extends FormRequest
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
            'customer_id' => ['required', 'integer', 'min:1'],
        ];
    }
}
