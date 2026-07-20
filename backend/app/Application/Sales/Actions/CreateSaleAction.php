<?php

declare(strict_types=1);

namespace App\Application\Sales\Actions;

use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateSaleAction
{
    public function __construct(
        private readonly SalesRepositoryInterface $sales,
        private readonly AddSaleLineAction $addLine,
        private readonly ShowSaleAction $showSale,
    ) {}

    /**
     * Create an in-progress sale. Optionally add the first line in the same transaction
     * so the POS first-item path is a single round-trip.
     */
    public function execute(
        User $user,
        int $storeId,
        int $cashShiftId,
        ?int $productId = null,
        ?int $quantity = null,
    ): Sale {
        return DB::transaction(function () use ($user, $storeId, $cashShiftId, $productId, $quantity): Sale {
            $sale = $this->sales->createInProgress($storeId, $user->id, $cashShiftId);

            if ($productId === null) {
                return $sale;
            }

            $this->addLine->execute(
                (int) $sale->id,
                $productId,
                $quantity ?? 1,
                $storeId,
                (int) $user->id,
                $cashShiftId,
            );

            return $this->showSale->execute(
                (int) $sale->id,
                $storeId,
                (int) $user->id,
                $cashShiftId,
            );
        });
    }
}
