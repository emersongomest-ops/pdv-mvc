<?php

declare(strict_types=1);

namespace App\Http\Sales\Requests;

use App\Domain\Payments\ValueObjects\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class CompleteSaleRequest extends FormRequest
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
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', 'string', Rule::in(PaymentMethod::values())],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.cash_received' => ['nullable', 'numeric', 'min:0.01'],
            'payments.*.card' => ['sometimes', 'array'],
            'payments.*.card.holder_name' => ['required_with:payments.*.card', 'string', 'max:120'],
            'payments.*.card.number' => ['required_with:payments.*.card', 'string', 'max:32'],
            'payments.*.card.exp_month' => ['required_with:payments.*.card', 'integer', 'between:1,12'],
            'payments.*.card.exp_year' => ['required_with:payments.*.card', 'integer', 'min:2000', 'max:2100'],
            'payments.*.card.indicated_person_name' => ['required_with:payments.*.card', 'string', 'max:120'],
            'payments.*.card.belongs_to_indicated_person' => ['required_with:payments.*.card', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var list<array<string, mixed>> $payments */
            $payments = $this->input('payments', []);

            foreach ($payments as $index => $payment) {
                if (! is_array($payment)) {
                    continue;
                }

                $method = PaymentMethod::tryFrom((string) ($payment['method'] ?? ''));

                if ($method !== PaymentMethod::DebitCard && $method !== PaymentMethod::CreditCard) {
                    continue;
                }

                if (! isset($payment['card']) || ! is_array($payment['card'])) {
                    $validator->errors()->add(
                        "payments.{$index}.card",
                        'Card details are required for card payments.',
                    );
                }
            }
        });
    }
}
