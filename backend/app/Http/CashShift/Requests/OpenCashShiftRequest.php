<?php

declare(strict_types=1);

namespace App\Http\CashShift\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class OpenCashShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'opening_cash_amount' => ['sometimes', 'numeric', 'min:0', 'max:9999999999.99'],
        ];
    }
}
