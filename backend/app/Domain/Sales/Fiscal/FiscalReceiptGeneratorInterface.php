<?php

declare(strict_types=1);

namespace App\Domain\Sales\Fiscal;

use App\Models\FiscalReceipt;
use App\Models\Sale;

interface FiscalReceiptGeneratorInterface
{
    public function generateForSale(Sale $sale): FiscalReceipt;
}
