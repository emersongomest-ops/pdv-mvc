<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Product;

final class ShowProductAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    public function execute(int $productId): Product
    {
        $product = $this->catalog->findProductById($productId);

        if ($product === null) {
            throw new CatalogDomainException(ErrorCode::CatProductNotFound);
        }

        return $product;
    }
}
