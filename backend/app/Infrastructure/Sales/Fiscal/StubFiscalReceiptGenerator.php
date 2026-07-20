<?php

declare(strict_types=1);

namespace App\Infrastructure\Sales\Fiscal;

use App\Domain\Sales\Fiscal\FiscalReceiptGeneratorInterface;
use App\Models\FiscalReceipt;
use App\Models\Sale;

final class StubFiscalReceiptGenerator implements FiscalReceiptGeneratorInterface
{
    public function generateForSale(Sale $sale): FiscalReceipt
    {
        return FiscalReceipt::query()->create([
            'sale_id' => $sale->id,
            'receipt_number' => sprintf('FR-%08d', $sale->id),
            'issued_at' => now(),
        ]);
    }
}
