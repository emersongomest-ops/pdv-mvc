<?php

declare(strict_types=1);

namespace App\Application\Catalog\Actions;

use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Category;

final class CreateCategoryAction
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    public function execute(string $name, bool $isActive = true): Category
    {
        if ($this->catalog->categoryNameExists($name)) {
            throw new CatalogDomainException(ErrorCode::CatCategoryNameDuplicate);
        }

        return $this->catalog->createCategory($name, $isActive);
    }
}
