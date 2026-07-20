<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Shared\ErrorCode;

final class DeleteProductAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    public function execute(int $productId): void
    {
        $product = $this->catalog->findProductById($productId);

        if ($product === null) {
            throw new CatalogDomainException(ErrorCode::CatProductNotFound);
        }

        if ($this->catalog->productHasSaleLines($product->id)) {
            throw new CatalogDomainException(ErrorCode::CatProductInUse);
        }

        $this->catalog->deleteProduct($product);
    }
}
