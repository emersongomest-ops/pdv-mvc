<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Category;

final class ShowCategoryAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    public function execute(int $categoryId): Category
    {
        $category = $this->catalog->findCategoryById($categoryId);

        if ($category === null) {
            throw new CatalogDomainException(ErrorCode::CatCategoryNotFound);
        }

        return $category;
    }
}
