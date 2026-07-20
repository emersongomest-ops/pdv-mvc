<?php

declare(strict_types=1);

namespace App\Http\Store\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SelectStoreContextRequest extends FormRequest
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
            'store_id' => ['required', 'integer', 'min:1'],
        ];
    }
}
