<?php

declare(strict_types=1);

namespace App\Http\RefundsReturns\Requests;

use App\Domain\RefundsReturns\ValueObjects\RefundType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreRefundRequest extends FormRequest
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
            'type' => ['required', Rule::enum(RefundType::class)],
            'reason' => ['required', 'string', 'min:3', 'max:500'],
            'lines' => ['sometimes', 'array', 'min:1'],
            'lines.*.sale_line_id' => ['required_with:lines', 'integer', 'min:1'],
            'lines.*.quantity' => ['required_with:lines', 'integer', 'min:1'],
        ];
    }
}
