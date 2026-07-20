<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Product;

final class CreateProductAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    /**
     * @param array{
     *     sku: string,
     *     name: string,
     *     base_price: int,
     *     is_active?: bool,
     *     category_id?: int|null
     * } $data
     */
    public function execute(array $data): Product
    {
        if ($this->catalog->skuExists($data['sku'])) {
            throw new CatalogDomainException(ErrorCode::CatSkuDuplicate);
        }

        if (isset($data['category_id']) && $data['category_id'] !== null) {
            $this->assertCategoryExists((int) $data['category_id']);
        }

        return $this->catalog->createProduct($data);
    }

    private function assertCategoryExists(int $categoryId): void
    {
        if ($this->catalog->findCategoryById($categoryId) === null) {
            throw new CatalogDomainException(ErrorCode::CatCategoryNotFound);
        }
    }
}
