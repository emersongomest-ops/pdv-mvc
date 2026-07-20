<?php

declare(strict_types=1);

namespace App\Http\Audit\Requests;

use App\Domain\Audit\ValueObjects\AuditAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ListAdminAuditLogsRequest extends FormRequest
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
            'action' => ['sometimes', 'nullable', 'string', Rule::in(AuditAction::values())],
            'actor_id' => ['sometimes', 'nullable', 'integer', Rule::exists('users', 'id')],
            'store_id' => ['sometimes', 'nullable', 'integer', Rule::exists('stores', 'id')],
            'subject_type' => ['sometimes', 'nullable', 'string', Rule::in(['product', 'refund', 'promotion', 'cash_shift', 'user'])],
            'subject_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'cursor' => ['sometimes', 'nullable', 'string', 'max:512'],
            'per_page' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach ([
            'from',
            'to',
            'action',
            'actor_id',
            'store_id',
            'subject_type',
            'subject_id',
            'cursor',
            'per_page',
        ] as $key) {
            if ($this->query($key) !== null) {
                $merge[$key] = $this->query($key);
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
