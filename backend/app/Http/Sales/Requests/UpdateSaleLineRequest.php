<?php

declare(strict_types=1);

namespace App\Http\Sales\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateSaleLineRequest extends FormRequest
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
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
