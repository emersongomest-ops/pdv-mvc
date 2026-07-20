<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Shared\ErrorCode;

final class DeleteCategoryAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    public function execute(int $categoryId): void
    {
        $category = $this->catalog->findCategoryById($categoryId);

        if ($category === null) {
            throw new CatalogDomainException(ErrorCode::CatCategoryNotFound);
        }

        if ($this->catalog->categoryHasProducts($category->id)) {
            throw new CatalogDomainException(ErrorCode::CatCategoryInUse);
        }

        $this->catalog->deleteCategory($category);
    }
}
